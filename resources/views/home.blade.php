@extends('layouts.app')

@section('title', 'AgendaPro - Encontre o seu profissional ideal')

@section('content')
    {{-- Hero Section --}}
    <section class="bg-gradient-to-r from-coral-500 to-rose-500 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight mb-4">
                    Encontre o seu profissional ideal
                </h1>
                <p class="text-lg md:text-xl text-white/80 mb-10">
                    Marque serviços de beleza e bem-estar online
                </p>
                <form action="{{ url('/') }}" method="GET" class="max-w-2xl mx-auto">
                    <div class="relative flex items-center bg-white rounded-full shadow-lg p-1">
                        <div class="flex-1 flex items-center pl-5">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Pesquisar por nome ou cidade..." class="w-full px-4 py-3 text-gray-700 placeholder-gray-400 bg-transparent border-none focus:outline-none focus:ring-0 text-sm">
                        </div>
                        <button type="submit" class="bg-coral-500 hover:bg-coral-600 text-white font-semibold px-6 py-3 rounded-full text-sm transition whitespace-nowrap mr-1">
                            Pesquisar
                        </button>
                    </div>
                </form>
                <div class="flex flex-wrap justify-center gap-2 mt-6">
                    @foreach($categories as $key => $cat)
                        <a href="{{ url('/?category=' . $key) }}" class="px-4 py-2 bg-white/15 hover:bg-white/25 backdrop-blur-sm rounded-full text-sm font-medium transition text-white border border-white/20">
                            {{ $cat['name'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Categories Section --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Categorias</h2>
            <a href="{{ url('/') }}" class="text-coral-500 hover:text-coral-600 font-medium text-sm transition">Ver todas</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($categories as $key => $cat)
                @php $emojis = ['scissors' => '✂️', 'hair' => '💇', 'spa' => '💆', 'nails' => '💅', 'aesthetics' => '✨', 'clinic' => '🏥']; @endphp
                <a href="{{ url('/?category=' . $key) }}" class="bg-white rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition border-t-4 border-coral-400 group">
                    <span class="text-4xl block mb-3">{{ $emojis[$key] ?? '📌' }}</span>
                    <h3 class="font-semibold text-gray-800 group-hover:text-coral-500 transition">{{ $cat['name'] }}</h3>
                    <p class="text-sm text-gray-400 mt-1">{{ $cat['count'] ?? 0 }} {{ $cat['count'] == 1 ? 'negócio' : 'negócios' }}</p>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Cities Section --}}
    @if(count($cities))
    <section class="bg-gray-50 border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">Cidades em destaque</h2>
            <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-thin">
                @foreach($cities as $city)
                    <a href="{{ url('/?city=' . urlencode($city)) }}" class="flex-shrink-0 px-5 py-2.5 bg-white border border-gray-200 hover:border-coral-300 hover:text-coral-600 text-gray-700 rounded-full text-sm font-medium shadow-sm hover:shadow transition @if(request('city') == $city) border-coral-500 text-coral-600 bg-coral-50 @endif">
                        {{ $city }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Featured Tenants Section --}}
    @if(count($tenants))
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Negócios em destaque</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($tenants as $tenant)
                <a href="{{ route('tenant.show', $tenant->slug) }}" class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition overflow-hidden group">
                    {{-- Banner --}}
                    <div class="h-32 bg-gradient-to-r from-coral-400 to-rose-400 relative">
                        @if($tenant->banner)
                            <img src="{{ asset('storage/' . $tenant->banner) }}" alt="" class="w-full h-full object-cover">
                        @endif
                    </div>
                    {{-- Content --}}
                    <div class="px-5 pb-5 -mt-10 relative">
                        {{-- Logo --}}
                        <div class="w-16 h-16 rounded-xl bg-white shadow-md flex items-center justify-center text-xl font-bold text-coral-500 border-2 border-white mb-3">
                            @if($tenant->logo)
                                <img src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->name }}" class="w-full h-full object-cover rounded-xl">
                            @else
                                {{ substr($tenant->name, 0, 1) }}
                            @endif
                        </div>
                        <h3 class="font-semibold text-gray-800 text-lg group-hover:text-coral-500 transition">{{ $tenant->name }}</h3>
                        <p class="text-sm text-gray-400 mt-0.5 flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $tenant->address ?? 'Sem endereço' }}
                        </p>
                        <div class="flex items-center justify-between mt-4">
                            <span class="text-xs font-medium px-3 py-1 bg-coral-50 text-coral-600 rounded-full">
                                {{ $tenant->category ?? 'Geral' }}
                            </span>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span>{{ $tenant->reviews_count ?? 0 }} {{ ($tenant->reviews_count ?? 0) == 1 ? 'avaliação' : 'avaliações' }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- CTA Section --}}
    <section class="bg-gradient-to-r from-coral-500 to-rose-500 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Pronto para marcar?</h2>
            <p class="text-white/80 text-lg mb-8 max-w-xl mx-auto">Encontre o profissional perfeito para você e agende online em segundos.</p>
            <a href="#top" class="inline-block bg-white text-coral-600 font-semibold px-8 py-3.5 rounded-full shadow-lg hover:bg-gray-50 transition">
                Começar agora
            </a>
        </div>
    </section>

    {{-- Simple Footer --}}
    <footer class="bg-gray-900 text-gray-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 text-center text-sm">
            <p>&copy; {{ date('Y') }} AgendaPro. Todos os direitos reservados.</p>
        </div>
    </footer>
@endsection
