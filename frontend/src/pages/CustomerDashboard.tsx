import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { api } from '../lib/api';

const statusColors: Record<string, string> = {
  SCHEDULED: 'bg-blue-100 text-blue-800',
  CONFIRMED: 'bg-green-100 text-green-800',
  CANCELLED: 'bg-red-100 text-red-800',
  COMPLETED: 'bg-gray-100 text-gray-800',
  NO_SHOW: 'bg-yellow-100 text-yellow-800',
};

const statusLabels: Record<string, string> = {
  SCHEDULED: 'Agendado',
  CONFIRMED: 'Confirmado',
  CANCELLED: 'Cancelado',
  COMPLETED: 'Realizado',
  NO_SHOW: 'Não compareceu',
};

export default function CustomerDashboard() {
  const [appointments, setAppointments] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get('/appointments/my').then(setAppointments).finally(() => setLoading(false));
  }, []);

  const handleCancel = async (id: string) => {
    if (!confirm('Tens a certeza que queres cancelar este agendamento?')) return;
    try {
      await api.put(`/appointments/${id}/status`, { status: 'CANCELLED', reason: 'Cancelado pelo cliente' });
      setAppointments(appointments.map(a => a.id === id ? { ...a, status: 'CANCELLED' } : a));
    } catch (err: any) {
      alert(err.message);
    }
  };

  return (
    <div className="max-w-4xl mx-auto px-4 py-8">
      <h1 className="text-2xl font-bold mb-6">Os meus agendamentos</h1>

      {loading ? (
        <div className="text-center py-12 text-gray-500">A carregar...</div>
      ) : appointments.length === 0 ? (
        <div className="text-center py-12">
          <p className="text-gray-500 mb-4">Ainda não tens agendamentos.</p>
          <Link to="/" className="bg-coral-500 text-white px-6 py-2 rounded-lg hover:bg-coral-600">
            Explorar negócios
          </Link>
        </div>
      ) : (
        <div className="space-y-4">
          {appointments.map(a => (
            <div key={a.id} className="bg-white rounded-xl shadow-sm border p-4">
              <div className="flex items-start justify-between">
                <div>
                  <div className="flex items-center gap-2 mb-1">
                    <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${statusColors[a.status]}`}>
                      {statusLabels[a.status]}
                    </span>
                  </div>
                  <p className="font-semibold">{a.tenant?.name}</p>
                  <div className="text-sm text-gray-500 mt-1">
                    <p>{a.professional?.name} - {a.service?.name}</p>
                    <p>{new Date(a.date).toLocaleDateString('pt-PT')} às {a.startTime}</p>
                    <p>{a.service?.duration} min - {a.service?.price?.toFixed(2)} €</p>
                  </div>
                </div>
                {a.status === 'SCHEDULED' && (
                  <button onClick={() => handleCancel(a.id)} className="text-sm text-red-500 hover:text-red-700">
                    Cancelar
                  </button>
                )}
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
