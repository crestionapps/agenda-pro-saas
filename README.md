# AgendaPro — SaaS Multi-Tenant de Agendamentos

Plataforma para barbearias, cabeleireiros, spas, unhas, estética e clínicas.

**Stack:** Node.js + Express + Prisma + PostgreSQL · React + Vite + Tailwind CSS

---

## 🚀 Para começar (iniciantes)

> **És iniciante?** Lê o [`GUIA_INICIANTE.md`](GUIA_INICIANTE.md) — tem passos detalhados com prints mentais e resolução de problemas comuns.

### Instalação rápida (se já tens Node.js e PostgreSQL)

```bash
# 1. Cria a base de dados (no SQL Shell ou pgAdmin)
createdb -U postgres agendamento_saas

# 2. Backend
cd backend
npm install
cp .env.example .env   # edita a password se necessário
npx prisma generate
npx prisma db push
npx tsx src/seed.ts     # dados de exemplo
npm run dev

# 3. Frontend (noutro terminal)
cd frontend
npm install
npm run dev
```

Abre [http://localhost:5173](http://localhost:5173)

### Setup automático (Windows)

```powershell
# Clica com direito no setup.ps1 → "Executar com PowerShell"
# Ou no terminal:
.\setup.ps1
```

---

## 🧪 Credenciais de teste

| Papel | Email | Senha |
|---|---|---|
| Super Admin | admin@plataforma.pt | 123456 |
| Cliente | cliente@teste.pt | 123456 |
| Gerente Barbearia | gerente@barbearia-classica.pt | 123456 |
| Gerente Spa | gerente@spa-bem-estar.pt | 123456 |
| Gerente Salon | gerente@salon-charme.pt | 123456 |

---

## 📋 Funcionalidades

- **Marketplace** — Pesquisa por nome/localização, filtro por tipo
- **Página do negócio** — Banner, logo, serviços por categoria, profissionais com foto/contacto
- **Agendamento** — Selecionar serviço → profissional → data → slot (anti-overlap)
- **Painéis** — Cliente, Gestor, Admin Negócio, Admin Plataforma
- **Subscrições** — Planos com cotas de SMS/Email/WhatsApp

## 📁 Estrutura

```
backend/    → API (Express + Prisma)
frontend/   → UI (React + Vite + Tailwind)
```

---

## 📄 Licença

Proprietário.
