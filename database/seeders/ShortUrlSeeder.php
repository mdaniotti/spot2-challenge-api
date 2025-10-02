<?php

namespace Database\Seeders;

use App\Models\ShortUrl;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ShortUrlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $urls = [
            [
                'original_url' => 'https://github.com/laravel/laravel',
                'clicks' => 1250,
                'expires_at' => null,
            ],
            [
                'original_url' => 'https://docs.mongodb.com/manual/introduction/',
                'clicks' => 890,
                'expires_at' => Carbon::now()->addDays(30),
            ],
            [
                'original_url' => 'https://stackoverflow.com/questions/tagged/laravel',
                'clicks' => 2100,
                'expires_at' => null,
            ],
            [
                'original_url' => 'https://www.php.net/manual/en/',
                'clicks' => 3400,
                'expires_at' => Carbon::now()->addDays(7),
            ],
            [
                'original_url' => 'https://tailwindcss.com/docs',
                'clicks' => 1560,
                'expires_at' => null,
            ],
            [
                'original_url' => 'https://vuejs.org/guide/',
                'clicks' => 980,
                'expires_at' => Carbon::now()->addDays(15),
            ],
            [
                'original_url' => 'https://developer.mozilla.org/en-US/docs/Web/JavaScript',
                'clicks' => 4200,
                'expires_at' => null,
            ],
            [
                'original_url' => 'https://www.docker.com/get-started',
                'clicks' => 750,
                'expires_at' => Carbon::now()->addDays(60),
            ],
            [
                'original_url' => 'https://redis.io/documentation',
                'clicks' => 1100,
                'expires_at' => null,
            ],
            [
                'original_url' => 'https://www.postman.com/',
                'clicks' => 2300,
                'expires_at' => Carbon::now()->addDays(45),
            ],
        ];

        foreach ($urls as $urlData) {
            $urlData['short_code'] = ShortUrl::generateShortCode();
            ShortUrl::create($urlData);
        }

        $this->command->info('Seeded 10 ShortUrl records successfully!');
    }
}
