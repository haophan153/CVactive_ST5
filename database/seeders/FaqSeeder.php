<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        Faq::query()->delete();

        $samples = [
            ['category' => 'cv', 'sort_order' => 1, 'views_count' => 1240,
                'question' => 'Làm thế nào để tạo một CV chuyên nghiệp?',
                'answer' => "Bạn có thể tạo CV chỉ trong 5 phút với 3 bước đơn giản:\n\n1. Chọn mẫu CV phù hợp với ngành nghề\n2. Điền thông tin cá nhân, học vấn, kinh nghiệm\n3. Xuất PDF hoặc chia sẻ link trực tuyến\n\nMẹo: Dùng các gạch đầu dòng ngắn gọn, tránh đoạn văn dài, và chỉ liệt kê những thông tin liên quan đến vị trí ứng tuyển."],

            ['category' => 'cv', 'sort_order' => 2, 'views_count' => 980,
                'question' => 'CVactive có những mẫu CV nào?',
                'answer' => "Hiện tại CVactive cung cấp hơn 50 mẫu CV chuyên nghiệp được thiết kế bởi các chuyên gia HR, được phân loại theo ngành nghề: Công nghệ, Marketing, Kinh tế, Sáng tạo, Giáo dục... Mỗi mẫu đều có phiên bản miễn phí và bản Premium với thiết kế tinh tế hơn."],

            ['category' => 'cv', 'sort_order' => 3, 'views_count' => 760,
                'question' => 'Tôi có thể tuỳ chỉnh màu sắc và font chữ không?',
                'answer' => "Có! Với gói Pro, bạn có thể tuỳ chỉnh toàn bộ:\n- Màu sắc chủ đạo\n- Font chữ (hơn 20 lựa chọn)\n- Bố cục các section\n- Icon và đường viền\n\nTất cả thay đổi được preview theo thời gian thực."],

            ['category' => 'account', 'sort_order' => 4, 'views_count' => 540,
                'question' => 'Làm sao để đăng ký tài khoản?',
                'answer' => "Bạn có thể đăng ký miễn phí chỉ với email:\n1. Nhấp vào nút \"Dùng miễn phí\" ở góc trên\n2. Nhập họ tên, email và mật khẩu\n3. Xác nhận email qua link được gửi\n\nNgoài ra bạn cũng có thể đăng nhập nhanh bằng tài khoản Google."],

            ['category' => 'account', 'sort_order' => 5, 'views_count' => 410,
                'question' => 'Tôi quên mật khẩu phải làm sao?',
                'answer' => "Tại trang đăng nhập, nhấp vào \"Quên mật khẩu\". Nhập email đã đăng ký, hệ thống sẽ gửi link đặt lại mật khẩu trong vòng 1-2 phút. Kiểm tra cả thư mục spam nếu không thấy email."],

            ['category' => 'account', 'sort_order' => 6, 'views_count' => 230,
                'question' => 'Làm sao để đổi email tài khoản?',
                'answer' => "Vào Dashboard → Hồ sơ của tôi → Cài đặt tài khoản → Đổi email. Bạn cần xác nhận email mới qua đường link được gửi. Sau khi xác nhận, mọi thông báo sẽ chuyển sang email mới."],

            ['category' => 'payment', 'sort_order' => 7, 'views_count' => 890,
                'question' => 'Các hình thức thanh toán nào được hỗ trợ?',
                'answer' => "CVactive hỗ trợ nhiều hình thức thanh toán:\n- Thẻ tín dụng/ghi nợ quốc tế (Visa, MasterCard)\n- Ví MoMo, ZaloPay, VNPay\n- Chuyển khoản ngân hàng nội địa\n- QR code\n\nTất cả giao dịch đều được mã hoá và bảo mật."],

            ['category' => 'payment', 'sort_order' => 8, 'views_count' => 720,
                'question' => 'Gói Pro có những tính năng gì?',
                'answer' => "Gói Pro (99.000đ/tháng) bao gồm:\n- Tạo không giới hạn CV\n- Truy cập toàn bộ 50+ mẫu Premium\n- Xuất PDF & PNG chất lượng cao\n- Tuỳ chỉnh màu sắc, font chữ\n- Xoá watermark\n- Hỗ trợ ưu tiên 24/7"],

            ['category' => 'payment', 'sort_order' => 9, 'views_count' => 380,
                'question' => 'Tôi có được hoàn tiền không?',
                'answer' => "Có. CVactive hỗ trợ hoàn tiền 100% trong vòng 7 ngày đầu nếu bạn không hài lòng. Liên hệ support@cvactive.vn với mã đơn hàng, đội ngũ sẽ xử lý trong 24 giờ."],

            ['category' => 'payment', 'sort_order' => 10, 'views_count' => 290,
                'question' => 'Làm sao để huỷ gói Pro?',
                'answer' => "Bạn có thể huỷ bất kỳ lúc nào:\n1. Vào Dashboard → Lịch sử thanh toán\n2. Chọn gói đang dùng → Huỷ\n\nBạn vẫn sử dụng được đến hết chu kỳ đã thanh toán, sau đó tự động chuyển về gói miễn phí."],

            ['category' => 'job', 'sort_order' => 11, 'views_count' => 650,
                'question' => 'Làm sao để ứng tuyển việc làm?',
                'answer' => "Sau khi tạo CV, vào mục \"Việc làm\" → chọn vị trí phù hợp → nhấp \"Ứng tuyển ngay\". CV sẽ được gửi kèm tin nhắn tới nhà tuyển dụng. Bạn có thể theo dõi trạng thái trong Dashboard → Việc làm đã ứng tuyển."],

            ['category' => 'job', 'sort_order' => 12, 'views_count' => 320,
                'question' => 'Tôi có thể đăng tin tuyển dụng không?',
                'answer' => "Có! Nếu tài khoản của bạn có vai trò HR (Nhà tuyển dụng), bạn có thể đăng tin tuyển dụng tại Dashboard → Quản lý tin tuyển dụng → Đăng tin mới. Tin đăng sẽ được duyệt trong 24 giờ."],

            ['category' => 'security', 'sort_order' => 13, 'views_count' => 480,
                'question' => 'Dữ liệu CV của tôi có được bảo mật không?',
                'answer' => "Tuyệt đối an toàn:\n- Mã hoá SSL/TLS cho toàn bộ kết nối\n- Mã hoá AES-256 cho dữ liệu lưu trữ\n- Chỉ bạn mới thấy CV nếu không chia sẻ\n- Tuân thủ GDPR và luật bảo vệ dữ liệu Việt Nam\n\nChúng tôi KHÔNG bán hoặc chia sẻ dữ liệu của bạn cho bên thứ ba."],

            ['category' => 'security', 'sort_order' => 14, 'views_count' => 270,
                'question' => 'Chia sẻ CV qua link có an toàn không?',
                'answer' => "Link chia sẻ được bảo vệ bằng token ngẫu nhiên 32 ký tự (gần như không thể đoán). Bạn có thể:\n- Đặt mật khẩu cho link\n- Đặt thời hạn hết hiệu lực\n- Xoá bất kỳ lúc nào\n- Xem ai đã xem qua log"],

            ['category' => 'general', 'sort_order' => 15, 'views_count' => 1850,
                'question' => 'CVactive có miễn phí không?',
                'answer' => "Có! Gói miễn phí cho phép bạn:\n- Tạo tối đa 2 CV\n- Dùng các mẫu cơ bản\n- Xuất PDF có watermark nhỏ\n- Chia sẻ link công khai\n\nĐể sử dụng đầy đủ tính năng, bạn có thể nâng cấp gói Pro chỉ từ 99.000đ/tháng."],

            ['category' => 'general', 'sort_order' => 16, 'views_count' => 920,
                'question' => 'Tôi có nên viết CV bằng tiếng Anh hay tiếng Việt?',
                'answer' => "Tuỳ vào vị trí ứng tuyển:\n- Công ty Việt Nam: tiếng Việt được ưu tiên\n- Công ty nước ngoài / FDI: tiếng Anh\n- Startup quốc tế: tiếng Anh\n\nCVactive hỗ trợ cả hai ngôn ngữ với font chữ đẹp, bạn có thể tạo song song hai phiên bản."],
        ];

        foreach ($samples as $data) {
            Faq::create($data + ['is_active' => true]);
        }

        $this->command->info('Seeded ' . count($samples) . ' FAQs across 6 categories.');
    }
}