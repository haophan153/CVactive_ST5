<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MoMoService
{
    private string $partnerCode;
    private string $accessKey;
    private string $secretKey;
    private string $endpoint;
    private string $returnUrl;
    private string $notifyUrl;

    public function __construct()
    {
        $this->partnerCode = config('momo.partner_code');
        $this->accessKey   = config('momo.access_key');
        $this->secretKey   = config('momo.secret_key');
        $this->endpoint    = config('momo.endpoint');
        $this->returnUrl   = config('momo.return_url');
        $this->notifyUrl   = config('momo.notify_url');
    }

    /**
     * Tạo URL thanh toán MoMo
     *
     * @param  string $orderId  Pending token (UUID) — không phải payment ID.
     *                          MoMo chấp nhận string cho orderId.
     */
    public function createPaymentUrl(string $orderId, int $amount, string $orderDesc): array
    {
        $requestId  = $this->partnerCode . now()->timestamp . Str::random(4);
        $orderInfo  = $orderDesc;
        $requestType = 'captureWallet';
        // Lưu pending token vào extraData để callback round-trip lấy lại được.
        $extraData  = base64_encode(json_encode(['pending_token' => $orderId]));

        $rawHash = "accessKey={$this->accessKey}"
            . "&amount={$amount}"
            . "&extraData={$extraData}"
            . "&ipnUrl={$this->notifyUrl}"
            . "&orderId={$orderId}"
            . "&orderInfo={$orderInfo}"
            . "&partnerCode={$this->partnerCode}"
            . "&redirectUrl={$this->returnUrl}"
            . "&requestId={$requestId}"
            . "&requestType={$requestType}";

        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        $payload = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => 'CVactive',
            'storeId'     => 'CVactive',
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $this->returnUrl,
            'ipnUrl'      => $this->notifyUrl,
            'lang'        => 'vi',
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature,
        ];

        $response = Http::post($this->endpoint . '/v2/gateway/api/create', $payload);

        return $response->json();
    }

    /**
     * Xác minh chữ ký từ MoMo callback.
     *
     * C1: Trả về false nếu thiếu field — tránh PHP warning "Undefined index"
     * lộ stack trace trong production khi MoMo gửi response không đầy đủ.
     */
    public function verifyCallback(array $data): bool
    {
        $required = [
            'amount', 'extraData', 'message', 'orderId', 'orderInfo',
            'orderType', 'partnerCode', 'payType', 'requestId',
            'responseTime', 'resultCode', 'transId',
        ];
        foreach ($required as $key) {
            if (!array_key_exists($key, $data)) {
                return false;
            }
        }

        $rawHash = "accessKey={$this->accessKey}"
            . "&amount={$data['amount']}"
            . "&extraData={$data['extraData']}"
            . "&message={$data['message']}"
            . "&orderId={$data['orderId']}"
            . "&orderInfo={$data['orderInfo']}"
            . "&orderType={$data['orderType']}"
            . "&partnerCode={$data['partnerCode']}"
            . "&payType={$data['payType']}"
            . "&requestId={$data['requestId']}"
            . "&responseTime={$data['responseTime']}"
            . "&resultCode={$data['resultCode']}"
            . "&transId={$data['transId']}";

        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        return hash_equals($signature, (string)($data['signature'] ?? ''));
    }

    public function isSuccess(array $data): bool
    {
        return $this->verifyCallback($data) && (int)($data['resultCode'] ?? -1) === 0;
    }
}
