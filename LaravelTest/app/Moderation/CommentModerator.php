<?php

namespace App\Moderation;

use App\Models\Comment;

interface CommentModerator
{
    /**
     * Judge a comment without persisting anything. Implementations must never
     * throw for ordinary problems: a moderator that cannot reach a verdict is
     * expected to return a Pending result rather than reject a comment.
     */
    public function moderate(Comment $comment): ModerationResult;

    /**
     * Short identifier stored on the comment so an admin can tell which
     * moderator produced a verdict.
     */
    public function name(): string;
}
