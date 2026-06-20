@extends('layouts.app')

@section('title', 'Dashboard - ' . $tenant->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $tenant->name }}</h1>
    <p class="text-gray-400 mb-8">Painel de Administração</p>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400 font-medium">Profissionais</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ count($professionals) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-coral-50 flex items-center justify-center text-coral-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400 font-medium">Serviços</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ count($services) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-coral-50 flex items-center justify-center text-coral-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400 font-medium">Agendamentos Hoje</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ count($todayAppointments) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-coral-50 flex items-center justify-center text-coral-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400 font-medium">Avaliações</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $tenant->reviews_count ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-coral-50 flex items-center justify-center text-coral-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Today's Appointments --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-800">Agendamentos de Hoje</h2>
                </div>
                @if(count($todayAppointments))
                    <div class="divide-y divide-gray-100">
                        @foreach($todayAppointments->sortBy('time') as $appointment)
                            <div class="px-6 py-4 flex items-center gap-4">
                                <div class="text-sm font-bold text-coral-500 w-12 flex-shrink-0">{{ $appointment->time ? \Carbon\Carbon::parse($appointment->time)->format('H:i') : '--' }}</div>
                                <div class="w-9 h-9 rounded-full bg-coral-100 text-coral-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                    {{ substr($appointment->customer->name ?? 'A', 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 text-sm">{{ $appointment->customer->name ?? '--' }}</p>
                                    <p class="text-xs text-gray-400">{{ $appointment->service->name ?? '--' }} - {{ $appointment->professional->name ?? '--' }}</p>
                                </div>
                                @php
                                    $sc = ['scheduled' => 'bg-blue-50 text-blue-700', 'confirmed' => 'bg-green-50 text-green-700', 'completed' => 'bg-gray-100 text-gray-600', 'cancelled' => 'bg-red-50 text-red-600', 'no_show' => 'bg-yellow-50 text-yellow-700'];
                                    $sl = ['scheduled' => 'Agendado', 'confirmed' => 'Confirmado', 'completed' => 'Concluído', 'cancelled' => 'Cancelado', 'no_show' => 'Não Compareceu'];
                                @endphp
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $sc[$appointment->status] ?? 'bg-gray-50 text-gray-600' }}">
                                    {{ $sl[$appointment->status] ?? $appointment->status }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 text-gray-400">
                        <p class="text-sm">Nenhum agendamento para hoje</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="space-y-4">
            <a href="#professionals-section" onclick="document.getElementById('professionals-section').scrollIntoView({behavior:'smooth'})" class="block bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-coral-50 flex items-center justify-center text-coral-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Gerir Profissionais</p>
                        <p class="text-sm text-gray-400">{{ count($professionals) }} cadastrados</p>
                    </div>
                </div>
            </a>
            <a href="#services-section" onclick="document.getElementById('services-section').scrollIntoView({behavior:'smooth'})" class="block bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-coral-50 flex items-center justify-center text-coral-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Gerir Serviços</p>
                        <p class="text-sm text-gray-400">{{ count($services) }} cadastrados</p>
                    </div>
                </div>
            </a>
            <a href="#hours-section" onclick="document.getElementById('hours-section').scrollIntoView({behavior:'smooth'})" class="block bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-coral-50 flex items-center justify-center text-coral-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Horários de Funcionamento</p>
                        <p class="text-sm text-gray-400">Definir dias e horários</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Professionals Management --}}
    <section id="professionals-section" class="mt-12">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-semibold text-gray-800">Profissionais</h2>
            <button onclick="document.getElementById('professional-modal').classList.remove('hidden')" class="bg-coral-500 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-coral-600 transition shadow-sm">Adicionar</button>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @if(count($professionals))
                <div class="divide-y divide-gray-100">
                    @foreach($professionals as $professional)
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-coral-100 text-coral-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
                                    @if($professional->photo)
                                        <img src="{{ asset('storage/' . $professional->photo) }}" alt="" class="w-full h-full object-cover rounded-full">
                                    @else
                                        {{ substr($professional->name, 0, 1) }}
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 text-sm">{{ $professional->name }}</p>
                                    @if($professional->email)
                                        <p class="text-xs text-gray-400">{{ $professional->email }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button onclick="openEditProfessional({{ $professional->id }}, '{{ $professional->name }}', '{{ $professional->email }}', '{{ $professional->phone }}')" class="text-xs font-medium px-3 py-1.5 rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-100 transition border border-gray-200">Editar</button>
                                <form method="POST" action="{{ route('professionals.destroy', $professional->id) }}" onsubmit="return confirm('Tem certeza?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium px-3 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition border border-red-200">Excluir</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-gray-400">
                    <p class="text-sm">Nenhum profissional cadastrado</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Services Management --}}
    <section id="services-section" class="mt-12">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-semibold text-gray-800">Serviços</h2>
            <button onclick="document.getElementById('service-modal').classList.remove('hidden')" class="bg-coral-500 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-coral-600 transition shadow-sm">Adicionar</button>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @if(count($services))
                <div class="divide-y divide-gray-100">
                    @foreach($services as $service)
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-800 text-sm">{{ $service->name }}</p>
                                <p class="text-xs text-gray-400">{{ $service->duration }} min - R$ {{ number_format($service->price, 2, ',', '.') }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button onclick="openEditService({{ $service->id }}, '{{ $service->name }}', '{{ $service->category }}', {{ $service->duration }}, {{ $service->price }})" class="text-xs font-medium px-3 py-1.5 rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-100 transition border border-gray-200">Editar</button>
                                <form method="POST" action="{{ route('services.destroy', $service->id) }}" onsubmit="return confirm('Tem certeza?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium px-3 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition border border-red-200">Excluir</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-gray-400">
                    <p class="text-sm">Nenhum serviço cadastrado</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Business Hours --}}
    <section id="hours-section" class="mt-12 mb-10">
        <h2 class="text-lg font-semibold text-gray-800 mb-5">Horários de Funcionamento</h2>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <form method="POST" action="{{ route('business-hours.update', $tenantId) }}">
                @csrf
                @method('PUT')
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left px-6 py-3 text-sm font-medium text-gray-500">Dia</th>
                            <th class="text-left px-6 py-3 text-sm font-medium text-gray-500">Aberto</th>
                            <th class="text-left px-6 py-3 text-sm font-medium text-gray-500">Abertura</th>
                            <th class="text-left px-6 py-3 text-sm font-medium text-gray-500">Fechamento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $days = ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado', 'Domingo'];
                            $hours = $tenant->businessHours ?? collect();
                        @endphp
                        @foreach($days as $day)
                            @php $bh = $hours->firstWhere('day', $day); @endphp
                            <tr class="border-b border-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-700">{{ $day }}</td>
                                <td class="px-6 py-4">
                                    <input type="hidden" name="hours[{{ $loop->index }}][day]" value="{{ $day }}">
                                    <input type="hidden" name="hours[{{ $loop->index }}][id]" value="{{ $bh->id ?? '' }}">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="hours[{{ $loop->index }}][is_open]" value="1" {{ ($bh && $bh->is_open) ? 'checked' : '' }} class="rounded border-gray-300 text-coral-500 focus:ring-coral-500">
                                    </label>
                                </td>
                                <td class="px-6 py-4">
                                    <input type="time" name="hours[{{ $loop->index }}][open_time]" value="{{ $bh && $bh->open_time ? \Carbon\Carbon::parse($bh->open_time)->format('H:i') : '08:00' }}" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-transparent">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="time" name="hours[{{ $loop->index }}][close_time]" value="{{ $bh && $bh->close_time ? \Carbon\Carbon::parse($bh->close_time)->format('H:i') : '18:00' }}" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-transparent">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-gray-100">
                    <button type="submit" class="bg-coral-500 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-coral-600 transition shadow-sm">Salvar Horários</button>
                </div>
            </form>
        </div>
    </section>
