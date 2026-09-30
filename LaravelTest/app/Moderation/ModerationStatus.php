<?php

namespace App\Moderation;

enum ModerationStatus: string
{
    /** Not judged yet, or judged too ambiguous to publish and held for a human. */
    case Pending = 'pending';

    /** Passed moderation and is publicly visible. */
    case Approved = 'approved';

    /** Refused by moderation and kept hidden. */
    case Rejected = 'rejected';

    public function isPublished(): bool
    {
        return $this === self::Approved;
    }

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending review',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }
}
