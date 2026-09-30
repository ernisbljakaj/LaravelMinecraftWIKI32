<?php

namespace App\Http\Controllers;

use App\Models\WikiPage;

class WikiController extends Controller
{
    public function index()
    {
        $pages = WikiPage::with('user')
            ->where('approved', true)
            ->when(request('category'), function ($query, $category) {
                $query->where('category', $category);
            })
            ->when(request('q'), function ($query, $q) {
                $query->where(function ($query) use ($q) {
                    $query->where('title', 'like', "%{$q}%")
                        ->orWhere('content', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = WikiPage::where('approved', true)
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('wiki.index', [
            'pages' => $pages,
            'categories' => $categories,
        ]);
    }

    public function show(WikiPage $wikiPage)
    {
        abort_unless($wikiPage->approved, 404);

        $wikiPage->load('user');

        // Only published comments are shown to the public. Admins see the
        // hidden ones too, otherwise there would be no way to review what the
        // moderation bot decided. The `comments.user` relation is not eager
        // loaded separately: the query below already hydrates it, and loading
        // it twice cost an extra round trip per page view.
        $comments = $wikiPage->comments()
            ->when(! auth()->user()?->isAdmin(), fn ($query) => $query->published())
            ->with('user')
            ->latest()
            ->get();
        $related = WikiPage::where('id', '!=', $wikiPage->id)
            ->where('approved', true)
            ->where('category', $wikiPage->category)
            ->latest()
            ->take(3)
            ->get();

        return view('wiki.show', [
            'page' => $wikiPage,
            'comments' => $comments,
            'related' => $related,
        ]);
    }
}
