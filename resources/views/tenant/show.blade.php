@extends('layouts.app')

@section('title', $tenant->name . ' - AgendaPro')

@section('content')
    {{-- Banner --}}
    <section class="h-48 md:h-64 bg-gradient-to-r from-coral-500 to-rose-500 relative overflow-hidden">
        @if($tenant->banner)
            <img src="{{ asset('storage/' . $tenant->banner) }}" alt="" class="w-full h-full object-cover">
        @endif
    </section>

    {{-- Business Card --}}
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 relative z-10">
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-5">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-coral-100 to-rose-100 flex items-center justify-center text-3xl font-bold text-coral-500 shadow-md flex-shrink-0 overflow-hidden border-2 border-white">
                        @if($tenant->logo)
                            <img src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->name }}" class="w-full h-full object-cover">
                        @else
                            {{ substr($tenant->name, 0, 1) }}
                        @endif
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">{{ $tenant->name }}</h1>
                        <p class="text-gray-400 text-sm mt-1 flex items-center">
                            <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $tenant->address ?? 'Endereço não informado' }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('booking', $tenant->slug) }}" class="inline-block bg-coral-500 hover:bg-coral-600 text-white font-semibold text-center px-8 py-3.5 rounded-xl transition shadow-md whitespace-nowrap">
                    Agendar agora
                </a>
            </div>
            @if($tenant->description)
                <p class="text-gray-600 mt-5 text-sm leading-relaxed">{{ $tenant->description }}</p>
            @endif
        </div>
    </section>

    {{-- Tabs --}}
    @php $currentTab = request('tab', 'servicos'); @endphp
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="flex gap-2 overflow-x-auto pb-1 mb-8">
            <a href="{{ route('tenant.show', ['slug' => $tenant->slug, 'tab' => 'servicos']) }}" class="px-5 py-2.5 rounded-full text-sm font-medium transition whitespace-nowrap @if($currentTab == 'servicos') bg-coral-500 text-white shadow-sm @else bg-white text-gray-600 hover:bg-gray-100 border border-gray-200 @endif">
                Serviços
            </a>
            <a href="{{ route('tenant.show', ['slug' => $tenant->slug, 'tab' => 'profissionais']) }}" class="px-5 py-2.5 rounded-full text-sm font-medium transition whitespace-nowrap @if($currentTab == 'profissionais') bg-coral-500 text-white shadow-sm @else bg-white text-gray-600 hover:bg-gray-100 border border-gray-200 @endif">
                Profissionais
            </a>
            <a href="{{ route('tenant.show', ['slug' => $tenant->slug, 'tab' => 'horarios']) }}" class="px-5 py-2.5 rounded-full text-sm font-medium transition whitespace-nowrap @if($currentTab == 'horarios') bg-coral-500 text-white shadow-sm @else bg-white text-gray-600 hover:bg-gray-100 border border-gray-200 @endif">
                Horários
            </a>
            <a href="{{ route('tenant.show', ['slug' => $tenant->slug, 'tab' => 'avaliacoes']) }}" class="px-5 py-2.5 rounded-full text-sm font-medium transition whitespace-nowrap @if($currentTab == 'avaliacoes') bg-coral-500 text-white shadow-sm @else bg-white text-gray-600 hover:bg-gray-100 border border-gray-200 @endif">
                Avaliações
            </a>
        </div>

        {{-- Tab: Serviços --}}
        @if($currentTab == 'servicos')
            @php $grouped = $tenant->services->groupBy('category'); @endphp
            @if(count($grouped))
                @foreach($grouped as $category => $services)
                    <div class="mb-10">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-xl">📌</span>
                            <h3 class="text-lg font-semibold text-gray-800">{{ $category ?: 'Geral' }}</h3>
                        </div>
                        <div class="space-y-3">
                            @foreach($services as $service)
                                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-800">{{ $service->name }}</h4>
                                            <div class="flex items-center gap-3 mt-2 text-sm text-gray-400">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    {{ $service->duration }} min
                                                </span>
                                                @if($service->professionals && count($service->professionals))
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                        {{ implode(', ', $service->professionals->pluck('name')->toArray()) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="text-lg font-bold text-coral-500 whitespace-nowrap ml-4">
                                            R$ {{ number_format($service->price, 2, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-16 text-gray-400">
                    <span class="text-5xl block mb-4">📋</span>
                    <p class="text-lg">Nenhum serviço disponível</p>
                </div>
            @endif

        {{-- Tab: Profissionais --}}
        @elseif($currentTab == 'profissionais')
            @if(count($tenant->professionals))
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($tenant->professionals as $professional)
                        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 text-center hover:shadow-md transition">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-coral-100 to-rose-100 flex items-center justify-center text-xl font-bold text-coral-500 mx-auto mb-3">
                                @if($professional->photo)
                                    <img src="{{ asset('storage/' . $professional->photo) }}" alt="" class="w-full h-full object-cover rounded-full">
                                @else
                                    {{ substr($professional->name, 0, 1) }}
                                @endif
                            </div>
                            <h4 class="font-semibold text-gray-800">{{ $professional->name }}</h4>
                            @if($professional->email)
                                <p class="text-sm text-gray-400 mt-1">{{ $professional->email }}</p>
                            @endif
                            @if($professional->phone)
                                <p class="text-sm text-gray-400">{{ $professional->phone }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 text-gray-400">
                    <span class="text-5xl block mb-4">👥</span>
                    <p class="text-lg">Nenhum profissional cadastrado</p>
                </div>
            @endif

        {{-- Tab: Horários --}}
        @elseif($currentTab == 'horarios')
            @php
                $days = ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado', 'Domingo'];
                $hours = $tenant->businessHours ?? collect();
            @endphp
            @if(count($hours))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    @foreach($days as $day)
                        @php $bh = $hours->firstWhere('day', $day); @endphp
                        <div class="flex items-center justify-between px-6 py-4 @if(!$loop->last) border-b border-gray-100 @endif">
                            <span class="font-medium text-gray-700">{{ $day }}</span>
                            @if($bh && $bh->is_open)
                                <span class="text-sm px-4 py-1.5 rounded-full bg-green-50 text-green-700 font-medium">
                                    {{ \Carbon\Carbon::parse($bh->open_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($bh->close_time)->format('H:i') }}
                                </span>
                            @else
                                <span class="text-sm px-4 py-1.5 rounded-full bg-red-50 text-red-500 font-medium">
                                    Fechado
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 text-gray-400">
                    <span class="text-5xl block mb-4">🕐</span>
                    <p class="text-lg">Horários não informados</p>
                </div>
            @endif

        {{-- Tab: Avaliações --}}
        @elseif($currentTab == 'avaliacoes')
            @if(count($tenant->reviews))
                <div class="space-y-4">
                    @foreach($tenant->reviews as $review)
                        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-full bg-coral-100 text-coral-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
                                    {{ substr($review->customer->name ?? 'A', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">{{ $review->customer->name ?? 'Anônimo' }}</p>
                                    <div class="flex items-center text-yellow-400 text-sm">
                                        @for($i = 1; $i <= 5; $i++)
                                            ★
                                        @endfor
                                        <span class="text-gray-400 ml-1 text-xs">{{ $review->rating ?? 0 }}/5</span>
                                    </div>
                                </div>
                            </div>
                            @if($review->comment)
                                <p class="text-gray-600 text-sm leading-relaxed">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 text-gray-400">
                    <span class="text-5xl block mb-4">💬</span>
                    <p class="text-lg">Sem avaliações</p>
                    <p class="text-sm mt-2">Seja o primeiro a avaliar!</p>
                </div>
            @endif
        @endif
    </section>

    {{-- CTA Footer --}}
    <section class="bg-gradient-to-r from-coral-500 to-rose-500 text-white mt-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
            <h2 class="text-2xl md:text-3xl font-bold mb-4">Pronto para marcar com {{ $tenant->name }}?</h2>
            <p class="text-white/80 mb-6">Escolha o horário ideal e garanta o seu atendimento.</p>
            <a href="{{ route('booking', $tenant->slug) }}" class="inline-block bg-white text-coral-600 font-semibold px-8 py-3.5 rounded-full shadow-lg hover:bg-gray-50 transition">
                Agendar agora
            </a>
        </div>
    </section>
@endsection
