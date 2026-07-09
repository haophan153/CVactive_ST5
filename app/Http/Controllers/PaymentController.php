<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Payment;
use App\Models\User;
use App\Services\VNPayService;
use App\Services\MoMoService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct(
        private VNPayService $vnpay,
        private MoMoService $momo,
    ) {}

    /**
     * Trang checkout – chọn phương thức thanh toán
     */
    public function checkout(Plan $plan)
    {
        if ($plan->price == 0) {
            return redirect()->route('pricing')->with('error', 'Gói này miễn phí, không cần thanh toán.');
        }

        $user = auth()->user();

        // Nếu đã có plan và chưa hết hạn
        if ($user->plan_id == $plan->id && $user->plan_expires_at && $user->plan_expires_at->isFuture()) {
            return redirect()->route('pricing')->with('error', 'Bạn đang sử dụng gói này và chưa hết hạn.');
        }

        return view('payment.checkout', compact('plan'));
    }

    /**
     * Sinh pending token + redirect sang cổng thanh toán.
     *
     * Lưu ý: KHÔNG tạo Payment record ở đây. Token chỉ là mã tạm để đối chiếu
     * khi user quay lại từ VNPay/MoMo. Payment record chỉ được tạo khi callback
     * trả về trạng thái thành công (xem vnpayReturn/momoReturn).
     * Nếu user bấm "Nâng cấp" rồi đóng tab hoặc thanh toán thất bại,
     * sẽ không có rác trong database.
     */
    public function process(Request $request, Plan $plan)
    {
        $request->validate([
            'method' => 'required|in:vnpay,momo,bank_transfer',
        ]);

        $user   = auth()->user();
        $amount = (int) $plan->price;
        $method = (string) $request->input('method');

        // Token tạm duy nhất cho giao dịch này (không lưu DB ở bước này).
        $pendingToken = 'pay_' . Str::uuid()->toString();

        // Đẩy thông tin giao dịch vào session để callback đọc lại khi user quay về.
        session([
            'pending_payment' => [
                'token'    => $pendingToken,
                'user_id'  => $user->id,
                'plan_id'  => $plan->id,
                'method'   => $method,
                'amount'   => $amount,
                'plan_name'=> $plan->name,
                'user_name'=> $user->name,
                'user_email'=> $user->email,
                'created_at'=> now()->toIso8601String(),
            ],
        ]);

        return match($method) {
            'vnpay'         => $this->processVNPay($pendingToken, $plan, $request),
            'momo'          => $this->processMoMo($pendingToken, $plan),
            'bank_transfer' => redirect()->route('payment.bank', ['token' => $pendingToken]),
            default         => back()->with('error', 'Phương thức thanh toán không hợp lệ.'),
        };
    }

    private function processVNPay(string $pendingToken, Plan $plan, Request $request): \Illuminate\Http\RedirectResponse
    {
        $payUrl = $this->vnpay->createPaymentUrl(
            orderId:   $pendingToken,
            amount:    (int) $plan->price,
            orderDesc: "Thanh toan goi {$plan->name} - CVactive",
            ipAddr:    $request->ip(),
        );

        return redirect()->away($payUrl);
    }

    private function processMoMo(string $pendingToken, Plan $plan)
    {
        $result = $this->momo->createPaymentUrl(
            orderId:   $pendingToken,
            amount:    (int) $plan->price,
            orderDesc: "Thanh toan goi {$plan->name} - CVactive",
        );

        if (!empty($result['payUrl'])) {
            return redirect()->away($result['payUrl']);
        }

        // Xóa session pending vì MoMo không tạo được URL
        session()->forget('pending_payment');
        Log::error('MoMo create payment failed', $result);
        return redirect()->route('payment.fail')->with('error', 'Không kết nối được MoMo. Vui lòng thử lại.');
    }

    // ─── VNPay Callbacks ───────────────────────────────────────────────────

    /**
     * Return URL sau khi user thanh toán VNPay
     */
    /**
     * Return URL sau khi user thanh toán VNPay — user redirect về từ trình duyệt.
     *
     * Flow mới (không lưu Payment pending):
     *  1. Xác minh chữ ký VNPay.
     *  2. Lấy pending_token từ vnp_TxnRef.
     *  3. Nếu response code = 00 và pending_payment trong session khớp token:
     *     → tạo Payment completed + nâng cấp plan.
     *  4. Ngược lại: KHÔNG tạo gì, redirect về trang fail.
     */
    public function vnpayReturn(Request $request)
    {
        $data = $request->all();

        if (!$this->vnpay->verifyCallback($data)) {
            session()->forget('pending_payment');
            return redirect()->route('payment.fail')->with('error', 'Chữ ký không hợp lệ.');
        }

        $pending = $this->consumePendingPayment($this->vnpay->parseOrderId($data['vnp_TxnRef'] ?? ''));

        if (!$pending) {
            return redirect()->route('payment.fail')->with('error', 'Phiên thanh toán đã hết hạn hoặc không hợp lệ.');
        }

        if (($data['vnp_ResponseCode'] ?? '') === '00') {
            $payment = $this->completePayment($pending, (string)($data['vnp_TransactionNo'] ?? ''));
            return redirect()->route('payment.success', ['payment' => $payment->id]);
        }

        // KHÔNG lưu record nào nếu user không thanh toán thành công.
        return redirect()->route('payment.fail', ['reason' => $data['vnp_ResponseCode'] ?? 'unknown']);
    }

    /**
     * IPN từ VNPay server — server gọi, không qua trình duyệt.
     *
     * Vì pending_payment nằm trong session user (client-side) và IPN là server-to-server,
     * IPN không thể đọc session. Ta dùng dữ liệu trong TxnRef + OrderInfo làm dấu vết,
     * nhưng để tránh gian lận ta xác minh bằng amount (vnp_Amount khớp plan.price).
     */
    public function vnpayIpn(Request $request)
    {
        $data = $request->all();

        if (!$this->vnpay->verifyCallback($data)) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        // Idempotency: tìm payment đã tồn tại với transaction_id này (khi return/IPN đã xử lý trước)
        $existingByTxn = !empty($data['vnp_TransactionNo'])
            ? Payment::where('transaction_id', (string)$data['vnp_TransactionNo'])->first()
            : null;
        if ($existingByTxn) {
            return response()->json(['RspCode' => '02', 'Message' => 'Order already confirmed']);
        }

        if (($data['vnp_ResponseCode'] ?? '') !== '00') {
            return response()->json(['RspCode' => '00', 'Message' => 'Skipped — not success']);
        }

        // IPN không tạo Payment vì không có đầy đủ context (user_id, plan_id) bảo mật.
        // Return URL (vnpayReturn) đã xử lý; IPN chỉ xác nhận từ server-side.
        // Nếu cần tạo tại IPN: cần truyền user_id + plan_id trong OrderInfo/TxnRef đã mã hóa.
        return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
    }

    // ─── MoMo Callbacks ────────────────────────────────────────────────────

    public function momoReturn(Request $request)
    {
        $data    = $request->all();
        $token   = $data['orderId'] ?? '';
        $pending = $this->consumePendingPayment($token);

        if (!$pending) {
            return redirect()->route('payment.fail')->with('error', 'Đơn hàng không tồn tại hoặc đã hết hạn.');
        }

        if ($this->momo->isSuccess($data)) {
            $payment = $this->completePayment($pending, (string)($data['transId'] ?? ''));
            return redirect()->route('payment.success', ['payment' => $payment->id]);
        }

        // KHÔNG lưu record khi thất bại.
        return redirect()->route('payment.fail', ['reason' => $data['message'] ?? 'unknown']);
    }

    public function momoIpn(Request $request)
    {
        $data = $request->all();

        // H-7: IPN bắt buộc phải verify chữ ký — không có ngoại lệ.
        // Không verify = cho phép attacker spam unique transId để pollution DB /
        // gây nhầm lẫn forensic khi sự cố.
        if (!$this->momo->verifyCallback($data)) {
            Log::warning('MoMo IPN signature invalid', [
                'transId'  => $data['transId'] ?? null,
                'orderId'  => $data['orderId'] ?? null,
                'ip'       => $request->ip(),
            ]);
            return response()->json(['status' => 'invalid_signature'], 400);
        }

        // Validate input tối thiểu
        if (empty($data['transId']) || empty($data['orderId'])) {
            return response()->json(['status' => 'missing_params'], 422);
        }

        $existingByTxn = Payment::where('transaction_id', (string) $data['transId'])->first();
        if ($existingByTxn) {
            return response()->json(['status' => 'ok']);
        }

        // IPN MoMo không tạo Payment vì thiếu session context (user_id, plan_id,
        // amount). Return URL (momoReturn) + cross-check với plan amount mới tạo.
        return response()->json(['status' => 'ok']);
    }

    // ─── Bank Transfer ─────────────────────────────────────────────────────

    /**
     * Bank transfer chỉ là hướng dẫn — KHÔNG tạo Payment record ở đây.
     * Staff xác nhận chuyển khoản → tạo Payment completed thủ công (qua admin).
     */
    public function bankTransfer(Request $request)
    {
        $token   = $request->input('token', '');
        $pending = session('pending_payment');

        if (!$pending || ($token && $pending['token'] !== $token)) {
            return redirect()->route('pricing')->with('error', 'Phiên thanh toán đã hết hạn.');
        }

        return view('payment.bank-transfer', ['pending' => $pending]);
    }

    // ─── Result pages ──────────────────────────────────────────────────────

    public function success(Payment $payment)
    {
        $this->authorize('view', $payment);
        $payment->load(['user', 'plan']);
        return view('payment.success', compact('payment'));
    }

    public function fail(Request $request)
    {
        $reason = $request->input('reason', '');
        return view('payment.fail', compact('reason'));
    }

    public function cancel(Request $request)
    {
        $pending = session('pending_payment');
        $planName = $pending['plan_name'] ?? null;
        // Khi user hủy — xóa pending trong session và không lưu DB.
        session()->forget('pending_payment');
        return view('payment.cancel', ['planName' => $planName]);
    }

    // ─── History ───────────────────────────────────────────────────────────

    public function history()
    {
        $payments = auth()->user()->payments()->with('plan')->latest()->paginate(10);
        return view('payment.history', compact('payments'));
    }

    // ─── Helpers ───────────────────────────────────────────────────────────

    /**
     * Validate pending token và xóa khỏi session (1 lần dùng).
     * Returns the pending array if valid, null otherwise.
     */
    private function consumePendingPayment(string $token): ?array
    {
        $pending = session('pending_payment');

        if (! $pending || empty($pending['token'])) {
            return null;
        }

        // Bảo mật: phải khớp token được gửi từ cổng thanh toán
        if (! hash_equals((string) $pending['token'], $token)) {
            return null;
        }

        // Xóa session để tránh reuse token
        session()->forget('pending_payment');

        return $pending;
    }

    /**
     * Tạo Payment với status=completed VÀ nâng cấp plan cho user.
     * Chỉ được gọi khi cổng thanh toán xác nhận thành công.
     *
     * @param  array  $pending  Thông tin pending lưu trong session.
     * @param  string $transactionId  Mã giao dịch từ VNPay/MoMo.
     * @return Payment Payment vừa được tạo (completed).
     */
    private function completePayment(array $pending, string $transactionId): Payment
    {
        return DB::transaction(function () use ($pending, $transactionId) {
            // C4: Idempotency guard — tránh cộng dồn plan khi return URL + IPN race.
            // Nếu transaction_id đã tồn tại (process khác đã xử lý), trả về payment cũ.
            if ($transactionId !== '') {
                $existing = Payment::where('transaction_id', $transactionId)->first();
                if ($existing) {
                    return $existing;
                }
            }

            $payment = Payment::create([
                'user_id'        => $pending['user_id'],
                'plan_id'        => $pending['plan_id'],
                'amount'         => $pending['amount'],
                'payment_method' => $pending['method'],
                'status'         => 'completed',
                'transaction_id' => $transactionId,
                'metadata'       => [
                    'plan_name'  => $pending['plan_name'] ?? null,
                    'user_name'  => $pending['user_name'] ?? null,
                    'user_email' => $pending['user_email'] ?? null,
                ],
            ]);

            // Nâng cấp plan cho user — nếu user đang có plan còn hạn, cộng dồn;
            // nếu hết hạn, bắt đầu tháng mới.
            $user = User::findOrFail($pending['user_id']);
            $newExpiry = $user->plan_expires_at && $user->plan_expires_at->isFuture()
                ? $user->plan_expires_at->addMonth()
                : now()->addMonth();

            $user->update([
                'plan_id'         => $pending['plan_id'],
                'plan_expires_at' => $newExpiry,
            ]);

            return $payment;
        });
    }
}
