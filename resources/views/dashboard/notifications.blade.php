@extends('layouts.app')

@section('title', 'Notificações')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Notificações</h1>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Destinatário</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($notifications as $notification)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                @switch($notification->type)
                                    @case('sms')
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-coral-100 text-coral-700">SMS</span>
                                        @break
                                    @case('email')
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Email</span>
                                        @break
                                    @case('whatsapp')
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">WhatsApp</span>
                                        @break
                                    @default
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $notification->type }}</span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $notification->recipient }}</td>
                            <td class="px-6 py-4">
                                @switch($notification->status)
                                    @case('sent')
                                        <span class="status-scheduled">Enviado</span>
                                        @break
                                    @case('delivered')
                                        <span class="status-confirmed">Entregue</span>
                                        @break
                                    @case('failed')
                                        <span class="status-cancelled">Falhou</span>
                                        @break
                                    @case('pending')
                                        <span class="status-no_show">Pendente</span>
                                        @break
                                    @default
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $notification->status }}</span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-sm">{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">Nenhuma notificação encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($notifications, 'links'))
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
