@extends('layouts.app')

@section('title', 'Minha Conta')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Minha Conta</h1>
    <p class="text-gray-400 mb-8">Gerencie seus agendamentos</p>

    @php
        $upcoming = $appointments->filter(fn($a) => \Carbon\Carbon::parse($a->date . ' ' . ($a->time ?? '00:00'))->isFuture());
        $past = $appointments->filter(fn($a) => \Carbon\Carbon::parse($a->date . ' ' . ($a->time ?? '00:00'))->isPast());
    @endphp

    {{-- Upcoming --}}
    <section class="mb-10">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-semibold text-gray-800">Próximos Agendamentos</h2>
            <span class="text-sm text-gray-400">{{ $upcoming->count() }} {{ $upcoming->count() === 1 ? 'agendamento' : 'agendamentos' }}</span>
        </div>

        @forelse($upcoming as $appointment)
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 mb-3 hover:shadow-md transition">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-semibold text-gray-800">{{ $appointment->tenant->name ?? '--' }}</span>
                            @php
                                $statusColors = ['scheduled' => 'bg-blue-50 text-blue-700', 'confirmed' => 'bg-green-50 text-green-700', 'completed' => 'bg-gray-100 text-gray-600', 'cancelled' => 'bg-red-50 text-red-600', 'no_show' => 'bg-yellow-50 text-yellow-700'];
                                $statusLabels = ['scheduled' => 'Agendado', 'confirmed' => 'Confirmado', 'completed' => 'Concluído', 'cancelled' => 'Cancelado', 'no_show' => 'Não Compareceu'];
                            @endphp
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $statusColors[$appointment->status] ?? 'bg-gray-50 text-gray-600' }}">
                                {{ $statusLabels[$appointment->status] ?? $appointment->status }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-x-6 gap-y-1 text-sm text-gray-500">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                                {{ $appointment->service->name ?? '--' }}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $appointment->professional->name ?? '--' }}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $appointment->time ? \Carbon\Carbon::parse($appointment->time)->format('H:i') : '--' }}
                            </span>
                        </div>
                    </div>
                    @if(in_array($appointment->status, ['scheduled', 'confirmed']))
                        <form method="POST" action="{{ route('appointments.cancel', $appointment->id) }}" onsubmit="return confirm('Tem certeza que deseja cancelar?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-600 border border-red-200 hover:border-red-300 px-4 py-2 rounded-lg transition whitespace-nowrap">Cancelar</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-white rounded-xl border border-gray-100">
                <span class="text-5xl block mb-4">📅</span>
                <p class="text-lg text-gray-400 mb-2">Nenhum agendamento encontrado</p>
                <a href="{{ url('/') }}" class="inline-block mt-2 text-coral-500 hover:text-coral-600 font-medium">Agendar agora</a>
            </div>
        @endforelse
    </section>

    {{-- History --}}
    <section>
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-semibold text-gray-800">Histórico</h2>
            <span class="text-sm text-gray-400">{{ $past->count() }} {{ $past->count() === 1 ? 'agendamento' : 'agendamentos' }}</span>
        </div>

        @forelse($past as $appointment)
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 mb-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-semibold text-gray-800">{{ $appointment->tenant->name ?? '--' }}</span>
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $statusColors[$appointment->status] ?? 'bg-gray-50 text-gray-600' }}">
                                {{ $statusLabels[$appointment->status] ?? $appointment->status }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-x-6 gap-y-1 text-sm text-gray-500">
                            <span>{{ $appointment->service->name ?? '--' }}</span>
                            <span>{{ $appointment->professional->name ?? '--' }}</span>
                            <span>{{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}</span>
                            <span>{{ $appointment->time ? \Carbon\Carbon::parse($appointment->time)->format('H:i') : '--' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-white rounded-xl border border-gray-100">
                <span class="text-5xl block mb-4">📋</span>
                <p class="text-lg text-gray-400 mb-2">Nenhum histórico</p>
                <a href="{{ url('/') }}" class="inline-block mt-2 text-coral-500 hover:text-coral-600 font-medium">Agendar agora</a>
            </div>
        @endforelse
    </section>
</div>
@endsection
