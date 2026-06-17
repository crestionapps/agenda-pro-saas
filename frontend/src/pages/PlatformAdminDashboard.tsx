import { useState, useEffect } from 'react';
import { api } from '../lib/api';

export default function PlatformAdminDashboard() {
  const [dashboard, setDashboard] = useState<any>(null);
  const [plans, setPlans] = useState<any[]>([]);
  const [tenants, setTenants] = useState<any[]>([]);
  const [tab, setTab] = useState<'dashboard' | 'planos' | 'subs'>('dashboard');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    Promise.all([
      api.get('/admin/dashboard'),
      api.get('/plans/all'),
      api.get('/admin/tenants?limit=100'),
    ]).then(([d, p, t]) => {
      setDashboard(d);
      setPlans(p);
      setTenants(t.tenants);
    }).finally(() => setLoading(false));
  }, []);

  const createPlan = async () => {
    const name = prompt('Nome do plano:');
    if (!name) return;
    const price = parseFloat(prompt('Preço (€):') || '0');
    const smsLimit = parseInt(prompt('Limite SMS:') || '0');
    const emailLimit = parseInt(prompt('Limite Email:') || '0');
    const whatsappLimit = parseInt(prompt('Limite WhatsApp:') || '0');
    await api.post('/plans', { name, price, smsLimit, emailLimit, whatsappLimit });
    setPlans(await api.get('/plans/all'));
  };

  const togglePlan = async (id: string, active: boolean) => {
    await api.put(`/plans/${id}`, { isActive: !active });
    setPlans(await api.get('/plans/all'));
  };

  const assignPlan = async (tenantId: string) => {
    const planId = prompt('ID do plano:');
    if (!planId) return;
    await api.post(`/plans/assign/${tenantId}`, { planId });
    alert('Plano atribuído com sucesso!');
  };

  const topup = async (tenantId: string) => {
    const sms = parseInt(prompt('SMS extra:') || '0');
    const email = parseInt(prompt('Email extra:') || '0');
    const whatsapp = parseInt(prompt('WhatsApp extra:') || '0');
    await api.post(`/plans/topup/${tenantId}`, { sms, email, whatsapp });
    alert('Topup realizado!');
  };

  if (loading) return <div className="text-center py-20 text-gray-500">A carregar...</div>;
  if (!dashboard) return <div className="text-center py-20 text-gray-500">Erro ao carregar dados</div>;

  const { kpis, tenantsByType, appointmentsByStatus, recentAppointments, topTenants } = dashboard;

  return (
    <div className="max-w-7xl mx-auto px-4 py-8">
      <h1 className="text-2xl font-bold mb-6">Administração da Plataforma</h1>

      <div className="flex gap-2 mb-6">
        {(['dashboard', 'planos', 'subs'] as const).map(t => (
          <button key={t} onClick={() => setTab(t)}
            className={`px-4 py-2 rounded-lg text-sm ${tab === t ? 'bg-coral-500 text-white' : 'bg-white border text-gray-600 hover:bg-gray-50'}`}>
            {t === 'dashboard' ? 'Dashboard' : t === 'planos' ? 'Planos' : 'Subscrições'}
          </button>
        ))}
      </div>

      {tab === 'dashboard' && (
        <>
          <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
            <div className="bg-white p-4 rounded-xl shadow-sm border">
              <p className="text-2xl font-bold text-coral-500">{kpis.totalTenants}</p>
              <p className="text-xs text-gray-500">Total Negócios</p>
              <p className="text-xs text-green-600">{kpis.activeTenants} ativos</p>
            </div>
            <div className="bg-white p-4 rounded-xl shadow-sm border">
              <p className="text-2xl font-bold text-blue-600">{kpis.totalUsers}</p>
              <p className="text-xs text-gray-500">Utilizadores</p>
            </div>
            <div className="bg-white p-4 rounded-xl shadow-sm border">
              <p className="text-2xl font-bold text-green-600">{kpis.totalProfessionals}</p>
              <p className="text-xs text-gray-500">Profissionais</p>
            </div>
            <div className="bg-white p-4 rounded-xl shadow-sm border">
              <p className="text-2xl font-bold text-purple-600">{kpis.totalAppointments}</p>
              <p className="text-xs text-gray-500">Total Agendamentos</p>
            </div>
            <div className="bg-white p-4 rounded-xl shadow-sm border">
              <p className="text-2xl font-bold text-orange-600">{kpis.monthlyAppointments}</p>
              <p className="text-xs text-gray-500">Agendamentos (mês)</p>
            </div>
            <div className="bg-white p-4 rounded-xl shadow-sm border">
              <p className="text-2xl font-bold text-red-600">{kpis.totalRevenue?.toFixed(0)}€</p>
              <p className="text-xs text-gray-500">Receita Total</p>
              <p className="text-xs text-green-600">{kpis.monthlyRevenue?.toFixed(0)}€ este mês</p>
            </div>
          </div>

          <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div className="bg-white p-4 rounded-xl shadow-sm border">
              <p className="text-2xl font-bold text-yellow-600">{kpis.plansCount}</p>
              <p className="text-xs text-gray-500">Planos Ativos</p>
            </div>
            <div className="bg-white p-4 rounded-xl shadow-sm border">
              <p className="text-2xl font-bold text-teal-600">{kpis.activeSubscriptions}</p>
              <p className="text-xs text-gray-500">Subscrições Ativas</p>
            </div>
            <div className="bg-white p-4 rounded-xl shadow-sm border">
              <p className="text-2xl font-bold text-cyan-600">{kpis.subscriptionRevenue?.toFixed(0)}€</p>
              <p className="text-xs text-gray-500">Receita Subscrições</p>
            </div>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div className="bg-white rounded-xl shadow-sm border p-6">
              <h2 className="font-semibold text-lg mb-4">Negócios por Tipo</h2>
              <div className="space-y-3">
                {tenantsByType.map((t: any) => (
                  <div key={t.type} className="flex justify-between items-center">
                    <span className="text-gray-700">{t.type}</span>
                    <div className="flex items-center gap-2">
                      <div className="bg-coral-100 rounded-full h-2" style={{ width: `${Math.min(100, (t._count / Math.max(...tenantsByType.map((x: any) => x._count)) * 100))}px` }} />
                      <span className="font-semibold text-sm">{t._count}</span>
                    </div>
                  </div>
                ))}
              </div>
            </div>
            <div className="bg-white rounded-xl shadow-sm border p-6">
              <h2 className="font-semibold text-lg mb-4">Agendamentos por Status</h2>
              <div className="space-y-3">
                {appointmentsByStatus.map((s: any) => (
                  <div key={s.status} className="flex justify-between items-center">
                    <span className="text-gray-700">{s.status}</span>
                    <div className="flex items-center gap-2">
                      <div className="bg-green-100 rounded-full h-2" style={{ width: `${Math.min(100, (s.count / Math.max(...appointmentsByStatus.map((x: any) => x.count)) * 100))}px` }} />
                      <span className="font-semibold text-sm">{s.count}</span>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div className="bg-white rounded-xl shadow-sm border p-6">
              <h2 className="font-semibold text-lg mb-4">Top Negócios</h2>
              <div className="space-y-3">
                {topTenants.slice(0, 5).map((t: any) => (
                  <div key={t.id} className="flex justify-between items-center p-2 bg-gray-50 rounded">
                    <div className="flex items-center gap-3">
                      <div className="w-8 h-8 bg-coral-100 rounded-full flex items-center justify-center text-sm font-bold text-coral-500">
                        {t.name?.charAt(0)}
                      </div>
                      <div>
                        <p className="font-medium text-sm">{t.name}</p>
                        <p className="text-xs text-gray-500">{t.type}</p>
                      </div>
                    </div>
                    <span className="font-semibold text-sm">{t.appointmentCount} agendamentos</span>
                  </div>
                ))}
              </div>
            </div>
            <div className="bg-white rounded-xl shadow-sm border p-6">
              <h2 className="font-semibold text-lg mb-4">Últimos Agendamentos</h2>
              <div className="space-y-2">
                {recentAppointments.map((a: any) => (
                  <div key={a.id} className="flex justify-between items-center p-2 bg-gray-50 rounded text-sm">
                    <div>
                      <p className="font-medium">{a.customer?.name}</p>
                      <p className="text-xs text-gray-500">{a.tenant?.name} - {a.service?.name}</p>
                    </div>
                    <p className="text-xs text-gray-500">{new Date(a.createdAt).toLocaleDateString('pt-PT')}</p>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </>
      )}

      {tab === 'planos' && (
        <div className="bg-white rounded-xl shadow-sm border p-6">
          <div className="flex justify-between items-center mb-4">
            <h2 className="font-semibold text-lg">Planos de Subscrição</h2>
            <button onClick={createPlan} className="bg-coral-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-coral-600">
              + Novo Plano
            </button>
          </div>
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b text-left text-gray-500">
                <th className="pb-2">Nome</th>
                <th className="pb-2">Preço</th>
                <th className="pb-2">SMS</th>
                <th className="pb-2">Email</th>
                <th className="pb-2">WhatsApp</th>
                <th className="pb-2">Status</th>
                <th className="pb-2">Ações</th>
              </tr>
            </thead>
            <tbody>
              {plans.map((p: any) => (
                <tr key={p.id} className="border-b last:border-0">
                  <td className="py-3 font-medium">{p.name}</td>
                  <td className="py-3">{p.price.toFixed(2)}€</td>
                  <td className="py-3">{p.smsLimit}</td>
                  <td className="py-3">{p.emailLimit}</td>
                  <td className="py-3">{p.whatsappLimit}</td>
                  <td className="py-3">
                    <span className={`px-2 py-0.5 rounded-full text-xs ${p.isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                      {p.isActive ? 'Ativo' : 'Inativo'}
                    </span>
                  </td>
                  <td className="py-3">
                    <button onClick={() => togglePlan(p.id, p.isActive)} className="text-sm text-red-500 hover:text-red-700">
                      {p.isActive ? 'Desativar' : 'Ativar'}
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {tab === 'subs' && (
        <div className="bg-white rounded-xl shadow-sm border p-6">
          <h2 className="font-semibold text-lg mb-4">Gerir Subscrições</h2>
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b text-left text-gray-500">
                <th className="pb-2">Negócio</th>
                <th className="pb-2">Ações</th>
              </tr>
            </thead>
            <tbody>
              {tenants.map((t: any) => (
                <tr key={t.id} className="border-b last:border-0">
                  <td className="py-3 font-medium">{t.name}</td>
                  <td className="py-3 flex gap-2">
                    <button onClick={() => assignPlan(t.id)} className="px-2 py-1 bg-coral-100 text-coral-600 rounded text-xs hover:bg-coral-200">
                      Atribuir Plano
                    </button>
                    <button onClick={() => topup(t.id)} className="px-2 py-1 bg-green-100 text-green-700 rounded text-xs hover:bg-green-200">
                      Topup
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}