</div>

{{-- Professional Modal --}}
<div id="professional-modal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-800" id="professional-modal-title">Adicionar Profissional</h3>
            <button onclick="document.getElementById('professional-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="professional-form" method="POST" action="{{ route('professionals.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="tenant_id" value="{{ $tenantId }}">
            <input type="hidden" name="_method" id="professional-method" value="POST">
            <input type="hidden" name="id" id="professional-id" value="">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nome</label>
                    <input type="text" name="name" id="professional-name" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                    <input type="email" name="email" id="professional-email" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Telefone</label>
                    <input type="text" name="phone" id="professional-phone" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Foto</label>
                    <input type="file" name="photo" accept="image/*" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-coral-50 file:text-coral-600 hover:file:bg-coral-100 focus:outline-none">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('professional-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl text-sm font-medium border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-coral-500 text-white hover:bg-coral-600 transition shadow-sm">Salvar</button>
            </div>
        </form>
    </div>
</div>

{{-- Service Modal --}}
<div id="service-modal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-800" id="service-modal-title">Adicionar Serviço</h3>
            <button onclick="document.getElementById('service-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="service-form" method="POST" action="{{ route('services.store') }}">
            @csrf
            <input type="hidden" name="tenant_id" value="{{ $tenantId }}">
            <input type="hidden" name="_method" id="service-method" value="POST">
            <input type="hidden" name="id" id="service-id" value="">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nome</label>
                    <input type="text" name="name" id="service-name" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Categoria</label>
                    <input type="text" name="category" id="service-category" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-transparent" placeholder="Ex: Cabelo, Estética...">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Duração (min)</label>
                        <input type="number" name="duration" id="service-duration" required min="5" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Preço (R$)</label>
                        <input type="number" step="0.01" name="price" id="service-price" required min="0" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-transparent">
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('service-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl text-sm font-medium border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-coral-500 text-white hover:bg-coral-600 transition shadow-sm">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditProfessional(id, name, email, phone) {
        document.getElementById('professional-modal').classList.remove('hidden');
        document.getElementById('professional-modal-title').textContent = 'Editar Profissional';
        document.getElementById('professional-id').value = id;
        document.getElementById('professional-name').value = name;
        document.getElementById('professional-email').value = email;
        document.getElementById('professional-phone').value = phone;
        document.getElementById('professional-method').value = 'PUT';
        document.getElementById('professional-form').action = '{{ route("professionals.update", "") }}/' + id;
    }

    document.getElementById('professional-modal')?.addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });

    function openEditService(id, name, category, duration, price) {
        document.getElementById('service-modal').classList.remove('hidden');
        document.getElementById('service-modal-title').textContent = 'Editar Serviço';
        document.getElementById('service-id').value = id;
        document.getElementById('service-name').value = name;
        document.getElementById('service-category').value = category;
        document.getElementById('service-duration').value = duration;
        document.getElementById('service-price').value = price;
        document.getElementById('service-method').value = 'PUT';
        document.getElementById('service-form').action = '{{ route("services.update", "") }}/' + id;
    }

    document.getElementById('service-modal')?.addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
</script>
@endpush
