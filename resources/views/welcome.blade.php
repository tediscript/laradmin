<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laradmin') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .grid-bg {
                background-image:
                    linear-gradient(rgba(55, 65, 81, 0.04) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(55, 65, 81, 0.04) 1px, transparent 1px);
                background-size: 60px 60px;
            }
            .grid-bg::before {
                content: '';
                position: absolute;
                inset: 0;
                background: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(55, 65, 81, 0.08), transparent);
                pointer-events: none;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="relative min-h-screen grid-bg overflow-hidden bg-gray-100">
            <!-- Nav -->
            <header class="relative z-10 flex items-center justify-between px-6 py-5 sm:px-10">
                <div class="flex items-center gap-2.5">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-900 text-white">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.212-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 tracking-tight">{{ config('app.name', 'Laradmin') }}</span>
                </div>

                @if (Route::has('login'))
                    <nav class="flex items-center gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-gray-900 text-white hover:bg-gray-800 transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-gray-900 text-white hover:bg-gray-800 transition-colors">
                                    Get Started
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </header>

            <!-- Hero -->
            <main class="relative z-10 flex flex-col items-center justify-center px-6 pt-16 pb-20 sm:pt-24 sm:pb-28 text-center">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-3.5 py-1 text-xs font-medium text-gray-600 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-700"></span>
                    Laravel {{ app()->version() }} · Open Source
                </div>

                <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold tracking-tight text-gray-900 max-w-3xl leading-[1.1]">
                    Build admin panels<br>
                    <span class="text-gray-700">without the boilerplate</span>
                </h1>

                <p class="mt-5 text-base sm:text-lg text-gray-500 max-w-xl leading-relaxed">
                    Laradmin is a reusable Laravel starter kit for admin-heavy web applications. Fork, configure, ship.
                </p>

                <div class="mt-8 flex flex-col sm:flex-row items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-6 py-3 text-sm font-medium rounded-lg bg-gray-900 text-white hover:bg-gray-800 transition-colors shadow-lg shadow-gray-900/10">
                                Go to Dashboard
                                <svg class="ml-2 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                            </a>
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 text-sm font-medium rounded-lg bg-gray-900 text-white hover:bg-gray-800 transition-colors shadow-lg shadow-gray-900/10">
                                    Get Started
                                    <svg class="ml-2 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                                </a>
                            @endif
                            <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 text-sm font-medium rounded-lg border border-gray-200 text-gray-700 hover:bg-white transition-colors">
                                Log in
                            </a>
                        @endauth
                    @else
                        <a href="https://github.com/tediscript/laradmin" target="_blank" class="inline-flex items-center px-6 py-3 text-sm font-medium rounded-lg bg-gray-900 text-white hover:bg-gray-800 transition-colors shadow-lg shadow-gray-900/10">
                            View on GitHub
                            <svg class="ml-2 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                        </a>
                    @endif
                </div>

                <!-- Feature cards -->
                <div class="mt-16 grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-2xl w-full">
                    <div class="rounded-xl border border-gray-200 bg-white/80 backdrop-blur-sm p-5 text-left shadow-sm">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center mb-3">
                            <svg class="w-4 h-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" /></svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900">Blade + Alpine.js</h3>
                        <p class="mt-1 text-xs text-gray-500 leading-relaxed">Server-rendered with sprinkles of reactivity. No SPA complexity.</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white/80 backdrop-blur-sm p-5 text-left shadow-sm">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center mb-3">
                            <svg class="w-4 h-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900">Auth Built-in</h3>
                        <p class="mt-1 text-xs text-gray-500 leading-relaxed">Laravel Breeze pre-installed. Login, register, password reset ready.</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white/80 backdrop-blur-sm p-5 text-left shadow-sm">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center mb-3">
                            <svg class="w-4 h-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900">Rapid Dev</h3>
                        <p class="mt-1 text-xs text-gray-500 leading-relaxed">Tailwind CSS, Pest testing, Vite. Everything configured, nothing to set up.</p>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="relative z-10 text-center pb-8">
                <p class="text-xs text-gray-400">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laradmin') }} ·
                    <a href="https://github.com/tediscript/laradmin" target="_blank" class="underline underline-offset-2 hover:text-gray-600 transition-colors">GitHub</a>
                </p>
            </footer>
        </div>
    </body>
</html>