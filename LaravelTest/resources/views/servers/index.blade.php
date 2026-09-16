@extends('layouts.app')

@section('title', 'Server-Verzeichnis – Minecraft Wiki & Server')

@section('content')
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-emerald-900/20 to-transparent pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="max-w-3xl">
                <h1 class="text-4xl sm:text-5xl font-bold tracking-tight">
                    Entdecke die besten <span class="text-emerald-400">Minecraft-Server</span>
                </h1>
                <p class="mt-4 text-lg text-slate-400">
                    Vergleiche Server nach IP, Version und Spielmodus. Speichere deine Lieblingsserver und
                    tauche ein in Guides aus unserem Wiki.
                </p>
                <form method="GET" action="{{ route('servers.index') }}" class="mt-8 flex flex-col sm:flex-row gap-3">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Server, IP oder Modus suchen…"
                        class="flex-1 rounded-lg bg-white/5 border border-white/10 px-4 py-3 text-sm focus:outline-none focus:border-emerald-500/50"
                    >
                    <button type="submit" class="rounded-lg bg-emerald-500 hover:bg-emerald-400 px-6 py-3 text-sm font-semibold text-white transition">
                        Suchen
                    </button>
                </form>
            </div>

            @if ($featured->isNotEmpty())
                <div class="mt-12">
                    <h2 class="text-xs font-semibold uppercase tracking-widest text-emerald-400">Vorgestellte Server</h2>
                    <div class="mt-4 grid gap-6 md:grid-cols-3">
                        @foreach ($featured as $server)
                            <a href="{{ route('servers.show', $server) }}" class="group rounded-2xl bg-white/5 border border-white/10 p-6 hover:border-emerald-500/40 transition">
                                <div class="flex items-start justify-between">
                                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-400 to-orange-600 grid place-items-center font-bold text-white">★</div>
                                    <span class="text-xs px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-300">Featured</span>
                                </div>
                                <h3 class="mt-4 text-lg font-semibold group-hover:text-emerald-400 transition">{{ $server->name }}</h3>
                                <code class="mt-1 inline-block text-sm text-emerald-400">{{ $server->ip }}</code>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach ($server->tags as $tag)
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-white/5 border border-white/10">{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-4 gap-8">
            <aside class="lg:col-span-1">
                <div class="lg:sticky lg:top-24 space-y-6">
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-widest text-slate-500">Tags filtern</h3>
                        <div class="mt-4 flex lg:flex-col flex-wrap gap-2">
                            <a href="{{ route('servers.index', array_merge(request()->except('tag'), ['tag' => null])) }}" class="px-3 py-2 rounded-lg text-sm {{ ! request('tag') ? 'bg-emerald-500 text-white font-medium' : 'bg-white/5 text-slate-300 hover:bg-white/10' }} transition">
                                Alle
                            </a>
                            @foreach ($tags as $tag)
                                <a href="{{ route('servers.index', array_merge(request()->except('tag'), ['tag' => $tag->slug])) }}" class="px-3 py-2 rounded-lg text-sm flex items-center justify-between {{ request('tag') === $tag->slug ? 'bg-emerald-500 text-white font-medium' : 'bg-white/5 text-slate-300 hover:bg-white/10' }} transition">
                                    <span>{{ $tag->name }}</span>
                                    <span class="text-xs opacity-70">{{ $tag->servers_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </aside>

            <div class="lg:col-span-3">
                @if (request('q'))
                    <p class="mb-6 text-sm text-slate-400">
                        Ergebnisse für <strong class="text-slate-200">"{{ request('q') }}"</strong>
                    </p>
                @endif

                <div class="grid sm:grid-cols-2 gap-6">
                    @forelse ($servers as $server)
                        <a href="{{ route('servers.show', $server) }}" class="group rounded-2xl bg-white/5 border border-white/10 p-6 hover:border-emerald-500/40 hover:-translate-y-0.5 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-lg font-semibold group-hover:text-emerald-400 transition">{{ $server->name }}</h3>
                                    <code class="mt-1 inline-block text-sm text-emerald-400">{{ $server->ip }}</code>
                                </div>
                                @if ($server->featured)
                                    <span class="text-amber-300" title="Featured">★</span>
                                @endif
                            </div>

                            <p class="mt-3 text-sm text-slate-400 line-clamp-2">{{ $server->description }}</p>

                            <div class="mt-4 flex items-center gap-2 text-xs">
                                <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                                    {{ $server->mode }}
                                </span>
                                <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10">
                                    {{ $server->version }}
                                </span>
                            </div>

                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($server->tags as $tag)
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-300 border border-amber-500/20">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        </a>
@empty
                    <div class="sm:col-span-2 rounded-2xl bg-white/5 border border-white/10 p-12 text-center">
                        <p class="text-slate-400">Keine Server gefunden.</p>
                        <p class="mt-2 text-sm text-slate-500">Ändere deinen Suchbegriff oder Filter.</p>
                        @auth
                            <a href="{{ route('servers.create') }}" class="mt-6 inline-block rounded-lg bg-emerald-500 hover:bg-emerald-400 px-6 py-3 text-sm font-semibold text-white transition">
                                Eigenen Server einreichen
                            </a>
                        @endauth
                    </div>
                @endforelse
                </div>

                <div class="mt-8">
                    {{ $servers->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection