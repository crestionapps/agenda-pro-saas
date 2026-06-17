import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { api } from '../lib/api';
import { useAuth } from '../contexts/AuthContext';

const typeLabels: Record<string, string> = {
  BARBERSHOP: 'Barbearia', HAIRDRESSER: 'Cabeleireiro', SPA: 'Spa',
  NAILS: 'Unhas', AESTHETICS: 'Estética', CLINIC: 'Clínica',
};

const weekDays = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

const categoryIcons: Record<string, string> = {
  Corte: '✂️', Barba: '🧔', Combo: '✨', Massagem: '💆',
  Facial: '🧴', Mãos: '💅', Pés: '🦶', Cor: '🎨',
  Tratamento: '💧', Design: '✏️',
};

export default function TenantPage() {
  const { slug } = useParams<{ slug: string }>();
  const { user } = useAuth();
  const [tenant, setTenant] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState<'servicos' | 'profissionais' | 'horarios' | 'avaliacoes'>('servicos');

  useEffect(() => {
    api.get(`/tenants/${slug}`)
      .then(setTenant)
      .finally(() => setLoading(false));
  }, [slug]);

  if (loading) return <div className="text-center py-20 text-gray-500">A carregar...</div>;
  if (!tenant) return <div className="text-center py-20 text-gray-500">Negócio não encontrado</div>;

  const groupedServices = tenant.services?.reduce((acc: any, s: any) => {
    const cat = s.category || 'Outros';
    if (!acc[cat]) acc[cat] = [];
    acc[cat].push(s);
    return acc;
  }, {} as Record<string, any[]>) || {};

  return (
    <div>
      <div
        className="h-64 md:h-80 bg-cover bg-center relative"
        style={{
          backgroundImage: tenant.coverImage
            ? `url(${tenant.coverImage})`
            : 'linear-gradient(135deg, #ff562e 0%, #f43f5e 100%)',
        }}
      >
        <div className="absolute inset-0 bg-black/30" />
      </div>

      <div className="max-w-6xl mx-auto px-4 -mt-20 relative z-10">
        <div className="flex flex-col md:flex-row md:items-end gap-6 mb-8">
          <div className="w-32 h-32 rounded-2xl bg-white shadow-xl flex items-center justify-center text-5xl font-bold text-coral-500 border-4 border-white shrink-0">
            {tenant.logo ? (
              <img src={tenant.logo} alt={tenant.name} className="w-full h-full rounded-2xl object-cover" />
            ) : (
              tenant.name.charAt(0)
            )}
          </div>
          <div className="flex-1 bg-white/90 backdrop-blur rounded-xl p-6 shadow-lg">
            <div className="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
              <div className="flex-1">
                <h1 className="text-3xl font-bold text-gray-900">{tenant.name}</h1>
                <div className="flex items-center gap-2 mt-1">
                  <span className="px-3 py-0.5 rounded-full text-sm font-medium bg-coral-100 text-coral-700">
                    {typeLabels[tenant.type]}
                  </span>
                  {tenant.reviews?.length > 0 && (
                    <span className="text-sm text-gray-500">
                      {(tenant.reviews.reduce((a: number, r: any) => a + r.rating, 0) / tenant.reviews.length).toFixed(1)}
                      {' '}★ ({tenant.reviews.length})
                    </span>
                  )}
                </div>
                <p className="text-gray-600 mt-3 leading-relaxed">{tenant.description}</p>
                <div className="flex flex-wrap gap-4 mt-3 text-sm text-gray-500">
                  {tenant.address && <span>📍 {tenant.address}</span>}
                  {tenant.phone && <span>📞 {tenant.phone}</span>}
                  {tenant.email && <span>✉️ {tenant.email}</span>}
                </div>
              </div>
              <Link
                to={`/negocio/${slug}/agendar`}
                className="bg-coral-500 text-white px-8 py-3 rounded-xl hover:bg-coral-600 font-medium text-center shadow-lg shadow-coral-200 whitespace-nowrap"
              >
                Agendar agora
              </Link>
            </div>
          </div>
        </div>

        <div className="flex gap-2 mb-8 overflow-x-auto pb-2">
          {(['servicos', 'profissionais', 'horarios', 'avaliacoes'] as const).map(tab => (
            <button
              key={tab}
              onClick={() => setActiveTab(tab)}
              className={`px-5 py-2.5 rounded-xl text-sm font-medium whitespace-nowrap transition ${
                activeTab === tab
                  ? 'bg-coral-500 text-white shadow-lg shadow-coral-200'
                  : 'bg-white text-gray-600 hover:bg-gray-50 border'
              }`}
            >
              {tab === 'servicos' && '📋 Serviços'}
              {tab === 'profissionais' && '👥 Profissionais'}
              {tab === 'horarios' && '🕐 Horários'}
              {tab === 'avaliacoes' && '⭐ Avaliações'}
            </button>
          ))}
        </div>

        {activeTab === 'servicos' && (
          <div className="space-y-8 mb-8">
            {Object.entries(groupedServices).map(([category, svcs]: [string, any]) => (
              <div key={category}>
                <div className="flex items-center gap-2 mb-4">
                  <span className="text-2xl">{categoryIcons[category] || '📌'}</span>
                  <h2 className="text-xl font-bold text-gray-800">{category}</h2>
                </div>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  {(svcs as any[]).map((service: any) => (
                    <div key={service.id} className="bg-white rounded-xl border hover:shadow-lg transition p-5">
                      <div className="flex justify-between items-start mb-3">
                        <div className="flex-1">
                          <h3 className="font-semibold text-gray-900">{service.name}</h3>
                          {service.description && (
                            <p className="text-sm text-gray-500 mt-1">{service.description}</p>
                          )}
                        </div>
                        <span className="text-lg font-bold text-coral-500 whitespace-nowrap ml-2">
                          {service.price.toFixed(2)}€
                        </span>
                      </div>
                      <div className="flex items-center gap-1 text-sm text-gray-400 mb-3">
                        <span>⏱ {service.duration} min</span>
                      </div>
                      {service.professionals?.length > 0 && (
                        <div className="border-t pt-3">
                          <p className="text-xs text-gray-400 mb-2">Profissionais:</p>
                          <div className="flex flex-wrap gap-2">
                            {service.professionals.map((p: any) => (
                              <div key={p.id} className="flex items-center gap-1.5 bg-gray-50 rounded-full px-3 py-1">
                                <div className="w-5 h-5 rounded-full bg-coral-100 flex items-center justify-center text-xs font-medium text-coral-500">
                                  {p.name.charAt(0)}
                                </div>
                                <span className="text-xs text-gray-600">{p.name}</span>
                              </div>
                            ))}
                          </div>
                        </div>
                      )}
                    </div>
                  ))}
                </div>
              </div>
            ))}
          </div>
        )}

        {activeTab === 'profissionais' && (
          <div className="mb-8">
            {tenant.professionals?.length === 0 ? (
              <div className="text-center py-12 text-gray-500">Nenhum profissional disponível</div>
            ) : (
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {tenant.professionals?.map((p: any) => (
                  <div key={p.id} className="bg-white rounded-xl border hover:shadow-lg transition p-6 text-center">
                    <div className="w-20 h-20 rounded-full mx-auto mb-4 overflow-hidden bg-coral-100 flex items-center justify-center">
                      {p.photo ? (
                        <img src={p.photo} alt={p.name} className="w-full h-full object-cover" />
                      ) : (
                        <span className="text-2xl font-bold text-coral-500">{p.name.charAt(0)}</span>
                      )}
                    </div>
                    <h3 className="font-semibold text-gray-900">{p.name}</h3>
                    {p.bio && <p className="text-sm text-gray-500 mt-1">{p.bio}</p>}
                    <div className="mt-3 space-y-1 text-sm text-gray-500">
                      {p.email && <p className="flex items-center justify-center gap-1">✉️ {p.email}</p>}
                      {p.phone && <p className="flex items-center justify-center gap-1">📞 {p.phone}</p>}
                    </div>
                    {p.specialties?.length > 0 && (
                      <div className="flex flex-wrap justify-center gap-1.5 mt-3">
                        {p.specialties.map((s: string) => (
                          <span key={s} className="px-2 py-0.5 bg-coral-50 text-coral-500 rounded-full text-xs font-medium">
                            {s}
                          </span>
                        ))}
                      </div>
                    )}
                    {p.services?.length > 0 && (
                      <div className="mt-3 pt-3 border-t">
                        <p className="text-xs text-gray-400 mb-1">Serviços:</p>
                        <div className="flex flex-wrap justify-center gap-1">
                          {p.services.map((s: any) => (
                            <span key={s.id} className="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">
                              {s.name}
                            </span>
                          ))}
                        </div>
                      </div>
                    )}
                  </div>
                ))}
              </div>
            )}
          </div>
        )}

        {activeTab === 'horarios' && (
          <div className="bg-white rounded-xl border p-6 mb-8">
            <h2 className="font-semibold text-lg mb-4">Horário de Funcionamento</h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
              {tenant.businessHours?.map((h: any) => (
                <div key={h.id} className={`flex justify-between items-center p-3 rounded-lg ${h.isOpen ? 'bg-green-50' : 'bg-red-50'}`}>
                  <span className="font-medium">{weekDays[h.dayOfWeek]}</span>
                  {h.isOpen ? (
                    <span className="text-green-700 font-medium">{h.openTime} - {h.closeTime}</span>
                  ) : (
                    <span className="text-red-500 font-medium">Fechado</span>
                  )}
                </div>
              ))}
            </div>
          </div>
        )}

        {activeTab === 'avaliacoes' && (
          <div className="bg-white rounded-xl border p-6 mb-8">
            <h2 className="font-semibold text-lg mb-4">Avaliações</h2>
            {tenant.reviews?.length === 0 ? (
              <p className="text-center py-8 text-gray-500">Ainda não há avaliações.</p>
            ) : (
              <div className="space-y-4">
                {tenant.reviews?.map((r: any) => (
                  <div key={r.id} className="border-b pb-4 last:border-0">
                    <div className="flex items-center gap-3 mb-2">
                      <div className="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-sm font-medium">
                        {r.customer?.name?.charAt(0) || '?'}
                      </div>
                      <div>
                        <p className="font-medium text-sm">{r.customer?.name}</p>
                        <div className="flex text-yellow-400 text-sm">{'★'.repeat(r.rating)}{'☆'.repeat(5 - r.rating)}</div>
                      </div>
                    </div>
                    {r.comment && <p className="text-gray-600 text-sm ml-11">{r.comment}</p>}
                    {r.managerReply && (
                      <div className="ml-11 mt-2 pl-3 border-l-2 border-coral-200">
                        <p className="text-xs text-coral-500 font-medium">Resposta do gestor</p>
                        <p className="text-sm text-gray-500">{r.managerReply}</p>
                      </div>
                    )}
                  </div>
                ))}
              </div>
            )}
          </div>
        )}
      </div>
    </div>
  );
}
