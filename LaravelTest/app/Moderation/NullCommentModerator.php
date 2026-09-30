<?php

namespace App\Moderation;

use App\Models\Comment;

/**
 * The moderator used when moderation is switched off: everything is published
 * immediately, which is the behaviour the site had before the bot existed.
 */
class NullCommentModerator implements CommentModerator
{
    public function name(): string
    {
        return 'disabled';
    }

    public function moderate(Comment $comment): ModerationResult
    {
        return ModerationResult::approved('Moderation is disabled.', source: $this->name());
    }
}
