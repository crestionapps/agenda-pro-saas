import { useState, useEffect } from 'react';
import { api } from '../lib/api';
import { useAuth } from '../contexts/AuthContext';

export default function BusinessAdminDashboard() {
  const { user } = useAuth();
  const [professionals, setProfessionals] = useState<any[]>([]);
  const [services, setServices] = useState<any[]>([]);
  const [stats, setStats] = useState<any>(null);
  const [subscription, setSubscription] = useState<any>(null);
  const [tab, setTab] = useState<'stats' | 'profissionais' | 'servicos' | 'subscricao'>('stats');
  const [loading, setLoading] = useState(true);

  const tenantId = user?.tenantId;

  const loadData = async () => {
    if (!tenantId) return;
    setLoading(true);
    const [profs, svcs, sts, sub] = await Promise.all([
      api.get(`/professionals/tenant/${tenantId}`),
      api.get(`/services/tenant/${tenantId}`),
      api.get(`/tenants/${tenantId}/stats`),
      api.get(`/plans/tenant/${tenantId}`),
    ]);
    setProfessionals(profs);
    setServices(svcs);
    setStats(sts);
    setSubscription(sub);
    setLoading(false);
  };

  useEffect(() => { loadData(); }, [tenantId]);

  const addProfessional = async () => {
    const name = prompt('Nome do profissional:');
    if (!name) return;
    await api.post(`/professionals/tenant/${tenantId}`, { name });
    loadData();
  };

  const addService = async () => {
    const name = prompt('Nome do serviço:');
    if (!name) return;
    const duration = parseInt(prompt('Duração (minutos):') || '30');
    const price = parseFloat(prompt('Preço (€):') || '0');
    await api.post(`/services/tenant/${tenantId}`, { name, duration, price });
    loadData();
  };

  const toggleProfessional = async (id: string, active: boolean) => {
    if (active) {
      await api.delete(`/professionals/${id}`);
    } else {
      await api.put(`/professionals/${id}`, { isActive: true });
    }
    loadData();
  };

  const toggleService = async (id: string, active: boolean) => {
    if (active) {
      await api.delete(`/services/${id}`);
    } else {
      await api.put(`/services/${id}`, { isActive: true });
    }
    loadData();
  };

  if (loading) return <div className="text-center py-20 text-gray-500">A carregar...</div>;
  if (!tenantId) return <div className="text-center py-20 text-gray-500">Sem negócio associado</div>;

  return (
    <div className="max-w-5xl mx-auto px-4 py-8">
      <h1 className="text-2xl font-bold mb-6">Administração do Negócio</h1>

      <div className="flex gap-2 mb-6 flex-wrap">
        {(['stats', 'profissionais', 'servicos', 'subscricao'] as const).map(t => (
          <button key={t} onClick={() => setTab(t)}
            className={`px-4 py-2 rounded-lg text-sm ${tab === t ? 'bg-coral-500 text-white' : 'bg-white border text-gray-600 hover:bg-gray-50'}`}>
            {t === 'stats' ? 'Estatísticas' : t === 'profissionais' ? 'Profissionais' : t === 'servicos' ? 'Serviços' : 'Subscrição'}
          </button>
        ))}
      </div>

      {tab === 'stats' && stats && (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div className="bg-white p-6 rounded-xl shadow-sm border">
            <p className="text-3xl font-bold text-coral-500">{stats.totalAppointments}</p>
            <p className="text-sm text-gray-500">Total</p>
          </div>
          <div className="bg-white p-6 rounded-xl shadow-sm border">
            <p className="text-3xl font-bold text-green-600">{stats.monthlyAppointments}</p>
            <p className="text-sm text-gray-500">Este Mês</p>
          </div>
          <div className="bg-white p-6 rounded-xl shadow-sm border">
            <p className="text-3xl font-bold text-blue-600">{stats.totalProfessionals}</p>
            <p className="text-sm text-gray-500">Profissionais</p>
          </div>
          <div className="bg-white p-6 rounded-xl shadow-sm border">
            <p className="text-3xl font-bold text-purple-600">{stats.totalCustomers}</p>
            <p className="text-sm text-gray-500">Clientes</p>
          </div>
        </div>
      )}

      {tab === 'profissionais' && (
        <div className="bg-white rounded-xl shadow-sm border p-6">
          <div className="flex justify-between items-center mb-4">
            <h2 className="font-semibold text-lg">Profissionais</h2>
            <button onClick={addProfessional} className="bg-coral-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-coral-600">
              + Adicionar
            </button>
          </div>
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b text-left text-gray-500">
                <th className="pb-2">Nome</th>
                <th className="pb-2">Especialidades</th>
                <th className="pb-2">Agendamentos</th>
                <th className="pb-2">Status</th>
                <th className="pb-2">Ações</th>
              </tr>
            </thead>
            <tbody>
              {professionals.map((p: any) => (
                <tr key={p.id} className="border-b last:border-0">
                  <td className="py-3 font-medium">{p.name}</td>
                  <td className="py-3 text-gray-500">{p.specialties?.join(', ')}</td>
                  <td className="py-3">{p._count?.appointments ?? 0}</td>
                  <td className="py-3">
                    <span className={`px-2 py-0.5 rounded-full text-xs ${p.isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                      {p.isActive ? 'Ativo' : 'Inativo'}
                    </span>
                  </td>
                  <td className="py-3">
                    <button onClick={() => toggleProfessional(p.id, p.isActive)}
                      className="text-sm text-red-500 hover:text-red-700">
                      {p.isActive ? 'Desativar' : 'Ativar'}
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {tab === 'servicos' && (
        <div className="bg-white rounded-xl shadow-sm border p-6">
          <div className="flex justify-between items-center mb-4">
            <h2 className="font-semibold text-lg">Serviços</h2>
            <button onClick={addService} className="bg-coral-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-coral-600">
              + Adicionar
            </button>
          </div>
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b text-left text-gray-500">
                <th className="pb-2">Nome</th>
                <th className="pb-2">Duração</th>
                <th className="pb-2">Preço</th>
                <th className="pb-2">Status</th>
                <th className="pb-2">Ações</th>
              </tr>
            </thead>
            <tbody>
              {services.map((s: any) => (
                <tr key={s.id} className="border-b last:border-0">
                  <td className="py-3 font-medium">{s.name}</td>
                  <td className="py-3 text-gray-500">{s.duration} min</td>
                  <td className="py-3 font-medium text-coral-500">{s.price.toFixed(2)}€</td>
                  <td className="py-3">
                    <span className={`px-2 py-0.5 rounded-full text-xs ${s.isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                      {s.isActive ? 'Ativo' : 'Inativo'}
                    </span>
                  </td>
                  <td className="py-3">
                    <button onClick={() => toggleService(s.id, s.isActive)}
                      className="text-sm text-red-500 hover:text-red-700">
                      {s.isActive ? 'Desativar' : 'Ativar'}
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {tab === 'subscricao' && (
        <div className="space-y-6">
          <div className="bg-white rounded-xl shadow-sm border p-6">
            <h2 className="font-semibold text-lg mb-4">Plano Atual</h2>
            {subscription?.hasSubscription ? (
              <div>
                <div className="flex items-center justify-between mb-4">
                  <div>
                    <p className="text-xl font-bold text-coral-500">{subscription.plan?.name}</p>
                    <p className="text-sm text-gray-500">{subscription.plan?.description}</p>
                    <p className="text-lg font-semibold mt-1">{subscription.plan?.price.toFixed(2)}€ / mês</p>
                  </div>
                  <span className="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                    {subscription.isActive ? 'Ativo' : 'Inativo'}
                  </span>
                </div>
              </div>
            ) : (
              <p className="text-gray-500">Sem subscrição ativa. Contacte o administrador da plataforma.</p>
            )}
          </div>

          {subscription?.hasSubscription && (
            <div className="bg-white rounded-xl shadow-sm border p-6">
              <h2 className="font-semibold text-lg mb-4">Cota de Notificações</h2>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {(['sms', 'email', 'whatsapp'] as const).map(type => {
                  const q = subscription.quotas?.[type];
                  if (!q) return null;
                  const pct = q.total > 0 ? Math.round((q.used / q.total) * 100) : 0;
                  return (
                    <div key={type} className="bg-gray-50 rounded-lg p-4">
                      <p className="text-sm font-medium text-gray-500 uppercase mb-1">{type === 'whatsapp' ? 'WhatsApp' : type.toUpperCase()}</p>
                      <p className="text-2xl font-bold">{q.remaining} / {q.total}</p>
                      <div className="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div className={`h-2 rounded-full ${pct >= 80 ? 'bg-red-500' : pct >= 50 ? 'bg-yellow-500' : 'bg-green-500'}`}
                          style={{ width: `${Math.min(100, pct)}%` }} />
                      </div>
                      <p className="text-xs text-gray-500 mt-1">{q.used} usados ({pct}%)</p>
                    </div>
                  );
                })}
              </div>
            </div>
          )}

          <div className="bg-white rounded-xl shadow-sm border p-6">
            <h2 className="font-semibold text-lg mb-4">Planos Disponíveis</h2>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              {[10, 20, 30].map(i => (
                <div key={i} className="border rounded-lg p-4 text-center">
                  <p className="font-semibold">Plano Exemplo {i / 10}x</p>
                  <p className="text-2xl font-bold text-coral-500 mt-2">{(i * 5).toFixed(2)}€</p>
                  <p className="text-sm text-gray-500 mt-1">Contacte o admin para mais informações</p>
                </div>
              ))}
            </div>
            <p className="text-sm text-gray-400 mt-4 text-center">
              Para alterar de plano ou fazer topup, contacte o administrador da plataforma.
            </p>
          </div>
        </div>
      )}
    </div>
  );
}
