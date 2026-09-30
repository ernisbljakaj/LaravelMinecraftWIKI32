<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Server;
use App\Models\User;
use App\Moderation\ModerationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    public function definition(): array
    {
        $server = Server::factory();

        return [
            'user_id' => User::factory(),
            'commentable_type' => $server->modelName(),
            'commentable_id' => $server,
            'body' => fake()->paragraph(2),
            'approved' => true,
            'moderation_status' => ModerationStatus::Approved,
            'moderation_source' => 'factory',
            'moderated_at' => now(),
        ];
    }

    /**
     * Attach the comment to an existing page instead of creating a new one.
     */
    public function on(mixed $commentable): static
    {
        return $this->state(fn () => [
            'commentable_type' => $commentable::class,
            'commentable_id' => $commentable->getKey(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'approved' => false,
            'moderation_status' => ModerationStatus::Pending,
            'moderation_source' => null,
            'moderation_reason' => null,
            'moderated_at' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'approved' => true,
            'moderation_status' => ModerationStatus::Approved,
        ]);
    }

    public function rejected(string $reason = 'Rejected by moderation.'): static
    {
        return $this->state(fn () => [
            'approved' => false,
            'moderation_status' => ModerationStatus::Rejected,
            'moderation_reason' => $reason,
        ]);
    }
}
