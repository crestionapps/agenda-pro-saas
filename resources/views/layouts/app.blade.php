<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AgendaPro')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        coral: { 50: '#fdf2f4', 100: '#fce7eb', 200: '#f9d0d9', 300: '#f4a9b9', 400: '#ec7894', 500: '#e04f73', 600: '#c9365a', 700: '#ab2846', 800: '#8f243d', 900: '#782238' },
                    }
                }
            }
        }
    </script>
    @stack('head')
</head>
<body class="min-h-screen bg-gray-50 antialiased">

    <nav class="bg-white shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="text-2xl font-bold tracking-tight">
                        <span class="text-coral-500">Agenda</span><span class="text-gray-800">Pro</span>
                    </a>
                </div>

                <div class="hidden sm:flex sm:items-center sm:space-x-8">
                    <a href="{{ url('/') }}" class="text-gray-600 hover:text-coral-500 font-medium transition text-sm">Início</a>

                    @guest
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-coral-500 font-medium transition text-sm">Entrar</a>
                        <a href="{{ route('register') }}" class="bg-coral-500 text-white px-5 py-2 rounded-full text-sm font-semibold hover:bg-coral-600 transition shadow-sm">Criar Conta</a>
                    @else
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-coral-500 font-medium text-sm transition">
                                <span class="w-8 h-8 rounded-full bg-coral-100 text-coral-600 flex items-center justify-center text-sm font-semibold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </span>
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50" style="display: none;">
                                <a href="{{ url('/dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-coral-50 hover:text-coral-600">Minha Conta</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-coral-50 hover:text-coral-600">Sair</button>
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>

                <div class="flex items-center sm:hidden">
                    <button id="mobile-menu-btn" class="text-gray-600 hover:text-coral-500 transition p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden sm:hidden border-t border-gray-100 bg-white">
            <div class="px-4 py-3 space-y-2">
                <a href="{{ url('/') }}" class="block px-3 py-2 text-gray-600 hover:text-coral-500 font-medium rounded-lg hover:bg-coral-50 transition">Início</a>
                @guest
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-gray-600 hover:text-coral-500 font-medium rounded-lg hover:bg-coral-50 transition">Entrar</a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 text-center bg-coral-500 text-white rounded-full font-semibold hover:bg-coral-600 transition">Criar Conta</a>
                @else
                    <a href="{{ url('/dashboard') }}" class="block px-3 py-2 text-gray-600 hover:text-coral-500 font-medium rounded-lg hover:bg-coral-50 transition">Minha Conta</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 text-gray-600 hover:text-coral-500 font-medium rounded-lg hover:bg-coral-50 transition">Sair</button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            var menu = document.getElementById('mobile-menu');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>