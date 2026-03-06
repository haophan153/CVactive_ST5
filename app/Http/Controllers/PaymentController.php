<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Payment;
use App\Services\VNPayService;
use App\Services\MoMoService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     * Tạo đơn hàng và redirect sang cổng thanh toán
     */
    public function process(Request $request, Plan $plan)
    {
        $request->validate([
            'method' => 'required|in:vnpay,momo,bank_transfer',
        ]);

        $user   = auth()->user();
        $amount = (int) $plan->price;

        // Tạo payment record với trạng thái pending
        $payment = Payment::create([
            'user_id'        => $user->id,
            'plan_id'        => $plan->id,
            'amount'         => $amount,
            'payment_method' => $request->method,
            'status'         => 'pending',
            'metadata'       => [
                'plan_name' => $plan->name,
                'user_name' => $user->name,
                'user_email' => $user->email,
            ],
        ]);

        return match($request->method) {
            'vnpay'         => $this->processVNPay($payment, $plan, $request),
            'momo'          => $this->processMoMo($payment, $plan),
            'bank_transfer' => $this->processBankTransfer($payment, $plan),
            default         => back()->with('error', 'Phương thức thanh toán không hợp lệ.'),
        };
    }

    private function processVNPay(Payment $payment, Plan $plan, Request $request): \Illuminate\Http\RedirectResponse
    {
        $payUrl = $this->vnpay->createPaymentUrl(
            orderId:   $payment->id,
            amount:    (int) $payment->amount,
            orderDesc: "Thanh toan goi {$plan->name} - CVactive",
            ipAddr:    $request->ip(),
        );

        return redirect()->away($payUrl);
    }

    private function processMoMo(Payment $payment, Plan $plan)
    {
        $result = $this->momo->createPaymentUrl(
            orderId:   $payment->id,
            amount:    (int) $payment->amount,
            orderDesc: "Thanh toan goi {$plan->name} - CVactive",
        );

        if (!empty($result['payUrl'])) {
            return redirect()->away($result['payUrl']);
        }

        $payment->update(['status' => 'failed']);
        Log::error('MoMo create payment failed', $result);
        return redirect()->route('payment.cancel', $payment)->with('error', 'Không kết nối được MoMo. Vui lòng thử lại.');
    }

    private function processBankTransfer(Payment $payment, Plan $plan): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('payment.bank', $payment);
    }

    // ─── VNPay Callbacks ───────────────────────────────────────────────────

    /**
     * Return URL sau khi user thanh toán VNPay
     */
    public function vnpayReturn(Request $request)
    {
        $data = $request->all();

        if (!$this->vnpay->verifyCallback($data)) {
            return redirect()->route('payment.fail')->with('error', 'Chữ ký không hợp lệ.');
        }

        $paymentId = $this->vnpay->parseOrderId($data['vnp_TxnRef']);
        $payment   = Payment::with(['user', 'plan'])->findOrFail($paymentId);

        if ($data['vnp_ResponseCode'] === '00') {
            $this->completePayment($payment, $data['vnp_TransactionNo']);
            return redirect()->route('payment.success', $payment);
        }

        $payment->update(['status' => 'failed']);
        return redirect()->route('payment.fail', ['reason' => $data['vnp_ResponseCode']]);
    }

    /**
     * IPN từ VNPay server (xác minh server-side)
     */
    public function vnpayIpn(Request $request)
    {
        $data = $request->all();

        if (!$this->vnpay->verifyCallback($data)) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        $paymentId = $this->vnpay->parseOrderId($data['vnp_TxnRef']);
        $payment   = Payment::find($paymentId);

        if (!$payment) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
        }

        if ($payment->status === 'completed') {
            return response()->json(['RspCode' => '02', 'Message' => 'Order already confirmed']);
        }

        if ($data['vnp_ResponseCode'] === '00') {
            $this->completePayment($payment, $data['vnp_TransactionNo']);
        } else {
            $payment->update(['status' => 'failed']);
        }

        return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
    }

    // ─── MoMo Callbacks ────────────────────────────────────────────────────

    public function momoReturn(Request $request)
    {
        $data = $request->all();

        $payment = Payment::with(['user', 'plan'])->find($data['orderId'] ?? 0);

        if (!$payment) {
            return redirect()->route('payment.fail')->with('error', 'Đơn hàng không tồn tại.');
        }

        if ($this->momo->isSuccess($data)) {
            $this->completePayment($payment, (string)($data['transId'] ?? ''));
            return redirect()->route('payment.success', $payment);
        }

        $payment->update(['status' => 'failed']);
        return redirect()->route('payment.fail', ['reason' => $data['message'] ?? '']);
    }

    public function momoIpn(Request $request)
    {
        $data    = $request->all();
        $payment = Payment::find($data['orderId'] ?? 0);

        if ($payment && $this->momo->isSuccess($data)) {
            $this->completePayment($payment, (string)($data['transId'] ?? ''));
        }

        return response()->json(['status' => 'ok']);
    }

    // ─── Bank Transfer ─────────────────────────────────────────────────────

    public function bankTransfer(Payment $payment)
    {
        $this->authorize('view', $payment);
        return view('payment.bank-transfer', compact('payment'));
    }

    // ─── Result pages ──────────────────────────────────────────────────────

    public function success(Payment $payment)
    {
        $payment->load(['user', 'plan']);
        return view('payment.success', compact('payment'));
    }

    public function fail(Request $request)
    {
        $reason = $request->get('reason', '');
        return view('payment.fail', compact('reason'));
    }

    public function cancel(Payment $payment)
    {
        $payment->load('plan');
        return view('payment.cancel', compact('payment'));
    }

    // ─── History ───────────────────────────────────────────────────────────

    public function history()
    {
        $payments = auth()->user()->payments()->with('plan')->latest()->paginate(10);
        return view('payment.history', compact('payments'));
    }

    // ─── Helpers ───────────────────────────────────────────────────────────

    private function completePayment(Payment $payment, string $transactionId): void
    {
        DB::transaction(function () use ($payment, $transactionId) {
            $payment->update([
                'status'         => 'completed',
                'transaction_id' => $transactionId,
            ]);

            // Nâng cấp plan cho user
            $plan = $payment->plan;
            $payment->user->update([
                'plan_id'        => $plan->id,
                'plan_expires_at' => now()->addMonth(),
            ]);
        });
    }
}
