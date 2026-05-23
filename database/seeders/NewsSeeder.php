<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $news = [
            [
                'title' => 'Welcome to Ascend Academy!',
                'slug' => 'welcome-to-ascend-academy',
                'description' => 'We are excited to launch our new platform for online learning.',
                'is_published' => true,
            ],
            [
                'title' => 'New English Courses Available',
                'slug' => 'new-english-courses-available',
                'description' => 'Check out our latest business English courses for adults.',
                'is_published' => true,
            ],
            [
                'title' => 'Upcoming Holiday Schedule',
                'slug' => 'upcoming-holiday-schedule',
                'description' => 'Please note the academy will be closed during the upcoming national holiday.',
                'is_published' => true,
            ],
        ];

        foreach ($news as $item) {
            News::firstOrCreate(['title' => $item['title']], $item);
        }
    }
}
