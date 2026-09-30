@php
    /** @var \Illuminate\Database\Eloquent\Model $commentable */
@endphp

<form method="POST" action="{{ route('comments.store') }}" class="mt-4 rounded-2xl bg-white/5 border border-white/10 p-6">
    @csrf
    <input type="hidden" name="commentable_type" value="{{ $commentable::class }}">
    <input type="hidden" name="commentable_id" value="{{ $commentable->id }}">

    <textarea name="body" rows="3" required minlength="{{ config('moderation.heuristic.min_length') }}"
        maxlength="{{ config('moderation.heuristic.max_length') }}"
        placeholder="Write a comment…"
        class="w-full rounded-lg bg-white/5 border border-white/10 px-4 py-3 text-sm focus:outline-none focus:border-emerald-500/50">{{ old('body') }}</textarea>

    <div class="mt-1 flex items-center justify-between gap-4">
        <p class="text-xs text-slate-500">
            Comments are checked automatically before they appear.
        </p>
        <p class="text-xs text-slate-500" data-comment-counter>
            <span data-comment-count>0</span>/{{ config('moderation.heuristic.max_length') }}
        </p>
    </div>

    @error('body')
        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
    @enderror

    <div class="mt-3 flex justify-end">
        <button type="submit" class="rounded-lg bg-emerald-500 hover:bg-emerald-400 px-5 py-2 text-sm font-semibold text-white transition">Comment</button>
    </div>
</form>
