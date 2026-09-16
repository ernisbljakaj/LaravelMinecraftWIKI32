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

        $wikiPage->load(['user', 'comments.user']);

        $comments = $wikiPage->comments()->with('user')->latest()->get();
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
