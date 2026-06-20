@extends('layouts.app')

@section('title', 'Agendar - ' . $tenant->name)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Tenant Header --}}
    <div class="flex items-center gap-3 mb-8">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-coral-100 to-rose-100 flex items-center justify-center text-lg font-bold text-coral-500 flex-shrink-0 overflow-hidden">
            @if($tenant->logo)
                <img src="{{ asset('storage/' . $tenant->logo) }}" alt="" class="w-full h-full object-cover">
            @else
                {{ substr($tenant->name, 0, 1) }}
            @endif
        </div>
        <div>
            <h1 class="text-xl font-bold text-gray-800">{{ $tenant->name }}</h1>
            <p class="text-sm text-gray-400">Agende seu horário</p>
        </div>
    </div>

    {{-- Stepper --}}
    <div class="flex items-center justify-center mb-10">
        <div class="flex items-center">
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-coral-500 text-white flex items-center justify-center text-sm font-bold step-indicator" data-step="1">1</div>
                <span class="text-xs mt-1.5 font-medium text-coral-500">Serviço</span>
            </div>
            <div class="w-12 h-0.5 mx-2 bg-gray-200 step-line"></div>
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-bold step-indicator" data-step="2">2</div>
                <span class="text-xs mt-1.5 font-medium text-gray-400">Profissional</span>
            </div>
            <div class="w-12 h-0.5 mx-2 bg-gray-200 step-line"></div>
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-bold step-indicator" data-step="3">3</div>
                <span class="text-xs mt-1.5 font-medium text-gray-400">Data & Hora</span>
            </div>
        </div>
    </div>

    <form id="booking-form">
        @csrf
        <input type="hidden" name="tenant_id" value="{{ $tenant->id }}">

        {{-- Step 1: Services --}}
        <div class="step" id="step-1">
            <h2 class="text-lg font-semibold text-gray-800 mb-5">Escolha um serviço</h2>
            @php $grouped = $tenant->services->groupBy('category'); @endphp
            @forelse($grouped as $category => $services)
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">{{ $category ?: 'Geral' }}</h3>
                    <div class="space-y-3">
                        @foreach($services as $service)
                            <div class="service-card bg-white rounded-xl p-5 shadow-sm border-2 border-transparent hover:border-coral-200 hover:shadow-md transition-all cursor-pointer" data-service-id="{{ $service->id }}" onclick="selectService({{ $service->id }}, this)">
                                <div class="flex justify-between items-center">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800">{{ $service->name }}</h4>
                                        <p class="text-sm text-gray-400 mt-1 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $service->duration }} min
                                        </p>
                                    </div>
                                    <span class="text-lg font-bold text-coral-500 whitespace-nowrap ml-4">
                                        R$ {{ number_format($service->price, 2, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-16 text-gray-400">
                    <p>Nenhum serviço disponível</p>
                </div>
            @endforelse
            <div class="flex justify-end mt-6">
                <button type="button" onclick="goStep(2)" id="step1-next" disabled class="bg-coral-300 text-white px-8 py-3 rounded-xl font-semibold transition cursor-not-allowed">Próximo</button>
            </div>
        </div>

        {{-- Step 2: Professionals --}}
        <div class="step hidden" id="step-2">
            <h2 class="text-lg font-semibold text-gray-800 mb-5">Escolha um profissional</h2>
            <div id="professionals-list" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($tenant->professionals as $professional)
                    <div class="professional-card bg-white rounded-xl p-5 shadow-sm border-2 border-transparent hover:border-coral-200 hover:shadow-md transition-all cursor-pointer text-center" data-professional-id="{{ $professional->id }}" data-services="{{ $professional->services->pluck('id')->join(',') }}" onclick="selectProfessional({{ $professional->id }}, this)">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-coral-100 to-rose-100 flex items-center justify-center text-xl font-bold text-coral-500 mx-auto mb-3">
                            @if($professional->photo)
                                <img src="{{ asset('storage/' . $professional->photo) }}" alt="" class="w-full h-full object-cover rounded-full">
                            @else
                                {{ substr($professional->name, 0, 1) }}
                            @endif
                        </div>
                        <h4 class="font-semibold text-gray-800">{{ $professional->name }}</h4>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-6">
                <button type="button" onclick="goStep(1)" class="px-6 py-3 rounded-xl font-semibold border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Voltar</button>
                <button type="button" onclick="goStep(3)" id="step2-next" disabled class="bg-coral-300 text-white px-8 py-3 rounded-xl font-semibold transition cursor-not-allowed">Próximo</button>
            </div>
        </div>

        {{-- Step 3: Date & Time --}}
        <div class="step hidden" id="step-3">
            <h2 class="text-lg font-semibold text-gray-800 mb-5">Escolha a data e horário</h2>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-600 mb-2">Data</label>
                <input type="date" id="date-picker" min="{{ date('Y-m-d') }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-transparent">
            </div>
            <div id="slots-container">
                <label class="block text-sm font-medium text-gray-600 mb-2">Horários disponíveis</label>
                <div id="slots-grid" class="grid grid-cols-3 sm:grid-cols-4 gap-3 min-h-[100px]">
                    <div class="col-span-full text-center text-gray-400 py-8">Selecione uma data para ver os horários</div>
                </div>
            </div>

            {{-- Review & Confirm --}}
            <div class="mt-8 bg-gray-50 rounded-xl p-5 border border-gray-100">
                <h3 class="font-semibold text-gray-800 mb-3">Resumo do Agendamento</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Serviço:</span>
                        <span id="review-service" class="font-medium text-gray-800">--</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Profissional:</span>
                        <span id="review-professional" class="font-medium text-gray-800">--</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Data:</span>
                        <span id="review-date" class="font-medium text-gray-800">--</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Horário:</span>
                        <span id="review-time" class="font-medium text-gray-800">--</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-200">
                        <span class="text-gray-500">Valor:</span>
                        <span id="review-price" class="font-bold text-coral-500">--</span>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Observações</label>
                    <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-transparent" placeholder="Alguma observação? (opcional)"></textarea>
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <button type="button" onclick="goStep(2)" class="px-6 py-3 rounded-xl font-semibold border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Voltar</button>
                <button type="button" onclick="confirmBooking()" id="confirm-btn" disabled class="bg-coral-400 text-white px-8 py-3 rounded-xl font-semibold transition cursor-not-allowed">Confirmar Agendamento</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const slug = '{{ $tenant->slug }}';
    const csrftoken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    let selectedService = null;
    let selectedProfessional = null;
    let selectedDate = null;
    let selectedSlot = null;

    const services = @json($tenant->services->keyBy('id'));
    const professionals = @json($tenant->professionals->keyBy('id'));

    function goStep(n) {
        document.querySelectorAll('.step').forEach((el, i) => {
            el.classList.toggle('hidden', (i + 1) !== n);
        });
        document.querySelectorAll('.step-indicator').forEach((el, i) => {
            const num = i + 1;
            const isActive = num <= n;
            el.classList.toggle('bg-coral-500', isActive);
            el.classList.toggle('text-white', isActive);
            el.classList.toggle('bg-gray-200', !isActive);
            el.classList.toggle('text-gray-500', !isActive);
        });
        document.querySelectorAll('.step-line').forEach((el, i) => {
            el.classList.toggle('bg-coral-500', i + 1 < n);
            el.classList.toggle('bg-gray-200', i + 1 >= n);
        });
        document.querySelectorAll('.step-indicator').forEach((el, i) => {
            const label = el.closest('.flex-col')?.querySelector('span');
            if (label) {
                label.classList.toggle('text-coral-500', i + 1 <= n);
                label.classList.toggle('text-gray-400', i + 1 > n);
            }
        });

        if (n === 3) updateReview();
    }

    function selectService(id, el) {
        selectedService = id;
        document.querySelectorAll('.service-card').forEach(c => {
            c.classList.remove('border-coral-500', 'bg-coral-50');
            c.classList.add('border-transparent');
        });
        el.classList.remove('border-transparent');
        el.classList.add('border-coral-500', 'bg-coral-50');
        document.getElementById('step1-next').disabled = false;
        document.getElementById('step1-next').classList.remove('bg-coral-300', 'cursor-not-allowed');
        document.getElementById('step1-next').classList.add('bg-coral-500', 'hover:bg-coral-600', 'cursor-pointer');

        document.querySelectorAll('.professional-card').forEach(c => {
            const s = c.dataset.services ? c.dataset.services.split(',') : [];
            if (s.includes(String(id))) {
                c.classList.remove('hidden');
            } else {
                c.classList.add('hidden');
            }
        });

        selectedProfessional = null;
        document.getElementById('step2-next').disabled = true;
        document.getElementById('step2-next').classList.add('bg-coral-300', 'cursor-not-allowed');
        document.getElementById('step2-next').classList.remove('bg-coral-500', 'hover:bg-coral-600', 'cursor-pointer');
    }

    function selectProfessional(id, el) {
        selectedProfessional = id;
        document.querySelectorAll('.professional-card').forEach(c => {
            c.classList.remove('border-coral-500', 'bg-coral-50');
            c.classList.add('border-transparent');
        });
        el.classList.remove('border-transparent');
        el.classList.add('border-coral-500', 'bg-coral-50');
        document.getElementById('step2-next').disabled = false;
        document.getElementById('step2-next').classList.remove('bg-coral-300', 'cursor-not-allowed');
        document.getElementById('step2-next').classList.add('bg-coral-500', 'hover:bg-coral-600', 'cursor-pointer');
    }

    document.getElementById('date-picker')?.addEventListener('change', function() {
        selectedDate = this.value;
        selectedSlot = null;
        if (selectedService && selectedProfessional && selectedDate) {
            loadSlots();
        }
        updateReview();
    });

    function loadSlots() {
        const grid = document.getElementById('slots-grid');
        grid.innerHTML = '<div class="col-span-full text-center text-gray-400 py-8">Carregando...</div>';

        fetch(`/api/tenants/${slug}/profissionais/${selectedProfessional}/slots?date=${selectedDate}&service_id=${selectedService}`)
            .then(r => {
                if (!r.ok) throw new Error('Erro ao carregar horários');
                return r.json();
            })
            .then(slots => {
                if (!slots || slots.length === 0) {
                    grid.innerHTML = '<div class="col-span-full text-center text-gray-400 py-8">Nenhum horário disponível nesta data</div>';
                    return;
                }
                grid.innerHTML = '';
                slots.forEach(slot => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'slot-btn py-3 px-3 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:border-coral-300 hover:text-coral-600 transition-all';
                    btn.textContent = slot.time || slot;
                    btn.dataset.time = slot.time || slot;
                    btn.onclick = function() {
                        document.querySelectorAll('.slot-btn').forEach(b => {
                            b.classList.remove('bg-coral-500', 'text-white', 'border-coral-500');
                            b.classList.add('border-gray-200', 'text-gray-700');
                        });
                        this.classList.remove('border-gray-200', 'text-gray-700');
                        this.classList.add('bg-coral-500', 'text-white', 'border-coral-500');
                        selectedSlot = this.dataset.time;
                        updateReview();
                        document.getElementById('confirm-btn').disabled = false;
                        document.getElementById('confirm-btn').classList.remove('bg-coral-400', 'cursor-not-allowed');
                        document.getElementById('confirm-btn').classList.add('bg-coral-500', 'hover:bg-coral-600', 'cursor-pointer');
                    };
                    grid.appendChild(btn);
                });
            })
            .catch(err => {
                grid.innerHTML = '<div class="col-span-full text-center text-red-400 py-8">Erro ao carregar horários. Tente novamente.</div>';
            });
    }

    function updateReview() {
        if (selectedService && services[selectedService]) {
            document.getElementById('review-service').textContent = services[selectedService].name;
            document.getElementById('review-price').textContent = 'R$ ' + Number(services[selectedService].price).toFixed(2).replace('.', ',');
        }
        if (selectedProfessional && professionals[selectedProfessional]) {
            document.getElementById('review-professional').textContent = professionals[selectedProfessional].name;
        }
        if (selectedDate) {
            const d = new Date(selectedDate + 'T12:00:00');
            document.getElementById('review-date').textContent = d.toLocaleDateString('pt-BR');
        }
        if (selectedSlot) {
            document.getElementById('review-time').textContent = selectedSlot;
        }
    }

    function confirmBooking() {
        if (!selectedService || !selectedProfessional || !selectedDate || !selectedSlot) return;

        const btn = document.getElementById('confirm-btn');
        btn.disabled = true;
        btn.textContent = 'Confirmando...';

        fetch('/api/appointments', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrftoken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                tenant_id: {{ $tenant->id }},
                service_id: selectedService,
                professional_id: selectedProfessional,
                date: selectedDate,
                time: selectedSlot,
                notes: document.querySelector('textarea[name="notes"]')?.value || ''
            })
        })
        .then(r => {
            if (r.ok) {
                window.location.href = '/minha-conta';
            } else {
                return r.json().then(data => { throw new Error(data.message || 'Erro ao confirmar agendamento'); });
            }
        })
        .catch(err => {
            alert(err.message);
            btn.disabled = false;
            btn.textContent = 'Confirmar Agendamento';
        });
    }
</script>
@endpush
