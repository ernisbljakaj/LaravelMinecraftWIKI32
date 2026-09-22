<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Login – Minecraft Wiki & Server')</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#0a0e14] text-slate-100 min-h-screen flex flex-col">
        <main class="flex-grow flex items-center justify-center p-6">
            <div class="w-full max-w-md">
                <a href="{{ route('home') }}" class="flex items-center justify-center gap-2 mb-8">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-400 to-emerald-600 grid place-items-center shadow-lg shadow-emerald-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                        </svg>
                    </div>
                    <span class="font-bold text-xl">Minecraft Wiki <span class="text-emerald-400">&</span> Server</span>
                </a>

                <div class="rounded-2xl bg-white/5 border border-white/10 p-8">
                    @yield('auth-card')
                </div>
            </div>
        </main>
    </body>
</html>