@extends('layouts.app')

@section('title', 'Gerir Profissionais')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Gerir Profissionais</h1>
        <button data-modal-toggle="addProfessionalModal" class="btn-primary">
            + Adicionar Profissional
        </button>
    </div>

    @if(session('success'))
        <div data-flash class="mb-4 p-4 rounded-xl bg-green-100 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Nome</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Telefone</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Serviços</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($professionals as $professional)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $professional->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $professional->email }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $professional->phone }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($professional->services as $service)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-coral-100 text-coral-700">{{ $service->name }}</span>
                                    @empty
                                        <span class="text-gray-400 text-sm">—</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button data-modal-toggle="editProfessionalModal-{{ $professional->id }}" class="text-sm font-medium text-coral-500 hover:text-coral-700">Editar</button>
                                    <form action="{{ route('tenant.professionals.destroy', [$tenantId, $professional->id]) }}" method="POST" onsubmit="return confirm('Tem a certeza?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-700">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">Nenhum profissional encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Professional Modal --}}
<div id="addProfessionalModal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">Adicionar Profissional</h2>
            <button data-modal-close class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form action="{{ route('tenant.professionals.store', $tenantId) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                <input type="text" name="name" required class="input-field" placeholder="Nome do profissional">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" required class="input-field" placeholder="email@exemplo.pt">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                <input type="text" name="phone" required class="input-field" placeholder="+351 900 000 000">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Biografia</label>
                <textarea name="bio" rows="3" class="input-field" placeholder="Breve descrição..."></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Serviços</label>
                <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto">
                    @foreach($services ?? [] as $service)
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="services[]" value="{{ $service->id }}" class="rounded border-gray-300 text-coral-500 focus:ring-coral-400">
                            {{ $service->name }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" data-modal-close class="btn-secondary">Cancelar</button>
                <button type="submit" class="btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Professional Modals --}}
@foreach($professionals as $professional)
    <div id="editProfessionalModal-{{ $professional->id }}" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Editar Profissional</h2>
                <button data-modal-close class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <form action="{{ route('tenant.professionals.update', [$tenantId, $professional->id]) }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                    <input type="text" name="name" value="{{ $professional->name }}" required class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ $professional->email }}" required class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                    <input type="text" name="phone" value="{{ $professional->phone }}" required class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biografia</label>
                    <textarea name="bio" rows="3" class="input-field">{{ $professional->bio }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Serviços</label>
                    <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto">
                        @foreach($services ?? [] as $service)
                            <label class="flex items-center gap-2 text-sm text-gray-600">
                                <input type="checkbox" name="services[]" value="{{ $service->id }}" {{ $professional->services->contains($service->id) ? 'checked' : '' }} class="rounded border-gray-300 text-coral-500 focus:ring-coral-400">
                                {{ $service->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" data-modal-close class="btn-secondary">Cancelar</button>
                    <button type="submit" class="btn-primary">Atualizar</button>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endsection
