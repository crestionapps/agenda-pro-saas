import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { api } from '../lib/api';

const typeLabels: Record<string, string> = {
  BARBERSHOP: 'Barbearia',
  HAIRDRESSER: 'Cabeleireiro',
  SPA: 'Spa & Massagem',
  NAILS: 'Unhas',
  AESTHETICS: 'Estética',
  CLINIC: 'Clínica',
};

const typeIcons: Record<string, string> = {
  BARBERSHOP: '💈',
  HAIRDRESSER: '💇',
  SPA: '🧖',
  NAILS: '💅',
  AESTHETICS: '✨',
  CLINIC: '🏥',
};

const categories = [
  { key: 'BARBERSHOP', label: 'Barbearia', icon: '💈', bg: 'bg-amber-50', text: 'text-amber-600' },
  { key: 'HAIRDRESSER', label: 'Cabeleireiro', icon: '💇‍♀️', bg: 'bg-pink-50', text: 'text-pink-600' },
  { key: 'SPA', label: 'Spa & Massagem', icon: '🧖', bg: 'bg-green-50', text: 'text-green-600' },
  { key: 'NAILS', label: 'Manicure & Pedicure', icon: '💅', bg: 'bg-purple-50', text: 'text-purple-600' },
  { key: 'AESTHETICS', label: 'Estética', icon: '✨', bg: 'bg-orange-50', text: 'text-orange-600' },
  { key: 'CLINIC', label: 'Clínica', icon: '🏥', bg: 'bg-red-50', text: 'text-red-600' },
];

const cities = [
  { name: 'Lisboa', slug: 'lisboa' },
  { name: 'Porto', slug: 'porto' },
  { name: 'Braga', slug: 'braga' },
  { name: 'Coimbra', slug: 'coimbra' },
  { name: 'Faro', slug: 'faro' },
  { name: 'Funchal', slug: 'funchal' },
];

