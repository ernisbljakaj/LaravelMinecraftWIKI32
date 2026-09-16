@extends('layouts.app')

@section('title', 'Meine Favoriten – Minecraft Server')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">Meine <span class="text-emerald-400">Favoriten</span></h1>
        <p class="mt-2 text-slate-400">Alle Server, die du als Favorit gespeichert hast.</p>

        <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($servers as $server)
                <a href="{{ route('servers.show', $server) }}" class="group rounded-2xl bg-white/5 border border-white/10 p-6 hover:border-amber-500/40 hover:-translate-y-0.5 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-lg font-semibold group-hover:text-emerald-400 transition">{{ $server->name }}</h2>
                            <code class="mt-1 inline-block text-sm text-emerald-400">{{ $server->ip }}</code>
                        </div>
                        <span class="text-amber-400">★</span>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 text-xs">{{ $server->mode }}</span>
                        <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-xs">{{ $server->version }}</span>
                        @foreach ($server->tags as $tag)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-300 border border-amber-500/20">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </a>
            @empty
                <div class="sm:col-span-2 lg:col-span-3 rounded-2xl bg-white/5 border border-white/10 p-12 text-center">
                    <p class="text-lg font-semibold">Noch keine Favoriten</p>
                    <p class="mt-2 text-sm text-slate-500">Entdecke Server und speichere sie mit einem Klick.</p>
                    <a href="{{ route('servers.index') }}" class="mt-6 inline-block rounded-lg bg-emerald-500 hover:bg-emerald-400 px-6 py-3 text-sm font-semibold text-white transition">
                        Server entdecken
                    </a>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $servers->links() }}
        </div>
    </div>
@endsection