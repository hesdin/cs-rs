<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ChatConversationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ChatConversation extends Model
{
    /** @use HasFactory<ChatConversationFactory> */
    use HasFactory;

    protected $fillable = [
        'session_id',
        'channel',
        'user_identifier',
        'metadata',
        'started_at',
        'last_activity_at',
        'handed_off',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'started_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'handed_off' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $conversation): void {
            if (empty($conversation->session_id)) {
                $conversation->session_id = (string) Str::uuid();
            }
            if (empty($conversation->started_at)) {
                $conversation->started_at = now();
            }
        });
    }

    /**
     * @return HasMany<ChatMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }
}
