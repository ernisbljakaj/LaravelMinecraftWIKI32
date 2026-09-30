@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\Comment> $comments */
    $canModerate = auth()->user()?->isAdmin() ?? false;
@endphp

<div class="mt-6 space-y-4" data-comments>
    @forelse ($comments as $comment)
        <div class="rounded-2xl bg-white/5 border border-white/10 p-6 {{ $comment->isPublished() ? '' : 'opacity-70' }}">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-green-600 grid place-items-center text-xs font-bold text-white">
                        {{ mb_strtoupper(mb_substr($comment->user?->name ?? '?', 0, 1)) }}
                    </span>
                    <div>
                        <p class="text-sm font-medium">{{ $comment->user?->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-slate-500">{{ $comment->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @unless ($comment->isPublished())
                        <span @class([
                            'rounded-full border px-2 py-0.5 text-[11px] font-medium',
                            'border-amber-500/30 bg-amber-500/10 text-amber-300' => $comment->isPending(),
                            'border-red-500/30 bg-red-500/10 text-red-300' => $comment->isRejected(),
                        ]) title="{{ $comment->moderation_reason }}">
                            {{ $comment->moderation_status->label() }}
                        </span>
                    @endunless

                    @if (auth()->id() === $comment->user_id || $canModerate)
                        <form method="POST" action="{{ route('comments.destroy', $comment) }}" onsubmit="return confirm('Delete this comment?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-300">Delete</button>
                        </form>
                    @endif
                </div>
            </div>

            <p class="mt-3 text-sm leading-relaxed text-slate-300 whitespace-pre-line">{{ $comment->body }}</p>

            @if ($canModerate && $comment->moderation_reason)
                <p class="mt-3 border-t border-white/10 pt-3 text-xs text-slate-500">
                    <span class="font-medium text-slate-400">Moderation</span>
                    <span>({{ $comment->moderation_source }})</span>
                    @if ($comment->moderation_confidence)
                        <span>· {{ number_format($comment->moderation_confidence * 100) }}% confidence</span>
                    @endif
                    <span>: {{ $comment->moderation_reason }}</span>
                </p>
            @endif
        </div>
    @empty
        <p class="text-sm text-slate-500">No comments yet. Be the first!</p>
    @endforelse
</div>
