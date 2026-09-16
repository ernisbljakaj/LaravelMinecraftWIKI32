@extends('layouts.app')

@section('title', $server->name . ' – Server-Verzeichnis')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <a href="{{ route('servers.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-emerald-400 transition">
            ← Zurück zur Serverliste
        </a>

        <div class="mt-6 grid lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="rounded-2xl bg-white/5 border border-white/10 p-8">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight">{{ $server->name }}</h1>
                            <code class="mt-3 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                                {{ $server->ip }}
                            </code>
                        </div>
                        <div class="flex items-center gap-3">
                            @if ($server->featured)
                                <span class="px-3 py-1.5 rounded-full bg-amber-500/20 text-amber-300 text-sm border border-amber-500/30">★ Featured</span>
                            @endif
                            @auth
                                <form method="POST" action="{{ route('servers.favorite', $server) }}">
                                    @csrf
                                    @if (auth()->user()->favoritedServers()->where('servers.id', $server->id)->exists())
                                        <button type="submit" class="px-4 py-2 rounded-lg bg-amber-500 text-white text-sm font-semibold hover:bg-amber-400 transition">
                                            ★ Favorit
                                        </button>
                                    @else
                                        <button type="submit" class="px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-sm font-semibold text-slate-300 hover:bg-white/10 transition">
                                            ☆ Als Favorit speichern
                                        </button>
                                    @endif
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-sm text-slate-300 hover:bg-white/10 transition">
                                    ☆ Anmelden zum Speichern
                                </a>
                            @endauth
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-2">
                        <span class="px-3 py-1.5 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 text-sm">{{ $server->mode }}</span>
                        <span class="px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-sm">Version {{ $server->version }}</span>
                        @foreach ($server->tags as $tag)
                            <a href="{{ route('servers.index', ['tag' => $tag->slug]) }}" class="px-3 py-1.5 rounded-full bg-amber-500/10 text-amber-300 border border-amber-500/20 text-sm hover:bg-amber-500/20 transition">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-8 prose prose-invert max-w-none">
                        <h2 class="text-xl font-semibold">Über diesen Server</h2>
                        <p class="mt-3 leading-relaxed text-slate-300 whitespace-pre-line">{{ $server->description }}</p>
                    </div>

                    @if ($server->user)
                        <p class="mt-8 text-sm text-slate-500">
                            Eingetragen von <span class="text-slate-300">{{ $server->user->name }}</span>
                        </p>
                    @endif
                </div>

                <div class="mt-8">
                    <h2 class="text-xl font-semibold">Kommentare <span class="text-slate-500 text-sm font-normal">({{ $comments->count() }})</span></h2>

                    @auth
                        <form method="POST" action="{{ route('comments.store') }}" class="mt-4 rounded-2xl bg-white/5 border border-white/10 p-6">
                            @csrf
                            <input type="hidden" name="commentable_type" value="{{ get_class($server) }}">
                            <input type="hidden" name="commentable_id" value="{{ $server->id }}">
                            <textarea name="body" rows="3" required placeholder="Schreibe einen Kommentar…" class="w-full rounded-lg bg-white/5 border border-white/10 px-4 py-3 text-sm focus:outline-none focus:border-emerald-500/50">{{ old('body') }}</textarea>
                            @error('body')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                            <div class="mt-3 flex justify-end">
                                <button type="submit" class="rounded-lg bg-emerald-500 hover:bg-emerald-400 px-5 py-2 text-sm font-semibold text-white transition">Kommentieren</button>
                            </div>
                        </form>
                    @else
                        <p class="mt-4 rounded-2xl bg-white/5 border border-white/10 p-6 text-sm text-slate-400">
                            <a href="{{ route('login') }}" class="text-emerald-400 hover:underline">Melde dich an</a>, um einen Kommentar zu schreiben.
                        </p>
                    @endauth

                    <div class="mt-6 space-y-4">
                        @forelse ($comments as $comment)
                            <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <span class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-green-600 grid place-items-center text-xs font-bold text-white">{{ mb_strtoupper(mb_substr($comment->user->name, 0, 1)) }}</span>
                                        <div>
                                            <p class="text-sm font-medium">{{ $comment->user->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $comment->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    @if (auth()->id() === $comment->user_id || (auth()->user()?->isAdmin()))
                                        <form method="POST" action="{{ route('comments.destroy', $comment) }}" onsubmit="return confirm('Kommentar wirklich löschen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-400 hover:text-red-300">Löschen</button>
                                        </form>
                                    @endif
                                </div>
                                <p class="mt-3 text-sm leading-relaxed text-slate-300">{{ $comment->body }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Noch keine Kommentare. Sei der erste!</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <aside class="space-y-6">
                @if ($related->isNotEmpty())
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-widest text-slate-500">Ähnliche Server</h3>
                        <div class="mt-4 space-y-3">
                            @foreach ($related as $rel)
                                <a href="{{ route('servers.show', $rel) }}" class="block rounded-xl bg-white/5 border border-white/10 p-4 hover:border-emerald-500/40 hover:-translate-y-0.5 transition">
                                    <p class="font-semibold text-sm group-hover:text-emerald-400">{{ $rel->name }}</p>
                                    <code class="text-xs text-emerald-400">{{ $rel->ip }}</code>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="rounded-2xl bg-gradient-to-br from-emerald-900/30 to-transparent border border-emerald-500/20 p-6">
                    <h3 class="text-sm font-semibold text-emerald-300">Neu in unserem Wiki?</h3>
                    <p class="mt-2 text-sm text-slate-400">Entdecke Guides zu Redstone, Farmen und mehr.</p>
                    <a href="{{ route('wiki.index') }}" class="mt-4 inline-block rounded-lg bg-emerald-500 hover:bg-emerald-400 px-4 py-2 text-sm font-semibold text-white transition">Zum Wiki</a>
                </div>
            </aside>
        </div>
    </div>
@endsection