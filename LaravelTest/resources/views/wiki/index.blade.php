@extends('layouts.app')

@section('title', 'Wiki – Minecraft Wiki & Server')

@section('content')
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-emerald-900/20 to-transparent pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="max-w-3xl">
                <h1 class="text-4xl sm:text-5xl font-bold tracking-tight">
                    Minecraft <span class="text-emerald-400">Wiki</span>
                </h1>
                <p class="mt-4 text-lg text-slate-400">
                    Guides zu Redstone, Farmen, Bausteinen und mehr – alles an einem Ort.
                </p>
                <form method="GET" action="{{ route('wiki.index') }}" class="mt-8 flex flex-col sm:flex-row gap-3">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Guide suchen…"
                        class="flex-1 rounded-lg bg-white/5 border border-white/10 px-4 py-3 text-sm focus:outline-none focus:border-emerald-500/50"
                    >
                    <button type="submit" class="rounded-lg bg-emerald-500 hover:bg-emerald-400 px-6 py-3 text-sm font-semibold text-white transition">
                        Suchen
                    </button>
                </form>
            </div>

            <div class="mt-10 flex flex-wrap gap-2">
                <a href="{{ route('wiki.index', array_merge(request()->except('category'), ['category' => null])) }}" class="px-3 py-1.5 rounded-full text-sm {{ ! request('category') ? 'bg-emerald-500 text-white font-medium' : 'bg-white/5 text-slate-300 hover:bg-white/10' }} transition">
                    Alle
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('wiki.index', array_merge(request()->except('category'), ['category' => $category])) }}" class="px-3 py-1.5 rounded-full text-sm {{ request('category') === $category ? 'bg-emerald-500 text-white font-medium' : 'bg-white/5 text-slate-300 hover:bg-white/10' }} transition">
                        {{ $category }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($pages as $page)
                <a href="{{ route('wiki.show', $page) }}" class="group rounded-2xl bg-white/5 border border-white/10 p-6 hover:border-emerald-500/40 hover:-translate-y-0.5 transition flex flex-col">
                    <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 w-fit">{{ $page->category }}</span>
                    <h2 class="mt-3 text-lg font-semibold group-hover:text-emerald-400 transition">{{ $page->title }}</h2>
                    @if ($page->excerpt)
                        <p class="mt-2 text-sm text-slate-400 line-clamp-3">{{ $page->excerpt }}</p>
                    @endif
                    <div class="mt-auto pt-4 flex items-center justify-between text-xs text-slate-500">
                        <span>{{ $page->user?->name ?? 'Unbekannt' }}</span>
                        <span>{{ $page->created_at->format('d.m.Y') }}</span>
                    </div>
                </a>
            @empty
                <div class="sm:col-span-2 lg:col-span-3 rounded-2xl bg-white/5 border border-white/10 p-12 text-center">
                    <p class="text-slate-400">Keine Wiki-Artikel gefunden.</p>
                    <p class="mt-2 text-sm text-slate-500">Ändere deinen Suchbegriff oder die Kategorie.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $pages->links() }}
        </div>
    </section>
@endsection