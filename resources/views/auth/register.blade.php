@extends('layouts.app')

@section('title', 'Criar Conta - AgendaPro')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center px-4 sm:px-6 py-12">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 sm:p-10">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Criar Conta</h1>
                <p class="text-gray-500 text-sm mt-2">Comece a gerir os seus agendamentos</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-coral-400 focus:ring-2 focus:ring-coral-100 outline-none transition @error('name') border-red-400 @enderror"
                            placeholder="O seu nome">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-coral-400 focus:ring-2 focus:ring-coral-100 outline-none transition @error('email') border-red-400 @enderror"
                            placeholder="seu@email.com">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-coral-400 focus:ring-2 focus:ring-coral-100 outline-none transition @error('password') border-red-400 @enderror"
                            placeholder="••••••••">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password-confirm" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Password</label>
                        <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-coral-400 focus:ring-2 focus:ring-coral-100 outline-none transition"
                            placeholder="••••••••">
                    </div>

                    <button type="submit"
                        class="w-full bg-coral-500 text-white py-3 rounded-xl font-semibold hover:bg-coral-600 focus:ring-4 focus:ring-coral-200 transition shadow-sm">
                        Criar Conta
                    </button>
                </div>
            </form>

            <p class="text-center text-sm text-gray-500 mt-8">
                Já tem conta?
                <a href="{{ route('login') }}" class="text-coral-500 hover:text-coral-600 font-semibold transition">Entrar</a>
            </p>
        </div>
    </div>
</div>
@endsection