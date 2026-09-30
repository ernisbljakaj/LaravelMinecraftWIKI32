<?php

namespace App\Moderation;

final readonly class ModerationResult
{
    /**
     * @param  array<int, string>  $categories
     */
    public function __construct(
        public ModerationStatus $status,
        public string $reason = '',
        public float $confidence = 1.0,
        public array $categories = [],
        public string $source = 'auto',
    ) {}

    public static function approved(string $reason = 'No policy violations detected.', float $confidence = 1.0, string $source = 'auto'): self
    {
        return new self(ModerationStatus::Approved, $reason, $confidence, source: $source);
    }

    public static function rejected(string $reason, float $confidence = 1.0, array $categories = [], string $source = 'auto'): self
    {
        return new self(ModerationStatus::Rejected, $reason, $confidence, $categories, $source);
    }

    public static function pending(string $reason, float $confidence = 0.0, array $categories = [], string $source = 'auto'): self
    {
        return new self(ModerationStatus::Pending, $reason, $confidence, $categories, $source);
    }
}
