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
     */
    public function createPaymentUrl(int $orderId, int $amount, string $orderDesc): array
    {
        $requestId  = $this->partnerCode . now()->timestamp . Str::random(4);
        $orderInfo  = $orderDesc;
        $requestType = 'captureWallet';
        $extraData  = base64_encode(json_encode(['payment_id' => $orderId]));

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
            'orderId'     => (string) $orderId,
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
     * Xác minh chữ ký từ MoMo callback
     */
    public function verifyCallback(array $data): bool
    {
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

        return hash_equals($signature, $data['signature'] ?? '');
    }

    public function isSuccess(array $data): bool
    {
        return $this->verifyCallback($data) && (int)($data['resultCode'] ?? -1) === 0;
    }
}
