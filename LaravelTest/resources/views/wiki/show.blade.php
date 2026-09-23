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
                <form method="POST" action="{{ route('comments.store') }}" class="mt-4 rounded-2xl bg-white/5 border border-white/10 p-6">
                    @csrf
                    <input type="hidden" name="commentable_type" value="{{ get_class($page) }}">
                    <input type="hidden" name="commentable_id" value="{{ $page->id }}">
                    <textarea name="body" rows="3" required placeholder="Write a comment…" class="w-full rounded-lg bg-white/5 border border-white/10 px-4 py-3 text-sm focus:outline-none focus:border-emerald-500/50">{{ old('body') }}</textarea>
                    <div class="mt-3 flex justify-end">
                        <button type="submit" class="rounded-lg bg-emerald-500 hover:bg-emerald-400 px-5 py-2 text-sm font-semibold text-white transition">Comment</button>
                    </div>
                </form>
            @else
                <p class="mt-4 rounded-2xl bg-white/5 border border-white/10 p-6 text-sm text-slate-400">
                    <a href="{{ route('login') }}" class="text-emerald-400 hover:underline">Log in</a> to leave a comment.
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
                                <form method="POST" action="{{ route('comments.destroy', $comment) }}" onsubmit="return confirm('Delete this comment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-300">Delete</button>
                                </form>
                            @endif
                        </div>
                        <p class="mt-3 text-sm leading-relaxed text-slate-300">{{ $comment->body }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No comments yet. Be the first!</p>
                @endforelse
            </div>
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