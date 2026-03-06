<?php

namespace App\Services;

use Illuminate\Http\Request;

class VNPayService
{
    private string $tmnCode;
    private string $hashSecret;
    private string $url;
    private string $returnUrl;

    public function __construct()
    {
        $this->tmnCode   = config('vnpay.tmn_code');
        $this->hashSecret = config('vnpay.hash_secret');
        $this->url       = config('vnpay.url');
        $this->returnUrl = config('vnpay.return_url');
    }

    /**
     * Tạo URL thanh toán VNPay
     */
    public function createPaymentUrl(int $orderId, int $amount, string $orderDesc, string $ipAddr): string
    {
        $vnpParams = [
            'vnp_Version'    => '2.1.0',
            'vnp_Command'    => 'pay',
            'vnp_TmnCode'    => $this->tmnCode,
            'vnp_Amount'     => $amount * 100, // VNPay nhận đơn vị VND * 100
            'vnp_CurrCode'   => 'VND',
            'vnp_TxnRef'     => $orderId . '_' . time(),
            'vnp_OrderInfo'  => $orderDesc,
            'vnp_OrderType'  => 'other',
            'vnp_Locale'     => 'vn',
            'vnp_ReturnUrl'  => $this->returnUrl,
            'vnp_IpAddr'     => $ipAddr,
            'vnp_CreateDate' => now()->format('YmdHis'),
            'vnp_ExpireDate' => now()->addMinutes(15)->format('YmdHis'),
        ];

        ksort($vnpParams);

        $queryString = http_build_query($vnpParams);
        $hmac        = hash_hmac('sha512', $queryString, $this->hashSecret);

        return $this->url . '?' . $queryString . '&vnp_SecureHash=' . $hmac;
    }

    /**
     * Xác minh chữ ký từ VNPay callback
     */
    public function verifyCallback(array $data): bool
    {
        $secureHash = $data['vnp_SecureHash'] ?? '';
        unset($data['vnp_SecureHash'], $data['vnp_SecureHashType']);

        ksort($data);
        $queryString = http_build_query($data);
        $hmac        = hash_hmac('sha512', $queryString, $this->hashSecret);

        return hash_equals($hmac, $secureHash);
    }

    /**
     * Kiểm tra trạng thái giao dịch
     */
    public function isSuccess(array $data): bool
    {
        return $this->verifyCallback($data) && ($data['vnp_ResponseCode'] ?? '') === '00';
    }

    /**
     * Lấy order ID từ TxnRef
     */
    public function parseOrderId(string $txnRef): int
    {
        return (int) explode('_', $txnRef)[0];
    }
}
