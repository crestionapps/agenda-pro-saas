@extends('layouts.app')

@section('title', 'Painel Administrativo')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Painel Administrativo</h1>
            <p class="text-gray-400 text-sm mt-1">Visão geral da plataforma</p>
        </div>
        <button onclick="document.getElementById('tenant-modal').classList.remove('hidden')" class="bg-coral-500 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-coral-600 transition shadow-sm inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Criar Negócio
        </button>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-10">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <p class="text-sm text-gray-400 font-medium">Total Negócios</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_tenants'] ?? $stats['total_businesses'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <p class="text-sm text-gray-400 font-medium">Utilizadores</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_users'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <p class="text-sm text-gray-400 font-medium">Agendamentos</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_appointments'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <p class="text-sm text-gray-400 font-medium">Hoje</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['today_appointments'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <p class="text-sm text-gray-400 font-medium">Ativos</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['active_tenants'] ?? 0 }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Top Tenants --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Top Negócios</h2>
            </div>
            @if(count($topTenants))
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-50 text-gray-400 text-xs uppercase tracking-wider">
                                <th class="text-left px-6 py-3 font-medium">Nome</th>
                                <th class="text-center px-4 py-3 font-medium">Agendamentos</th>
                                <th class="text-center px-4 py-3 font-medium">Profissionais</th>
                                <th class="text-center px-4 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($topTenants as $tenant)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-coral-100 text-coral-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                                {{ substr($tenant->name, 0, 1) }}
                                            </div>
                                            <span class="font-medium text-gray-800">{{ $tenant->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center text-gray-600">{{ $tenant->appointments_count ?? 0 }}</td>
                                    <td class="px-4 py-4 text-center text-gray-600">{{ $tenant->professionals_count ?? 0 }}</td>
                                    <td class="px-4 py-4 text-center">
                                        @if($tenant->is_active ?? true)
                                            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-green-50 text-green-700">Ativo</span>
                                        @else
                                            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-red-50 text-red-600">Inativo</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-10 text-gray-400">
                    <p class="text-sm">Nenhum negócio cadastrado</p>
                </div>
            @endif
        </div>

        {{-- Recent Appointments --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Agendamentos Recentes</h2>
            </div>
            @if(count($recentAppointments))
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-50 text-gray-400 text-xs uppercase tracking-wider">
                                <th class="text-left px-6 py-3 font-medium">Negócio</th>
                                <th class="text-left px-4 py-3 font-medium">Cliente</th>
                                <th class="text-left px-4 py-3 font-medium">Serviço</th>
                                <th class="text-left px-4 py-3 font-medium">Data</th>
                                <th class="text-center px-4 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @php
                                $sc = ['scheduled' => 'bg-blue-50 text-blue-700', 'confirmed' => 'bg-green-50 text-green-700', 'completed' => 'bg-gray-100 text-gray-600', 'cancelled' => 'bg-red-50 text-red-600', 'no_show' => 'bg-yellow-50 text-yellow-700'];
                                $sl = ['scheduled' => 'Agendado', 'confirmed' => 'Confirmado', 'completed' => 'Concluído', 'cancelled' => 'Cancelado', 'no_show' => 'Não Compareceu'];
                            @endphp
                            @foreach($recentAppointments as $appt)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-800">{{ $appt->tenant->name ?? '--' }}</td>
                                    <td class="px-4 py-4 text-gray-600">{{ $appt->customer->name ?? '--' }}</td>
                                    <td class="px-4 py-4 text-gray-600">{{ $appt->service->name ?? '--' }}</td>
                                    <td class="px-4 py-4 text-gray-600">{{ \Carbon\Carbon::parse($appt->date)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $sc[$appt->status] ?? 'bg-gray-50 text-gray-600' }}">
                                            {{ $sl[$appt->status] ?? $appt->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-10 text-gray-400">
                    <p class="text-sm">Nenhum agendamento recente</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Create Tenant Modal --}}
<div id="tenant-modal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-800">Criar Negócio</h3>
            <button onclick="document.getElementById('tenant-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.tenants.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nome do Negócio</label>
                    <input type="text" name="name" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Slug</label>
                    <input type="text" name="slug" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-transparent" placeholder="meu-negocio">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                    <input type="email" name="email" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Endereço</label>
                    <input type="text" name="address" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Categoria</label>
                    <input type="text" name="category" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-transparent">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('tenant-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl text-sm font-medium border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-coral-500 text-white hover:bg-coral-600 transition shadow-sm">Criar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('tenant-modal')?.addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
</script>
@endpush
