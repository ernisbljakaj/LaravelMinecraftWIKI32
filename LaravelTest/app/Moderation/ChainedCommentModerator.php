<?php

namespace App\Moderation;

use App\Models\Comment;

/**
 * Runs moderators in order and stops at the first one that does not approve.
 *
 * Used to put the cheap heuristic in front of the AI so obvious spam never
 * costs an API call, while a comment the heuristic is happy with is still
 * given to the AI.
 */
class ChainedCommentModerator implements CommentModerator
{
    /**
     * @param  array<int, CommentModerator>  $moderators
     */
    public function __construct(private readonly array $moderators) {}

    public function name(): string
    {
        return implode('+', array_map(
            fn (CommentModerator $moderator) => $moderator->name(),
            $this->moderators,
        ));
    }

    public function moderate(Comment $comment): ModerationResult
    {
        $result = ModerationResult::approved('No moderator ran.');

        foreach ($this->moderators as $moderator) {
            $result = $moderator->moderate($comment);

            if (! $result->status->isPublished()) {
                return $result;
            }
        }

        return $result;
    }
}
