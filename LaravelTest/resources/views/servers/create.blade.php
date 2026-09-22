@extends('layouts.app')

@section('title', 'Submit a server – Minecraft Wiki & Server')

@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold tracking-tight">Submit a <span class="text-emerald-400">server</span></h1>
        <p class="mt-2 text-slate-400">After submission, an admin will review your server before it appears in the list.</p>

        @if ($errors->any())
            <div class="mt-6 rounded-lg bg-red-500/10 border border-red-500/30 p-4 text-sm text-red-300">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('servers.store') }}" class="mt-8 space-y-6 rounded-2xl bg-white/5 border border-white/10 p-8">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-300">Name *</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required class="mt-1.5 w-full rounded-lg bg-white/5 border border-white/10 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500/50" placeholder="e.g. Craftland">
            </div>

            <div>
                <label for="ip" class="block text-sm font-medium text-slate-300">Server address (IP) *</label>
                <input id="ip" type="text" name="ip" value="{{ old('ip') }}" required class="mt-1.5 w-full rounded-lg bg-white/5 border border-white/10 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500/50" placeholder="play.example.com">
            </div>

            <div class="grid sm:grid-cols-2 gap-6">
                <div>
                    <label for="version" class="block text-sm font-medium text-slate-300">Minecraft version *</label>
                    <input id="version" type="text" name="version" value="{{ old('version', '1.21') }}" required class="mt-1.5 w-full rounded-lg bg-white/5 border border-white/10 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500/50" placeholder="1.21">
                </div>
                <div>
                    <label for="mode" class="block text-sm font-medium text-slate-300">Game mode *</label>
                    <select id="mode" name="mode" required class="mt-1.5 w-full rounded-lg bg-white/5 border border-white/10 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500/50">
                        <option value="Survival" @selected(old('mode') === 'Survival')>Survival</option>
                        <option value="Creative" @selected(old('mode') === 'Creative')>Creative</option>
                        <option value="PvP" @selected(old('mode') === 'PvP')>PvP</option>
                        <option value="Skyblock" @selected(old('mode') === 'Skyblock')>Skyblock</option>
                        <option value="Bedwars" @selected(old('mode') === 'Bedwars')>Bedwars</option>
                        <option value="Minigames" @selected(old('mode') === 'Minigames')>Minigames</option>
                        <option value="Anarchy" @selected(old('mode') === 'Anarchy')>Anarchy</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-300">Description *</label>
                <textarea id="description" name="description" rows="5" required class="mt-1.5 w-full rounded-lg bg-white/5 border border-white/10 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500/50" placeholder="What makes your server special? What kind of players are you looking for?">{{ old('description') }}</textarea>
            </div>

            <div>
                <span class="block text-sm font-medium text-slate-300">Tags</span>
                <div class="mt-3 flex flex-wrap gap-3">
                    @foreach ($tags as $tag)
                        <label class="inline-flex items-center gap-2 text-sm text-slate-300">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}" @checked(is_array(old('tags')) && in_array($tag->id, old('tags'))) class="rounded bg-white/5 border-white/10">
                            {{ $tag->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="w-full rounded-lg bg-emerald-500 hover:bg-emerald-400 px-4 py-3 text-sm font-semibold text-white transition">
                Submit server
            </button>
        </form>
    </div>
@endsection