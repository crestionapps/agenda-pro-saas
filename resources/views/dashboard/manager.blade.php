@extends('layouts.app')

@section('title', 'Agenda do Dia - ' . $tenant->name)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Agenda do Dia</h1>
            <p class="text-gray-400 text-sm mt-1">{{ $tenant->name }}</p>
        </div>
        <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" onchange="this.form.submit()" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-transparent">
            <button type="submit" class="bg-coral-500 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-coral-600 transition shadow-sm">Ir</button>
        </form>
    </div>

    @php
        $statusLabels = ['scheduled' => 'Agendado', 'confirmed' => 'Confirmado', 'completed' => 'Concluído', 'cancelled' => 'Cancelado', 'no_show' => 'Não Compareceu'];
        $statusColors = ['scheduled' => 'bg-blue-50 text-blue-700', 'confirmed' => 'bg-green-50 text-green-700', 'completed' => 'bg-gray-100 text-gray-600', 'cancelled' => 'bg-red-50 text-red-600', 'no_show' => 'bg-yellow-50 text-yellow-700'];
    @endphp

    @if(count($appointments))
        <div class="space-y-3">
            @foreach($appointments->sortBy('time') as $appointment)
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="text-center flex-shrink-0 w-16">
                                <div class="text-lg font-bold text-coral-500">{{ $appointment->time ? \Carbon\Carbon::parse($appointment->time)->format('H:i') : '--' }}</div>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-coral-100 text-coral-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
                                {{ substr($appointment->customer->name ?? 'A', 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-semibold text-gray-800">{{ $appointment->customer->name ?? '--' }}</span>
                                    <span class="text-xs font-medium px-2.5 py-0.5 rounded-full {{ $statusColors[$appointment->status] ?? 'bg-gray-50 text-gray-600' }}">
                                        {{ $statusLabels[$appointment->status] ?? $appointment->status }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-500 mt-0.5 flex flex-wrap gap-x-4">
                                    <span>{{ $appointment->service->name ?? '--' }}</span>
                                    <span class="text-gray-400">|</span>
                                    <span>{{ $appointment->professional->name ?? '--' }}</span>
                                    @if($appointment->notes)
                                        <span class="text-gray-400">|</span>
                                        <span class="text-gray-400 italic">{{ $appointment->notes }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2 flex-shrink-0">
                            @if($appointment->status === 'scheduled')
                                <form method="POST" action="{{ route('appointments.status', $appointment->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="text-xs font-medium px-3 py-2 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 transition border border-green-200">Confirmar</button>
                                </form>
                            @endif
                            @if(in_array($appointment->status, ['scheduled', 'confirmed']))
                                <form method="POST" action="{{ route('appointments.status', $appointment->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="text-xs font-medium px-3 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition border border-gray-200">Concluir</button>
                                </form>
                            @endif
                            @if(in_array($appointment->status, ['scheduled', 'confirmed']))
                                <form method="POST" action="{{ route('appointments.status', $appointment->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="text-xs font-medium px-3 py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition border border-red-200">Cancelar</button>
                                </form>
                            @endif
                            @if(in_array($appointment->status, ['scheduled', 'confirmed']))
                                <form method="POST" action="{{ route('appointments.status', $appointment->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="no_show">
                                    <button type="submit" class="text-xs font-medium px-3 py-2 rounded-lg bg-yellow-50 text-yellow-700 hover:bg-yellow-100 transition border border-yellow-200">No Show</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-20 bg-white rounded-xl border border-gray-100">
            <span class="text-5xl block mb-4">📅</span>
            <p class="text-lg text-gray-400 mb-1">Nenhum agendamento para esta data</p>
            <p class="text-sm text-gray-400">Selecione outra data no calendário acima</p>
        </div>
    @endif
</div>
@endsection
