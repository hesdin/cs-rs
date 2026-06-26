<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ChatbotSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ChatbotSetting extends Model
{
    /** @use HasFactory<ChatbotSettingFactory> */
    use HasFactory;

    protected const CACHE_KEY = 'chatbot.settings.all';

    protected $fillable = [
        'key',
        'value',
        'group',
        'label',
        'description',
        'is_sensitive',
    ];

    protected function casts(): array
    {
        return [
            'is_sensitive' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /**
     * Ambil setting by key dengan fallback default. Hasil dicache di memory.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        return self::cached()->get($key, $default);
    }

    /**
     * @return Collection<string, ?string>
     */
    public static function cached(): Collection
    {
        /** @var array<string, ?string> $values */
        $values = Cache::remember(self::CACHE_KEY, now()->addMinutes(10), function (): array {
            return self::query()
                ->orderBy('group')
                ->orderBy('key')
                ->pluck('value', 'key')
                ->all();
        });

        return collect($values);
    }

    public static function set(string $key, ?string $value): self
    {
        $setting = self::query()->updateOrCreate(['key' => $key], ['value' => $value]);

        Cache::forget(self::CACHE_KEY);

        return $setting;
    }
}
