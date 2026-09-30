@extends('layouts.app')

@section('title', $page->title . ' – Minecraft Wiki')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <a href="{{ route('wiki.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-emerald-400 transition">
            ← Back to the wiki
        </a>

        <div class="mt-6">
            <span class="px-3 py-1.5 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 text-sm">{{ $page->category }}</span>
            <h1 class="mt-4 text-3xl sm:text-4xl font-bold tracking-tight">{{ $page->title }}</h1>
            <p class="mt-3 text-sm text-slate-500">
                By {{ $page->user?->name ?? 'Unknown' }} · {{ $page->created_at->format('d.m.Y') }}
            </p>
        </div>

        <article class="mt-8 rounded-2xl bg-white/5 border border-white/10 p-8">
            <div class="prose prose-invert prose-lg prose-p:leading-relaxed prose-headings:text-slate-100 prose-strong:text-emerald-300 max-w-none whitespace-pre-line">
                {!! $page->content !!}
            </div>
        </article>

        <div class="mt-10">
            <h2 class="text-xl font-semibold">Comments <span class="text-slate-500 text-sm font-normal">({{ $comments->count() }})</span></h2>

            @auth
                @include('comments._form', ['commentable' => $page])
            @else
                <p class="mt-4 rounded-2xl bg-white/5 border border-white/10 p-6 text-sm text-slate-400">
                    <a href="{{ route('login') }}" class="text-emerald-400 hover:underline">Log in</a> to leave a comment.
                </p>
            @endauth

            @include('comments._list', ['comments' => $comments])
        </div>

        @if ($related->isNotEmpty())
            <div class="mt-10">
                <h3 class="text-sm font-semibold uppercase tracking-widest text-slate-500">More articles in {{ $page->category }}</h3>
                <div class="mt-4 grid sm:grid-cols-3 gap-4">
                    @foreach ($related as $rel)
                        <a href="{{ route('wiki.show', $rel) }}" class="rounded-xl bg-white/5 border border-white/10 p-4 hover:border-emerald-500/40 hover:-translate-y-0.5 transition">
                            <p class="text-sm font-semibold hover:text-emerald-400">{{ $rel->title }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $rel->excerpt ?? '' }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection