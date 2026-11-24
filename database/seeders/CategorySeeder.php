<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Thời sự',    'slug' => 'thoi-su',    'description' => 'Tin tức thời sự trong nước và quốc tế'],
            ['name' => 'Công nghệ',  'slug' => 'cong-nghe',  'description' => 'Xu hướng công nghệ, AI và lập trình'],
            ['name' => 'Giáo dục',   'slug' => 'giao-duc',   'description' => 'Giáo dục, Tri thức hôm nay, tương lai ngày mai'],
            ['name' => 'Sức khỏe',   'slug' => 'suc-khoe',   'description' => 'Sức khỏe, sống xanh và lành mạnh'],
            ['name' => 'Pháp luật',  'slug' => 'phap-luat',  'description' => 'Pháp luật và công lý'],
            ['name' => 'Kinh doanh', 'slug' => 'kinh-doanh', 'description' => 'Vượt giới hạn, bứt phá tương lai.'],
            ['name' => 'Thể thao',   'slug' => 'the-thao',   'description' => 'Các sự kiện thể thao'],
            ['name' => 'Giải trí',   'slug' => 'giai-tri',   'description' => 'Văn hóa, âm nhạc, điện ảnh'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
