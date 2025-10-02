<?php

namespace Tests\Unit;

use App\Models\ShortUrl;
use Tests\TestCase;

class ShortUrlModelTest extends TestCase
{
    public function test_can_create_short_url_instance(): void
    {
        $shortUrl = new ShortUrl([
            'original_url' => 'https://example.com/test',
            'short_code' => 'abc12345',
            'clicks' => 0,
        ]);

        $this->assertEquals('https://example.com/test', $shortUrl->original_url);
        $this->assertEquals('abc12345', $shortUrl->short_code);
        $this->assertEquals(0, $shortUrl->clicks);
    }

    public function test_fillable_fields_are_correctly_configured(): void
    {
        $shortUrl = new ShortUrl();
        $fillableFields = ['original_url', 'short_code', 'clicks', 'expires_at'];

        $this->assertEquals($fillableFields, $shortUrl->getFillable());
    }

    public function test_casts_are_correctly_configured(): void
    {
        $shortUrl = new ShortUrl();
        $expectedCasts = [
            'expires_at' => 'datetime',
            'clicks' => 'integer',
        ];

        $this->assertEquals($expectedCasts, $shortUrl->getCasts());
    }

    public function test_model_has_correct_table_name(): void
    {
        $shortUrl = new ShortUrl();
        $this->assertEquals('short_urls', $shortUrl->getTable());
    }

    public function test_model_has_timestamps_enabled(): void
    {
        $shortUrl = new ShortUrl();
        $this->assertTrue($shortUrl->timestamps);
    }
}
