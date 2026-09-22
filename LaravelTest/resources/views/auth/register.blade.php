@extends('layouts.auth')

@section('title', 'Sign up – Minecraft Wiki & Server')

@section('auth-card')
    <h1 class="text-2xl font-bold">Create an account</h1>
    <p class="mt-2 text-sm text-slate-400">Sign up for free and become part of the community.</p>

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
            <label for="name" class="block text-sm font-medium text-slate-300">Username</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="mt-1.5 w-full rounded-lg bg-white/5 border border-white/10 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500/50" placeholder="Your gamertag">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-300">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="mt-1.5 w-full rounded-lg bg-white/5 border border-white/10 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500/50" placeholder="you@example.com">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-300">Password</label>
            <input id="password" type="password" name="password" required class="mt-1.5 w-full rounded-lg bg-white/5 border border-white/10 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500/50" placeholder="At least 8 characters">
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-300">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="mt-1.5 w-full rounded-lg bg-white/5 border border-white/10 px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500/50" placeholder="Repeat your password">
        </div>

        <button type="submit" class="w-full rounded-lg bg-emerald-500 hover:bg-emerald-400 px-4 py-3 text-sm font-semibold text-white transition">
            Sign up
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-400">
        Already registered?
        <a href="{{ route('login') }}" class="text-emerald-400 hover:underline font-medium">Log in</a>
    </p>
@endsection