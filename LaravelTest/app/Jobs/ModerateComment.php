<?php

namespace App\Jobs;

use App\Models\Comment;
use App\Moderation\CommentModerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ModerateComment implements ShouldQueue
{
    use Queueable;

    /**
     * A verdict that needs a human is a normal outcome, not a failure, so the
     * job is given a few attempts before it is written to the failed jobs
     * table for an admin to look at.
     */
    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(public readonly int $commentId) {}

    public function handle(CommentModerator $moderator): void
    {
        $comment = Comment::find($this->commentId);

        if ($comment === null || ! $comment->isPending()) {
            return;
        }

        $comment->applyModerationResult($moderator->moderate($comment));
    }
}
