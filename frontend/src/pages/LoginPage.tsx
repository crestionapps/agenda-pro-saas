import { useState, FormEvent } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

export default function LoginPage() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const { login } = useAuth();
  const navigate = useNavigate();

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError('');
    try {
      await login(email, password);
      navigate('/');
    } catch (err: any) {
      setError(err.message);
    }
  };

  return (
    <div className="min-h-[80vh] flex items-center justify-center px-4 py-12">
      <div className="w-full max-w-md">
        <div className="text-center mb-8">
          <div className="w-14 h-14 bg-coral-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-coral-200">
            <span className="text-white font-bold text-xl">AP</span>
          </div>
          <h1 className="text-2xl font-bold text-gray-900">Bem-vindo de volta</h1>
          <p className="text-gray-500 mt-1">Entra na tua conta AgendaPro</p>
        </div>

        <div className="bg-white rounded-2xl shadow-sm border p-8">
          <form onSubmit={handleSubmit} className="space-y-4">
            {error && (
              <div className="bg-red-50 text-red-600 p-3 rounded-xl text-sm flex items-center gap-2">
                <span>⚠️</span> {error}
              </div>
            )}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
              <input
                type="email"
                value={email}
                onChange={e => setEmail(e.target.value)}
                required
                placeholder="teu@email.com"
                className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-coral-400 focus:border-coral-400 outline-none transition"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1.5">Senha</label>
              <input
                type="password"
                value={password}
                onChange={e => setPassword(e.target.value)}
                required
                placeholder="A tua senha"
                className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-coral-400 focus:border-coral-400 outline-none transition"
              />
            </div>
            <button type="submit" className="w-full btn-primary py-3">
              Entrar
            </button>
          </form>

          <div className="mt-6 text-center">
            <p className="text-sm text-gray-500">
              Não tens conta?{' '}
              <Link to="/registar" className="text-coral-500 hover:text-coral-600 font-medium hover:underline">
                Regista-te
              </Link>
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}
