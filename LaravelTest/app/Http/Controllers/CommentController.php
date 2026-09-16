<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Server;
use App\Models\WikiPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $allowedTypes = [
            Server::class,
            WikiPage::class,
        ];

        $data = $request->validate([
            'commentable_type' => ['required', 'string', 'in:'.implode(',', $allowedTypes)],
            'commentable_id' => ['required', 'integer'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $commentable = $data['commentable_type']::findOrFail($data['commentable_id']);

        if (method_exists($commentable, 'approved') && ! $commentable->approved) {
            abort(404);
        }

        $commentable->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $data['body'],
            'approved' => true,
        ]);

        return redirect()->back()->with('status', 'Kommentar hinzugefügt.');
    }

    public function destroy(Request $request, Comment $comment): RedirectResponse
    {
        abort_unless($request->user()->isAdmin() || $comment->user_id === $request->user()->id, 403);

        $comment->delete();

        return redirect()->back()->with('status', 'Kommentar gelöscht.');
    }
}
