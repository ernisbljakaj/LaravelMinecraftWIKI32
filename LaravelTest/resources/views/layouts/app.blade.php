<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Minecraft Wiki & Server Directory')</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#0a0e14] text-slate-100 min-h-screen flex flex-col">
        <nav class="sticky top-0 z-50 bg-[#0a0e14]/80 backdrop-blur border-b border-white/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-green-400 to-emerald-600 grid place-items-center shadow-lg shadow-emerald-500/20">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                            </svg>
                        </div>
                        <span class="font-bold text-lg tracking-tight group-hover:text-emerald-400 transition">Minecraft Wiki <span class="text-emerald-400">&</span> Server</span>
                    </a>

                    <div class="hidden md:flex items-center gap-1">
                        <a href="{{ route('servers.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('servers.*') || request()->routeIs('home') ? 'bg-white/10 text-emerald-400' : 'text-slate-300 hover:bg-white/5' }}">Server</a>
                        <a href="{{ route('wiki.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('wiki.*') ? 'bg-white/10 text-emerald-400' : 'text-slate-300 hover:bg-white/5' }}">Wiki</a>
                        @auth
                            <a href="{{ route('favorites.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('favorites.*') ? 'bg-white/10 text-emerald-400' : 'text-slate-300 hover:bg-white/5' }}">Favorites</a>
                            <a href="{{ route('servers.create') }}" class="px-4 py-2 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm font-medium hover:bg-emerald-500/20 transition">+ Submit a server</a>
                        @endauth
                    </div>

                    <div class="flex items-center gap-3">
                        @auth
                            <span class="hidden sm:inline text-sm text-slate-300">
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="w-6 h-6 rounded-full bg-gradient-to-br from-emerald-400 to-green-600 grid place-items-center text-xs font-bold text-white">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                                    {{ auth()->user()->name }}
                                </span>
                            </span>
                            @if (auth()->user()->isAdmin())
                                <a href="{{ url('/admin') }}" class="hidden sm:inline-flex px-3 py-1.5 rounded-lg text-sm font-medium text-amber-300 hover:bg-white/5 border border-amber-500/30">Admin</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-white/5">Log out</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-white/5">Log in</a>
                            <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-white text-sm font-semibold shadow-lg shadow-emerald-500/25 transition">Sign up</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        @if (session('status'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 text-sm">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        <main class="flex-grow">
            @yield('content')
        </main>

        <footer class="border-t border-white/5 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-slate-500">Minecraft Wiki & Server Directory – not affiliated with Mojang/Microsoft</p>
                <div class="flex items-center gap-6 text-sm text-slate-400">
                    <a href="{{ route('servers.index') }}" class="hover:text-emerald-400 transition">Server</a>
                    <a href="{{ route('wiki.index') }}" class="hover:text-emerald-400 transition">Wiki</a>
                </div>
            </div>
        </footer>
    </body>
</html>