export default function HomePage() {
  const [tenants, setTenants] = useState<any[]>([]);
  const [search, setSearch] = useState('');
  const [typeFilter, setTypeFilter] = useState('');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get(`/tenants?search=${search}&type=${typeFilter}&limit=12`)
      .then(data => setTenants(data.tenants))
      .finally(() => setLoading(false));
  }, [search, typeFilter]);

  return (
    <div>
      <section className="bg-gradient-to-br from-coral-500 via-rose-500 to-coral-600 text-white relative overflow-hidden">
        <div className="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMiIvPjwvZz48L2c+PC9zdmc+')] opacity-50" />
        <div className="max-w-5xl mx-auto px-4 py-20 md:py-28 relative">
          <div className="text-center mb-10">
            <h1 className="text-4xl md:text-6xl font-extrabold mb-4 tracking-tight">
              Encontra o teu<br />especialista de beleza
            </h1>
            <p className="text-lg text-coral-100 max-w-xl mx-auto">
              Marca consultas online com os melhores profissionais perto de ti
            </p>
          </div>

          <div className="max-w-3xl mx-auto bg-white rounded-2xl p-2 shadow-2xl">
            <div className="flex flex-col md:flex-row gap-2">
              <div className="flex-1 relative">
                <span className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
                <input
                  type="text"
                  placeholder="Pesquisar por nome, localização..."
                  value={search}
                  onChange={e => setSearch(e.target.value)}
                  className="w-full pl-10 pr-4 py-3.5 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-coral-300"
                />
              </div>
              <select
                value={typeFilter}
                onChange={e => setTypeFilter(e.target.value)}
                className="md:w-48 px-4 py-3.5 rounded-xl text-gray-700 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-coral-300 cursor-pointer"
              >
                <option value="">Todas as categorias</option>
                {categories.map(c => (
                  <option key={c.key} value={c.key}>{c.icon} {c.label}</option>
                ))}
              </select>
              <button
                onClick={() => {}}
                className="btn-primary px-8 py-3.5 whitespace-nowrap"
              >
                Pesquisar
              </button>
            </div>
          </div>

          <div className="flex justify-center gap-2 mt-6 text-sm text-coral-100">
            <span>Popular:</span>
            {categories.slice(0, 4).map(c => (
              <button
                key={c.key}
                onClick={() => { setTypeFilter(c.key); setSearch(''); }}
                className="px-3 py-1 rounded-full bg-white/10 hover:bg-white/20 transition"
              >
                {c.label}
              </button>
            ))}
          </div>
        </div>
      </section>

      <section className="max-w-7xl mx-auto px-4 -mt-8 relative z-10">
        <div className="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div className="grid grid-cols-3 md:grid-cols-6 gap-4">
            {categories.map(cat => (
              <button
                key={cat.key}
                onClick={() => setTypeFilter(typeFilter === cat.key ? '' : cat.key)}
                className={`flex flex-col items-center gap-2 p-4 rounded-xl transition ${
                  typeFilter === cat.key
                    ? 'bg-coral-50 ring-2 ring-coral-300'
                    : 'hover:bg-gray-50'
                }`}
              >
                <div className={`w-12 h-12 ${cat.bg} rounded-xl flex items-center justify-center text-2xl`}>
                  {cat.icon}
                </div>
                <span className={`text-xs font-medium text-center ${typeFilter === cat.key ? 'text-coral-600' : 'text-gray-600'}`}>
                  {cat.label}
                </span>
              </button>
            ))}
          </div>
        </div>
      </section>

      <section className="max-w-7xl mx-auto px-4 py-12">
        <div className="flex items-center justify-between mb-8">
          <div>
            <h2 className="text-2xl font-bold text-gray-900">
              {typeFilter ? typeLabels[typeFilter] || 'Resultados' : 'Negócios em destaque'}
            </h2>
            <p className="text-sm text-gray-500 mt-1">
              {loading ? 'A carregar...' : `${tenants.length} negócios encontrados`}
            </p>
          </div>
          {typeFilter && (
            <button onClick={() => { setTypeFilter(''); setSearch(''); }}
              className="text-sm text-coral-500 hover:text-coral-600 font-medium">
              Limpar filtros
            </button>
          )}
        </div>

        {loading ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[1, 2, 3, 4, 5, 6].map(i => (
              <div key={i} className="card p-5 animate-pulse">
                <div className="flex items-start gap-4">
                  <div className="w-14 h-14 bg-gray-200 rounded-xl" />
                  <div className="flex-1 space-y-2">
                    <div className="h-4 bg-gray-200 rounded w-3/4" />
                    <div className="h-3 bg-gray-100 rounded w-1/2" />
                    <div className="h-3 bg-gray-100 rounded w-2/3" />
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : tenants.length === 0 ? (
          <div className="text-center py-16">
            <div className="text-5xl mb-4">🔍</div>
            <p className="text-gray-500 text-lg">Nenhum negócio encontrado</p>
            <p className="text-gray-400 text-sm mt-1">Tenta alterar os filtros ou pesquisa</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {tenants.map(t => (
              <Link
                key={t.id}
                to={`/negocio/${t.slug}`}
                className="card card-hover overflow-hidden"
              >
                <div className="h-32 bg-gradient-to-br from-coral-100 to-rose-100 relative">
                  {t.coverImage && (
                    <img src={t.coverImage} alt="" className="w-full h-full object-cover" />
                  )}
                  <div className="absolute top-3 left-3">
                    <span className={`badge ${t.type === 'BARBERSHOP' ? 'bg-amber-100 text-amber-800' : t.type === 'SPA' ? 'bg-green-100 text-green-800' : 'bg-pink-100 text-pink-800'}`}>
                      {typeIcons[t.type]} {typeLabels[t.type]}
                    </span>
                  </div>
                </div>
                <div className="p-5">
                  <div className="flex items-start gap-3">
                    <div className="w-12 h-12 bg-coral-100 rounded-xl flex items-center justify-center text-lg font-bold text-coral-600 shrink-0 -mt-8 border-2 border-white shadow-sm">
                      {t.logo ? (
                        <img src={t.logo} alt="" className="w-full h-full rounded-xl object-cover" />
                      ) : (
                        t.name.charAt(0)
                      )}
                    </div>
                    <div className="flex-1 min-w-0">
                      <h3 className="font-semibold text-gray-900 truncate">{t.name}</h3>
                      {t.address && (
                        <p className="text-sm text-gray-500 truncate flex items-center gap-1">
                          📍 {t.address}
                        </p>
                      )}
                      <div className="flex items-center gap-3 mt-2 text-xs text-gray-400">
                        <span>{t.professionalCount} profissionais</span>
                        <span>•</span>
                        <span>{t.reviewCount} avaliações</span>
                      </div>
                    </div>
                  </div>
                  {t.description && (
                    <p className="text-sm text-gray-500 mt-3 line-clamp-2 border-t pt-3">{t.description}</p>
                  )}
                </div>
              </Link>
            ))}
          </div>
        )}
      </section>

      <section className="bg-white py-16 border-t border-gray-100">
        <div className="max-w-7xl mx-auto px-4">
          <div className="text-center mb-10">
            <h2 className="text-2xl font-bold text-gray-900">Encontra profissionais nas principais cidades</h2>
            <p className="text-gray-500 mt-2">Escolhe a tua cidade e descobre os melhores negócios</p>
          </div>
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            {cities.map(city => (
              <button
                key={city.slug}
                onClick={() => setSearch(city.name)}
                className="card p-4 text-center hover:ring-2 hover:ring-coral-200 card-hover"
              >
                <div className="text-2xl mb-1">📍</div>
                <p className="font-medium text-gray-900 text-sm">{city.name}</p>
                <p className="text-xs text-gray-400 mt-0.5">Ver negócios</p>
              </button>
            ))}
          </div>
        </div>
      </section>

      <section className="max-w-7xl mx-auto px-4 py-16">
        <div className="bg-gradient-to-br from-coral-500 to-rose-600 rounded-3xl p-8 md:p-12 text-white text-center relative overflow-hidden">
          <div className="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMiIvPjwvZz48L2c+PC9zdmc+')] opacity-50" />
          <div className="relative">
            <h2 className="text-3xl md:text-4xl font-extrabold mb-4">Tens um negócio?</h2>
            <p className="text-coral-100 max-w-lg mx-auto mb-8">
              Cadastra a tua empresa no AgendaPro e começa a receber agendamentos online
            </p>
            <a href="#" className="inline-block bg-white text-coral-600 px-8 py-3.5 rounded-xl font-semibold hover:bg-coral-50 transition shadow-xl">
              Cadastrar minha empresa
            </a>
          </div>
        </div>
      </section>
    </div>
  );
}
