<?php

return [
    'limit_per_feed' => env('RSS_LIMIT_PER_FEED', 10),

    'sources' => [
        [
            'name' => 'VnExpress',
            'category_slug' => 'thoi-su',
            'url' => 'https://vnexpress.net/rss/thoi-su.rss',
        ],
        [
            'name' => 'VnExpress',
            'category_slug' => 'cong-nghe',
            'url' => 'https://vnexpress.net/rss/so-hoa.rss',
        ],
        [
            'name' => 'Tuổi Trẻ',
            'category_slug' => 'kinh-doanh',
            'url' => 'https://tuoitre.vn/rss/kinh-doanh.rss',
        ],
        [
            'name' => 'Dân Trí',
            'category_slug' => 'the-thao',
            'url' => 'https://dantri.com.vn/rss/the-thao.rss',
        ],
        [
            'name' => 'VnExpress',
            'category_slug' => 'giai-tri',
            'url' => 'https://vnexpress.net/rss/giai-tri.rss',
        ],
    ],
];