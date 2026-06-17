import { useState, useEffect, useMemo } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { api } from '../lib/api';
import { useAuth } from '../contexts/AuthContext';

export default function BookingPage() {
  const { slug } = useParams<{ slug: string }>();
  const { user } = useAuth();
  const navigate = useNavigate();
  const [tenant, setTenant] = useState<any>(null);
  const [professionalId, setProfessionalId] = useState('');
  const [serviceId, setServiceId] = useState('');
  const [date, setDate] = useState('');
  const [slots, setSlots] = useState<any[]>([]);
  const [selectedTime, setSelectedTime] = useState('');
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState(false);

  useEffect(() => {
    api.get(`/tenants/${slug}`).then(t => {
      setTenant(t);
      setLoading(false);
    });
  }, [slug]);

  useEffect(() => {
    if (professionalId && date) {
      api.get(`/appointments/availability?professionalId=${professionalId}&date=${date}`)
        .then(data => setSlots(data.slots || []))
        .catch(() => setSlots([]));
    } else {
      setSlots([]);
    }
  }, [professionalId, date]);

  useEffect(() => {
    setSelectedTime('');
  }, [professionalId, serviceId, date]);

  const professionalsForService = useMemo(() => {
    if (!serviceId || !tenant?.services) return tenant?.professionals || [];
    const svc = tenant.services.find((s: any) => s.id === serviceId);
    return svc?.professionals || [];
  }, [serviceId, tenant]);

  const servicesForProfessional = useMemo(() => {
    if (!professionalId || !tenant?.professionals) return tenant?.services || [];
    const prof = tenant.professionals.find((p: any) => p.id === professionalId);
    return prof?.services || [];
  }, [professionalId, tenant]);

  const today = new Date().toISOString().split('T')[0];

  const handleSubmit = async () => {
    if (!user) { navigate('/login'); return; }
    setError('');
    setSubmitting(true);
    try {
      await api.post('/appointments', {
        tenantId: tenant.id,
        professionalId,
        serviceId,
        date,
        startTime: selectedTime,
      });
      setSuccess(true);
    } catch (err: any) {
      setError(err.message);
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) return <div className="text-center py-20 text-gray-500">A carregar...</div>;
  if (!tenant) return <div className="text-center py-20 text-gray-500">Negócio não encontrado</div>;

  if (success) {
    return (
      <div className="max-w-lg mx-auto px-4 py-20 text-center">
        <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <span className="text-3xl text-green-600">&#10003;</span>
        </div>
        <h1 className="text-2xl font-bold mb-2">Agendamento Confirmado!</h1>
        <p className="text-gray-600 mb-6">Receberás uma notificação quando o profissional confirmar.</p>
        <button onClick={() => navigate('/cliente')} className="btn-primary">
          Ver os meus agendamentos
        </button>
      </div>
    );
  }

  const selectedService = tenant.services?.find((s: any) => s.id === serviceId);
  const selectedProfessional = tenant.professionals?.find((p: any) => p.id === professionalId);

  return (
    <div className="max-w-3xl mx-auto px-4 py-8">
      <div className="flex items-center gap-3 mb-6">
        <div className="w-12 h-12 rounded-xl bg-coral-100 flex items-center justify-center text-xl font-bold text-coral-500">
          {tenant.name.charAt(0)}
        </div>
        <div>
          <h1 className="text-2xl font-bold">Agendar</h1>
          <p className="text-gray-500">{tenant.name}</p>
        </div>
      </div>

      {error && (
        <div className="bg-red-50 text-red-600 p-3 rounded-xl text-sm mb-4 flex items-center gap-2">
          <span>⚠️</span> {error}
        </div>
      )}

      <div className="bg-white rounded-2xl shadow-sm border p-6 md:p-8 space-y-6">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="block font-medium text-gray-700 mb-1.5 text-sm">Serviço</label>
            <select value={serviceId} onChange={e => { setServiceId(e.target.value); setProfessionalId(''); }}
              className="w-full border border-gray-200 rounded-xl px-4 py-2.5 bg-white focus:ring-2 focus:ring-coral-400 outline-none">
              <option value="">Selecionar serviço</option>
              {(professionalId ? servicesForProfessional : tenant.services)?.map((s: any) => (
                <option key={s.id} value={s.id}>
                  {s.category ? `[${s.category}] ` : ''}{s.name} — {s.duration}min — {s.price.toFixed(2)}€
                </option>
              ))}
            </select>
          </div>
          <div>
            <label className="block font-medium text-gray-700 mb-1.5 text-sm">Profissional</label>
            <select value={professionalId} onChange={e => setProfessionalId(e.target.value)}
              className="w-full border border-gray-200 rounded-xl px-4 py-2.5 bg-white focus:ring-2 focus:ring-coral-400 outline-none">
              <option value="">Selecionar profissional</option>
              {(serviceId ? professionalsForService : tenant.professionals)?.map((p: any) => (
                <option key={p.id} value={p.id}>{p.name}</option>
              ))}
            </select>
          </div>
        </div>

        <div>
          <label className="block font-medium text-gray-700 mb-1.5 text-sm">Data</label>
          <input type="date" value={date} min={today} onChange={e => setDate(e.target.value)}
            className="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-coral-400 outline-none" />
        </div>

        {slots.length > 0 && (
          <div>
            <label className="block font-medium text-gray-700 mb-2 text-sm">Horário disponível</label>
            <div className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
              {slots.map((s: any) => (
                <button
                  key={s.time}
                  onClick={() => setSelectedTime(s.time)}
                  className={`px-3 py-2.5 rounded-xl border text-sm font-medium transition ${
                    selectedTime === s.time
                      ? 'bg-coral-500 text-white border-coral-500 shadow-md'
                      : 'hover:bg-coral-50 hover:border-coral-200 border-gray-200'
                  }`}
                >
                  {s.time}
                </button>
              ))}
            </div>
          </div>
        )}

        {selectedService && (
          <div className="bg-gradient-to-r from-coral-50 to-rose-50 rounded-2xl p-6">
            <div className="flex items-center justify-between mb-3">
              <p className="text-sm text-gray-500">Resumo do agendamento</p>
              {date && <span className="text-xs text-gray-400">{new Date(date).toLocaleDateString('pt-PT')}</span>}
            </div>
            <div className="flex items-center gap-4">
              <div className="flex-1">
                <p className="font-semibold text-gray-900">{selectedService.name}</p>
                {selectedProfessional && (
                  <p className="text-sm text-gray-500">com {selectedProfessional.name}</p>
                )}
                <div className="flex items-center gap-3 mt-1 text-sm text-gray-400">
                  <span>⏱ {selectedService.duration} min</span>
                  {selectedTime && <span>🕐 {selectedTime}h</span>}
                </div>
              </div>
              <div className="text-right">
                <p className="text-2xl font-bold text-coral-500">{selectedService.price.toFixed(2)}€</p>
              </div>
            </div>
          </div>
        )}

        <button
          onClick={handleSubmit}
          disabled={!professionalId || !serviceId || !date || !selectedTime || submitting}
          className="w-full bg-coral-500 text-white py-3.5 rounded-xl hover:bg-coral-600 disabled:opacity-50 disabled:cursor-not-allowed font-semibold text-lg shadow-lg shadow-coral-200 transition"
        >
          {submitting ? 'A agendar...' : 'Confirmar Agendamento'}
        </button>
      </div>
    </div>
  );
}
