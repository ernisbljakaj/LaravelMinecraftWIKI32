@extends('layouts.auth')

@section('title', 'Anmelden – Minecraft Wiki & Server')

@section('auth-card')
    <h1 class="text-2xl font-bold">Willkommen zurück!</h1>
    <p class="mt-2 text-sm text-slate-400">Melde dich an, um Favoriten zu speichern und zu kommentieren.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg bg-red-500/10 border border-red-500/30 p-4 text-sm text-red-300">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-300">E-Mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="mt-1.5 w-full rounded-lg bg-white/5 border border-white/10 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500/50" placeholder="du@beispiel.com">
        </div>

        <div class="flex items-center justify-between">
            <label for="password" class="block text-sm font-medium text-slate-300">Passwort</label>
        </div>
        <input id="password" type="password" name="password" required class="mt-1.5 w-full rounded-lg bg-white/5 border border-white/10 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500/50" placeholder="••••••••">

        <label class="flex items-center gap-2 text-sm text-slate-400">
            <input type="checkbox" name="remember" class="rounded bg-white/5 border-white/10">
            Angemeldet bleiben
        </label>

        <button type="submit" class="w-full rounded-lg bg-emerald-500 hover:bg-emerald-400 px-4 py-3 text-sm font-semibold text-white transition">
            Anmelden
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-400">
        Noch kein Konto?
        <a href="{{ route('register') }}" class="text-emerald-400 hover:underline font-medium">Jetzt registrieren</a>
    </p>
@endsection