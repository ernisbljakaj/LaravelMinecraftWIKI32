<?php

namespace App\Http\Controllers;

use App\Jobs\ModerateComment;
use App\Models\Comment;
use App\Models\Server;
use App\Models\WikiPage;
use App\Moderation\ModerationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    /**
     * The only models a comment may be attached to. Kept in one place so the
     * request validation and the "is this page public" check cannot drift.
     *
     * @var array<int, class-string<Model>>
     */
    private const COMMENTABLE_TYPES = [
        Server::class,
        WikiPage::class,
    ];

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'commentable_type' => ['required', 'string', Rule::in(self::COMMENTABLE_TYPES)],
            'commentable_id' => ['required', 'integer'],
            'body' => [
                'required',
                'string',
                'min:'.config('moderation.heuristic.min_length'),
                'max:'.config('moderation.heuristic.max_length'),
            ],
        ]);

        $commentable = $data['commentable_type']::findOrFail($data['commentable_id']);

        // A comment can only be attached to a page that is actually visible.
        // This used to be `method_exists($commentable, 'approved')`, which is
        // always false for an Eloquent attribute, so the guard never ran.
        abort_unless($commentable->approved, 404);

        $comment = $commentable->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $data['body'],
            'approved' => false,
            'moderation_status' => ModerationStatus::Pending,
        ]);

        $this->dispatchModeration($comment);

        return redirect()->back()->with('status', $this->statusMessage($comment));
    }

    public function destroy(Request $request, Comment $comment): RedirectResponse
    {
        abort_unless($request->user()->isAdmin() || $comment->user_id === $request->user()->id, 403);

        $comment->delete();

        return redirect()->back()->with('status', 'Comment deleted.');
    }

    /**
     * The job re-reads the comment as its own model instance, so the local one
     * has to be refreshed before its verdict can be reported back.
     */
    private function dispatchModeration(Comment $comment): void
    {
        $job = new ModerateComment($comment->getKey());

        if (config('moderation.queue')) {
            dispatch($job);

            return;
        }

        dispatch_sync($job);

        $comment->refresh();
    }

    /**
     * When moderation runs inline the verdict is already known, so the person
     * who posted gets a truthful message instead of a generic one.
     */
    private function statusMessage(Comment $comment): string
    {
        return match ($comment->moderation_status) {
            ModerationStatus::Approved => 'Comment added.',
            ModerationStatus::Rejected => 'Your comment was not published. Please review the community guidelines.',
            default => 'Thanks! Your comment was received and is awaiting review.',
        };
    }
}
