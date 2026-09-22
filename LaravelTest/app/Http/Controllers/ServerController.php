<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServerController extends Controller
{
    public function create()
    {
        $tags = Tag::orderBy('name')->get();

        return view('servers.create', ['tags' => $tags]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'ip' => ['required', 'string', 'max:255'],
            'version' => ['required', 'string', 'max:50'],
            'mode' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:2000'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ]);

        $server = Auth::user()->servers()->create([
            'name' => $data['name'],
            'ip' => $data['ip'],
            'version' => $data['version'] ?? '1.21',
            'mode' => $data['mode'] ?? 'Survival',
            'description' => $data['description'],
            'approved' => false,
            'featured' => false,
        ]);

        if (! empty($data['tags'])) {
            $server->tags()->attach($data['tags']);
        }

        return redirect()->route('servers.index')
            ->with('status', 'Your server has been submitted and is waiting for approval by an admin.');
    }

    public function index()
    {
        $servers = Server::with('tags')
            ->where('approved', true)
            ->when(request('tag'), function ($query, $tag) {
                $query->whereHas('tags', fn ($q) => $q->where('slug', $tag));
            })
            ->when(request('q'), function ($query, $q) {
                $query->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                        ->orWhere('ip', 'like', "%{$q}%")
                        ->orWhere('mode', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('featured')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        $tags = Tag::withCount('servers')
            ->orderByDesc('servers_count')
            ->get();

        $featured = Server::with('tags')
            ->where('approved', true)
            ->where('featured', true)
            ->latest()
            ->take(3)
            ->get();

        return view('servers.index', [
            'servers' => $servers,
            'tags' => $tags,
            'featured' => $featured,
        ]);
    }

    public function show(Server $server)
    {
        abort_unless($server->approved, 404);

        $server->load(['tags', 'user', 'comments.user']);

        $comments = $server->comments()->with('user')->latest()->get();
        $related = Server::where('id', '!=', $server->id)
            ->where('approved', true)
            ->whereHas('tags', fn ($q) => $q->whereIn('tags.id', $server->tags->pluck('id')))
            ->with('tags')
            ->latest()
            ->take(3)
            ->get();

        return view('servers.show', [
            'server' => $server,
            'comments' => $comments,
            'related' => $related,
        ]);
    }
}
