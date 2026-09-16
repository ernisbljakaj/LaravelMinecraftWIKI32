@extends('layouts.auth')

@section('title', 'Registrieren – Minecraft Wiki & Server')

@section('auth-card')
    <h1 class="text-2xl font-bold">Konto erstellen</h1>
    <p class="mt-2 text-sm text-slate-400">Registriere dich kostenlos und werde Teil der Community.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg bg-red-500/10 border border-red-500/30 p-4 text-sm text-red-300">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-slate-300">Benutzername</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="mt-1.5 w-full rounded-lg bg-white/5 border border-white/10 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500/50" placeholder="Dein Spielername">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-300">E-Mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="mt-1.5 w-full rounded-lg bg-white/5 border border-white/10 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500/50" placeholder="du@beispiel.com">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-300">Passwort</label>
            <input id="password" type="password" name="password" required class="mt-1.5 w-full rounded-lg bg-white/5 border border-white/10 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500/50" placeholder="Mindestens 8 Zeichen">
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-300">Passwort bestätigen</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="mt-1.5 w-full rounded-lg bg-white/5 border border-white/10 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500/50" placeholder="Passwort wiederholen">
        </div>

        <button type="submit" class="w-full rounded-lg bg-emerald-500 hover:bg-emerald-400 px-4 py-3 text-sm font-semibold text-white transition">
            Registrieren
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-400">
        Schon registriert?
        <a href="{{ route('login') }}" class="text-emerald-400 hover:underline font-medium">Anmelden</a>
    </p>
@endsection