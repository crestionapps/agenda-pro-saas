@extends('layouts.app')

@section('title', 'Gerir Planos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Planos de Subscrição</h1>
        <button data-modal-toggle="addPlanModal" class="btn-primary">
            + Criar Plano
        </button>
    </div>

    @if(session('success'))
        <div data-flash class="mb-4 p-4 rounded-xl bg-green-100 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($plans as $plan)
            <div class="card p-6 flex flex-col">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $plan->name }}</h3>
                        <p class="text-3xl font-bold text-coral-500 mt-1">&euro;{{ number_format($plan->price, 2, ',', '.') }}<span class="text-sm font-normal text-gray-400">/mês</span></p>
                    </div>
                    @if($plan->is_active)
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Ativo</span>
                    @else
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Inativo</span>
                    @endif
                </div>

                <div class="space-y-3 flex-1">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                        <div class="w-10 h-10 rounded-lg bg-coral-100 flex items-center justify-center text-coral-600 font-bold">SMS</div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $plan->sms_quota }}</p>
                            <p class="text-xs text-gray-500">SMS/mês</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 font-bold">@</div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $plan->email_quota }}</p>
                            <p class="text-xs text-gray-500">E-mails/mês</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                        <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-green-600 font-bold">WA</div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $plan->whatsapp_quota }}</p>
                            <p class="text-xs text-gray-500">WhatsApp/mês</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-4 mt-4 border-t border-gray-100">
                    <button data-modal-toggle="editPlanModal-{{ $plan->id }}" class="btn-secondary flex-1 text-sm">Editar</button>
                    <form action="{{ route('admin.plans.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Tem a certeza?')" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-secondary text-sm text-red-500 w-full">Eliminar</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-400">Nenhum plano encontrado.</div>
        @endforelse
    </div>
</div>

{{-- Add Plan Modal --}}
<div id="addPlanModal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">Criar Plano</h2>
            <button data-modal-close class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form action="{{ route('admin.plans.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Plano</label>
                <input type="text" name="name" required class="input-field" placeholder="Ex: Basic">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Preço (&euro;/mês)</label>
                <input type="number" name="price" step="0.01" min="0" required class="input-field" placeholder="19.90">
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SMS</label>
                    <input type="number" name="sms_quota" min="0" required class="input-field" placeholder="50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="number" name="email_quota" min="0" required class="input-field" placeholder="100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                    <input type="number" name="whatsapp_quota" min="0" required class="input-field" placeholder="30">
                </div>
            </div>
            <div>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-coral-500 focus:ring-coral-400">
                    Plano ativo
                </label>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" data-modal-close class="btn-secondary">Cancelar</button>
                <button type="submit" class="btn-primary">Criar Plano</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Plan Modals --}}
@foreach($plans as $plan)
    <div id="editPlanModal-{{ $plan->id }}" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Editar Plano</h2>
                <button data-modal-close class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <form action="{{ route('admin.plans.update', $plan->id) }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Plano</label>
                    <input type="text" name="name" value="{{ $plan->name }}" required class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preço (&euro;/mês)</label>
                    <input type="number" name="price" step="0.01" min="0" value="{{ $plan->price }}" required class="input-field">
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SMS</label>
                        <input type="number" name="sms_quota" min="0" value="{{ $plan->sms_quota }}" required class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                        <input type="number" name="email_quota" min="0" value="{{ $plan->email_quota }}" required class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                        <input type="number" name="whatsapp_quota" min="0" value="{{ $plan->whatsapp_quota }}" required class="input-field">
                    </div>
                </div>
                <div>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_active" value="1" {{ $plan->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-coral-500 focus:ring-coral-400">
                        Plano ativo
                    </label>
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
