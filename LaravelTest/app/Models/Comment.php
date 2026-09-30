<?php

namespace App\Models;

use App\Moderation\ModerationResult;
use App\Moderation\ModerationStatus;
use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'commentable_type',
        'commentable_id',
        'body',
        'approved',
        'moderation_status',
        'moderation_source',
        'moderation_reason',
        'moderation_confidence',
        'moderated_at',
    ];

    protected function casts(): array
    {
        return [
            'approved' => 'boolean',
            'moderation_status' => ModerationStatus::class,
            'moderation_confidence' => 'float',
            'moderated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo('commentable');
    }

    /**
     * Only comments that passed moderation are ever shown publicly.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('approved', true);
    }

    public function scopeAwaitingReview(Builder $query): Builder
    {
        return $query->where('approved', false);
    }

    public function isPublished(): bool
    {
        return $this->approved === true;
    }

    public function isPending(): bool
    {
        return $this->moderation_status === ModerationStatus::Pending;
    }

    public function isRejected(): bool
    {
        return $this->moderation_status === ModerationStatus::Rejected;
    }

    /**
     * Record a verdict and keep the legacy `approved` column in sync, since it
     * is what the public queries and the admin panel filter on.
     */
    public function applyModerationResult(ModerationResult $result): void
    {
        $this->forceFill([
            'moderation_status' => $result->status,
            'moderation_source' => $result->source,
            'moderation_reason' => $result->reason !== '' ? $result->reason : null,
            'moderation_confidence' => $result->confidence,
            'moderated_at' => now(),
            'approved' => $result->status->isPublished(),
        ])->save();
    }
}
