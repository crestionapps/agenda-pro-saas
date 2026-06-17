import { useState, useEffect } from 'react';
import { api } from '../lib/api';
import { useAuth } from '../contexts/AuthContext';

const statusColors: Record<string, string> = {
  SCHEDULED: 'bg-blue-100 text-blue-800',
  CONFIRMED: 'bg-green-100 text-green-800',
  CANCELLED: 'bg-red-100 text-red-800',
  COMPLETED: 'bg-gray-100 text-gray-800',
  NO_SHOW: 'bg-yellow-100 text-yellow-800',
};
const statusLabels: Record<string, string> = {
  SCHEDULED: 'Agendado', CONFIRMED: 'Confirmado', CANCELLED: 'Cancelado',
  COMPLETED: 'Realizado', NO_SHOW: 'Não compareceu',
};
const weekDays = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

export default function ManagerDashboard() {
  const { user } = useAuth();
  const [appointments, setAppointments] = useState<any[]>([]);
  const [professionals, setProfessionals] = useState<any[]>([]);
  const [services, setServices] = useState<any[]>([]);
  const [stats, setStats] = useState<any>(null);
  const [date, setDate] = useState(new Date().toISOString().split('T')[0]);
  const [loading, setLoading] = useState(true);

  const tenantId = user?.tenantId;

  useEffect(() => {
    if (!tenantId) return;
    Promise.all([
      api.get(`/appointments/tenant/${tenantId}?date=${date}`),
      api.get(`/professionals/tenant/${tenantId}`),
      api.get(`/services/tenant/${tenantId}`),
      api.get(`/tenants/${tenantId}/stats`),
    ]).then(([apps, profs, svcs, sts]) => {
      setAppointments(apps);
      setProfessionals(profs);
      setServices(svcs);
      setStats(sts);
    }).finally(() => setLoading(false));
  }, [tenantId, date]);

  const updateStatus = async (id: string, status: string) => {
    try {
      await api.put(`/appointments/${id}/status`, { status });
      setAppointments(appointments.map(a => a.id === id ? { ...a, status } : a));
    } catch (err: any) { alert(err.message); }
  };

  if (loading) return <div className="text-center py-20 text-gray-500">A carregar...</div>;
  if (!tenantId) return <div className="text-center py-20 text-gray-500">Sem negócio associado</div>;

  return (
    <div className="max-w-7xl mx-auto px-4 py-8">
      <h1 className="text-2xl font-bold mb-6">Painel de Gestão</h1>

      {stats && (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
          <div className="bg-white p-4 rounded-xl shadow-sm border">
            <p className="text-2xl font-bold text-coral-500">{stats.totalAppointments}</p>
            <p className="text-sm text-gray-500">Total Agendamentos</p>
          </div>
          <div className="bg-white p-4 rounded-xl shadow-sm border">
            <p className="text-2xl font-bold text-green-600">{stats.monthlyAppointments}</p>
            <p className="text-sm text-gray-500">Este Mês</p>
          </div>
          <div className="bg-white p-4 rounded-xl shadow-sm border">
            <p className="text-2xl font-bold text-blue-600">{stats.totalProfessionals}</p>
            <p className="text-sm text-gray-500">Profissionais</p>
          </div>
          <div className="bg-white p-4 rounded-xl shadow-sm border">
            <p className="text-2xl font-bold text-purple-600">{stats.totalCustomers}</p>
            <p className="text-sm text-gray-500">Clientes</p>
          </div>
        </div>
      )}

      <div className="bg-white rounded-xl shadow-sm border p-6 mb-6">
        <div className="flex items-center justify-between mb-4">
          <h2 className="font-semibold text-lg">Agendamentos do Dia</h2>
          <input type="date" value={date} onChange={e => setDate(e.target.value)}
            className="border rounded-lg px-3 py-1 text-sm" />
        </div>
        {appointments.length === 0 ? (
          <p className="text-gray-500 text-center py-8">Nenhum agendamento para esta data</p>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead>
                <tr className="border-b text-left text-gray-500">
                  <th className="pb-2">Hora</th>
                  <th className="pb-2">Cliente</th>
                  <th className="pb-2">Profissional</th>
                  <th className="pb-2">Serviço</th>
                  <th className="pb-2">Status</th>
                  <th className="pb-2">Ações</th>
                </tr>
              </thead>
              <tbody>
                {appointments.map(a => (
                  <tr key={a.id} className="border-b last:border-0">
                    <td className="py-3">{a.startTime}</td>
                    <td className="py-3">{a.customer?.name}</td>
                    <td className="py-3">{a.professional?.name}</td>
                    <td className="py-3">{a.service?.name}</td>
                    <td className="py-3">
                      <span className={`px-2 py-0.5 rounded-full text-xs ${statusColors[a.status]}`}>
                        {statusLabels[a.status]}
                      </span>
                    </td>
                    <td className="py-3">
                      <div className="flex gap-1">
                        {a.status === 'SCHEDULED' && (
                          <button onClick={() => updateStatus(a.id, 'CONFIRMED')}
                            className="px-2 py-1 bg-green-100 text-green-700 rounded text-xs hover:bg-green-200">
                            Confirmar
                          </button>
                        )}
                        {a.status === 'SCHEDULED' && (
                          <button onClick={() => updateStatus(a.id, 'CANCELLED')}
                            className="px-2 py-1 bg-red-100 text-red-700 rounded text-xs hover:bg-red-200">
                            Cancelar
                          </button>
                        )}
                        {(a.status === 'CONFIRMED') && (
                          <button onClick={() => updateStatus(a.id, 'COMPLETED')}
                            className="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs hover:bg-gray-200">
                            Concluir
                          </button>
                        )}
                        {a.status === 'CONFIRMED' && (
                          <button onClick={() => updateStatus(a.id, 'NO_SHOW')}
                            className="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs hover:bg-yellow-200">
                            Não compareceu
                          </button>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div className="bg-white rounded-xl shadow-sm border p-6">
          <h2 className="font-semibold text-lg mb-4">Profissionais</h2>
          <div className="space-y-2">
            {professionals.map((p: any) => (
              <div key={p.id} className="flex justify-between items-center p-2 bg-gray-50 rounded">
                <span className="font-medium">{p.name}</span>
                <span className="text-sm text-gray-500">{p.specialties?.join(', ')}</span>
              </div>
            ))}
          </div>
        </div>
        <div className="bg-white rounded-xl shadow-sm border p-6">
          <h2 className="font-semibold text-lg mb-4">Serviços</h2>
          <div className="space-y-2">
            {services.map((s: any) => (
              <div key={s.id} className="flex justify-between items-center p-2 bg-gray-50 rounded">
                <span className="font-medium">{s.name}</span>
                <span className="text-sm text-coral-500 font-medium">{s.price.toFixed(2)}€</span>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
