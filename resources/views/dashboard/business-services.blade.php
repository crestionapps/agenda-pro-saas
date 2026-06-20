@extends('layouts.app')

@section('title', 'Gerir Serviços')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Gerir Serviços</h1>
        <button data-modal-toggle="addServiceModal" class="btn-primary">
            + Adicionar Serviço
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
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Descrição</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Preço</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Duração</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Categoria</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Profissionais</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($services as $service)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $service->name }}</td>
                            <td class="px-6 py-4 text-gray-600 max-w-xs truncate">{{ $service->description }}</td>
                            <td class="px-6 py-4 text-gray-900 font-medium">&euro;{{ number_format($service->price, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $service->duration }} min</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-coral-100 text-coral-700">{{ $service->category }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($service->professionals as $professional)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $professional->name }}</span>
                                    @empty
                                        <span class="text-gray-400 text-sm">—</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button data-modal-toggle="editServiceModal-{{ $service->id }}" class="text-sm font-medium text-coral-500 hover:text-coral-700">Editar</button>
                                    <form action="{{ route('tenant.services.destroy', [$tenantId, $service->id]) }}" method="POST" onsubmit="return confirm('Tem a certeza?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-700">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">Nenhum serviço encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Service Modal --}}
<div id="addServiceModal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">Adicionar Serviço</h2>
            <button data-modal-close class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form action="{{ route('tenant.services.store', $tenantId) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                <input type="text" name="name" required class="input-field" placeholder="Nome do serviço">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                <textarea name="description" rows="2" class="input-field" placeholder="Breve descrição..."></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preço (&euro;)</label>
                    <input type="number" name="price" step="0.01" min="0" required class="input-field" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duração (min)</label>
                    <input type="number" name="duration" min="5" step="5" required class="input-field" placeholder="30">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                <select name="category" required class="input-field">
                    <option value="">Selecionar...</option>
                    <option value="Corte">Corte</option>
                    <option value="Barba">Barba</option>
                    <option value="Massagem">Massagem</option>
                    <option value="Combo">Combo</option>
                    <option value="Facial">Facial</option>
                    <option value="Tratamento">Tratamento</option>
                    <option value="Mãos">Mãos</option>
                    <option value="Cor">Cor</option>
                    <option value="Design">Design</option>
                    <option value="Outro">Outro</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Profissionais</label>
                <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto">
                    @foreach($professionals as $professional)
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="professionals[]" value="{{ $professional->id }}" class="rounded border-gray-300 text-coral-500 focus:ring-coral-400">
                            {{ $professional->name }}
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

{{-- Edit Service Modals --}}
@foreach($services as $service)
    <div id="editServiceModal-{{ $service->id }}" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Editar Serviço</h2>
                <button data-modal-close class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <form action="{{ route('tenant.services.update', [$tenantId, $service->id]) }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                    <input type="text" name="name" value="{{ $service->name }}" required class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                    <textarea name="description" rows="2" class="input-field">{{ $service->description }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Preço (&euro;)</label>
                        <input type="number" name="price" step="0.01" min="0" value="{{ $service->price }}" required class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Duração (min)</label>
                        <input type="number" name="duration" min="5" step="5" value="{{ $service->duration }}" required class="input-field">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                    <select name="category" required class="input-field">
                        <option value="">Selecionar...</option>
                        <option value="Corte" {{ $service->category == 'Corte' ? 'selected' : '' }}>Corte</option>
                        <option value="Barba" {{ $service->category == 'Barba' ? 'selected' : '' }}>Barba</option>
                        <option value="Massagem" {{ $service->category == 'Massagem' ? 'selected' : '' }}>Massagem</option>
                        <option value="Combo" {{ $service->category == 'Combo' ? 'selected' : '' }}>Combo</option>
                        <option value="Facial" {{ $service->category == 'Facial' ? 'selected' : '' }}>Facial</option>
                        <option value="Tratamento" {{ $service->category == 'Tratamento' ? 'selected' : '' }}>Tratamento</option>
                        <option value="Mãos" {{ $service->category == 'Mãos' ? 'selected' : '' }}>Mãos</option>
                        <option value="Cor" {{ $service->category == 'Cor' ? 'selected' : '' }}>Cor</option>
                        <option value="Design" {{ $service->category == 'Design' ? 'selected' : '' }}>Design</option>
                        <option value="Outro" {{ $service->category == 'Outro' ? 'selected' : '' }}>Outro</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Profissionais</label>
                    <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto">
                        @foreach($professionals as $professional)
                            <label class="flex items-center gap-2 text-sm text-gray-600">
                                <input type="checkbox" name="professionals[]" value="{{ $professional->id }}" {{ $service->professionals->contains($professional->id) ? 'checked' : '' }} class="rounded border-gray-300 text-coral-500 focus:ring-coral-400">
                                {{ $professional->name }}
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
