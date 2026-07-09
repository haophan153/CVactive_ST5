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
     *
     * @param  string $orderId  Pending token (UUID) từ session — không phải payment ID.
     *                          VNPay không yêu cầu số, chỉ yêu cầu duy nhất.
     */
    public function createPaymentUrl(string $orderId, int $amount, string $orderDesc, string $ipAddr): string
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
     * Xác minh chữ ký từ VNPay callback.
     *
     * C1: Validate input — tránh PHP warning lộ stack trace khi response thiếu field.
     */
    public function verifyCallback(array $data): bool
    {
        $secureHash = $data['vnp_SecureHash'] ?? '';
        if ($secureHash === '') {
            return false;
        }

        unset($data['vnp_SecureHash'], $data['vnp_SecureHashType']);

        // Lọc bỏ các field không tham gia ký (an toàn với response lạ)
        $allowed = [];
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'vnp_')) {
                $allowed[$key] = $value;
            }
        }

        ksort($allowed);
        $queryString = http_build_query($allowed);
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
     * Lấy order token (UUID) từ TxnRef.
     * Trả về string vì giờ pending token là UUID, không phải payment ID số.
     */
    public function parseOrderId(string $txnRef): string
    {
        return explode('_', $txnRef)[0];
    }
}
