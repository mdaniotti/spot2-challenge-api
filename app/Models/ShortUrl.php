<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class ShortUrl extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_url',
        'short_code',
        'clicks',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'clicks' => 'integer',
    ];

    protected $guarded = [
        '_id',
        'created_at',
        'updated_at',
    ];

    // Generate a unique short code
    public static function generateShortCode(): string
    {
        do {
            $code = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
        } while (self::where('short_code', $code)->exists());

        return $code;
    }

    // Check if the URL is expired
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    // Increment click count
    public function incrementClicks(): void
    {
        $this->increment('clicks');
    }
}
