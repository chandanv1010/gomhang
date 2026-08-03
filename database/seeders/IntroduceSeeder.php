<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Introduce;

class IntroduceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'block_1_banner_image' => 'https://picsum.photos/1920/1080?random=10',
            'block_1_banner_title' => 'Công Nghệ Độc Quyền TexGuard',
            'block_1_banner_desc' => 'Với vị thế tạo nên xu hướng công nghệ đột phá toàn cầu, Quy trình xử lý bề mặt Thanh nhôm MAXPRO.JP được áp dụng công nghệ độc quyền TexGuard.',
            
            // Icon 1
            'block_2_icon_1' => '/frontend/resources/img/project/techs-icon-1.svg',
            'block_2_title_1' => 'Cung cấp độ bền bỉ vượt trội với thời tiết',
            'block_2_desc_1' => '',
            
            // Icon 2
            'block_2_icon_2' => '/frontend/resources/img/project/techs-icon-2.svg',
            'block_2_title_2' => 'Thân thiện với môi trường',
            'block_2_desc_2' => '',
            
            // Icon 3
            'block_2_icon_3' => '/frontend/resources/img/project/techs-icon-3.svg',
            'block_2_title_3' => 'Phai màu tối thiểu',
            'block_2_desc_3' => '',
            
            // Icon 4
            'block_2_icon_4' => '/frontend/resources/img/project/techs-icon-4.svg',
            'block_2_title_4' => 'Bảo dưỡng ít, dễ vệ sinh bề mặt, tăng khả năng chống bám bẩn',
            'block_2_desc_4' => '',
            
            // Accordion Showcase
            'block_3_image' => 'https://picsum.photos/800/600?random=11',
            'block_3_title' => 'Ưu Điểm Vượt Trội',
            
            'block_3_acc_title_1' => 'Bền màu ấn tượng',
            'block_3_acc_content_1' => '<p>Kết quả thử nghiệm cho thấy lớp phủ TEXGUARD mang lại khả năng chống phai màu tốt hơn lớp phủ thông thường. Bề mặt sáng bóng tự nhiên và tăng cường tuổi thọ bề mặt dài hơn đáng kể.</p>',
            
            'block_3_acc_title_2' => 'Tăng cường khả năng chống chọi với mọi loại thời tiết',
            'block_3_acc_content_2' => '<p>Công nghệ phủ độc quyền giúp chống muối biển, chống tia UV cực mạnh và các tác nhân ô nhiễm môi trường công nghiệp hiện đại.</p>',
            
            'block_3_acc_title_3' => 'Chống bám bẩn, dễ vệ sinh',
            'block_3_acc_content_3' => '<p>Lớp bảo vệ trơn láng làm giảm khả năng tích tụ bụi bẩn, giúp nước mưa dễ dàng rửa trôi và giảm thiểu công sức bảo dưỡng định kỳ.</p>'
        ];

        foreach ($data as $key => $value) {
            Introduce::updateOrCreate(
                ['keyword' => $key, 'language_id' => 1],
                ['content' => $value, 'user_id' => 1]
            );
        }
    }
}
