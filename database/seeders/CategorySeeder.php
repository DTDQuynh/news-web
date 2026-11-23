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
            ['name' => 'Kinh doanh', 'slug' => 'kinh-doanh', 'description' => 'Thị trường tài chính, khởi nghiệp'],
            ['name' => 'Thể thao',   'slug' => 'the-thao',   'description' => 'Bóng đá và các sự kiện thể thao'],
            ['name' => 'Giải trí',   'slug' => 'giai-tri',   'description' => 'Văn hóa, âm nhạc, điện ảnh'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
