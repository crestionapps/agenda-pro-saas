@extends('layouts.app')

@section('title', 'Gerir Empresas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Empresas</h1>
    </div>

    @if(session('success'))
        <div data-flash class="mb-4 p-4 rounded-xl bg-green-100 text-green-700">{{ session('success') }}</div>
    @endif

    {{-- Search / Filters --}}
    <div class="card p-4 mb-6">
        <form method="GET" action="{{ route('admin.tenants.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Pesquisar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, cidade ou slug..." class="input-field">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Estado</label>
                <select name="status" class="input-field">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">Filtrar</button>
            @if(request()->anyFilled(['search', 'status']))
                <a href="{{ route('admin.tenants.index') }}" class="btn-secondary">Limpar</a>
            @endif
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Nome</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Slug</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Cidade</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Agendamentos</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Profissionais</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tenants as $tenant)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $tenant->name }}</td>
                            <td class="px-6 py-4 text-gray-600 font-mono text-sm">{{ $tenant->slug }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $tenant->type }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $tenant->city }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $tenant->appointments_count ?? 0 }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $tenant->professionals_count ?? 0 }}</td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.tenants.toggle-status', $tenant->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 {{ $tenant->is_active ? 'bg-green-500' : 'bg-gray-300' }}">
                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-200 {{ $tenant->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.tenants.edit', $tenant->id) }}" class="text-sm font-medium text-coral-500 hover:text-coral-700">Editar</a>
                                    <form action="{{ route('admin.tenants.destroy', $tenant->id) }}" method="POST" onsubmit="return confirm('Tem a certeza?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-700">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">Nenhuma empresa encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($tenants, 'links'))
        <div class="mt-6">
            {{ $tenants->links() }}
        </div>
    @endif
</div>
@endsection
