<?php

namespace App\Models;

use Database\Factories\ChatConversationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['uuid', 'user_id', 'locale', 'last_message_at'])]
class ChatConversation extends Model
{
    /** @use HasFactory<ChatConversationFactory> */
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    /**
     * Resolve conversations by their public uuid in routes.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * The customer who owns this conversation, if they were logged in.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * All stored turns of this conversation, including tool turns.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('id');
    }

    /**
     * Only the turns a person typed or read (no tool calls or results).
     */
    public function visibleMessages(): HasMany
    {
        return $this->messages()->whereNotNull('text');
    }

    /**
     * Whether the given customer (or guest) may read and continue this conversation.
     */
    public function isAccessibleBy(?User $user): bool
    {
        return $this->user_id === null || $this->user_id === $user?->id;
    }
}
