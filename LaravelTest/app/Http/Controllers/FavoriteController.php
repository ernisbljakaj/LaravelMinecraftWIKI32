<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $servers = auth()->user()->favoritedServers()
            ->with('tags')
            ->where('approved', true)
            ->latest()
            ->paginate(12);

        return view('favorites.index', ['servers' => $servers]);
    }

    public function toggle(Request $request, Server $server): RedirectResponse
    {
        abort_unless($server->approved, 404);

        $user = $request->user();

        if ($user->favoritedServers()->where('servers.id', $server->id)->exists()) {
            $user->favoritedServers()->detach($server->id);
            $status = 'removed from your favorites';
        } else {
            $user->favoritedServers()->attach($server->id);
            $status = 'added to your favorites';
        }

        return redirect()->back()->with('status', "{$server->name} was $status.");
    }
}
