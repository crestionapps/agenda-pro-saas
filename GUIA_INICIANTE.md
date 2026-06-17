# Guia Completo para Iniciantes — AgendaPro

Este guia ensina-te a instalar e correr o AgendaPro no teu computador do zero.

---

## 📦 O que precisas de instalar

### 1. Node.js (versão 18 ou superior)
- Vai a https://nodejs.org
- Descarrega a versão "LTS" (à esquerda)
- Abre o instalador e clica "Next" até acabar
- **Verifica se instalou corretamente:** Abre o terminal (PowerShell) e escreve:
  ```powershell
  node --version
  npm --version
  ```
  Deve mostrar números tipo `v18.x.x` e `9.x.x`

### 2. PostgreSQL (base de dados)
- Vai a https://www.postgresql.org/download/windows/
- Descarrega o instalador para Windows
- Durante a instalação:
  - **Password:** define `postgres` (para facilitar) ou a que quiseres
  - **Port:** deixa `5432`
- No final, desmarca "Stack Builder" e clica "Finish"

### 3. VS Code (editor de código)
- Vai a https://code.visualstudio.com
- Descarrega e instala (sempre "Next")
- **Recomendo instalar a extensão "Prisma"** — abre VS Code, clica nas extensões (ícone de blocos à esquerda), pesquisa "Prisma" e instala

---

## 🚀 Passo a passo para correr o projeto

### Passo 1: Abrir o terminal

Clica com o botão direito no menu Iniciar → "Windows PowerShell" ou "Terminal"

Navega até à pasta do projeto:
```powershell
cd C:\Users\LUAM\Desktop\Luis\opencode\agendamento-saas
```

### Passo 2: Criar a base de dados

Abre o "SQL Shell (psql)" que veio com o PostgreSQL ou usa este comando no PowerShell:

```powershell
& "C:\Program Files\PostgreSQL\17\bin\createdb.exe" -U postgres agendamento_saas
```

Se pedir password, mete `postgres` (ou a que definiste na instalação).

> ⚠️ Se der erro "createdb não encontrado", vai a https://www.postgresql.org/download/windows/ e instala. A versão na pasta pode ser `16` ou `15` em vez de `17`.

### Passo 3: Configurar o Backend

```powershell
cd backend
npm install
```

Isto vai instalar todas as dependências (espera 1-2 minutos).

Agora vamos criar a base de dados com as tabelas:

```powershell
npx prisma generate
npx prisma db push
```

Se tudo correu bem, deves ver mensagens verdes sem erros.

Agora vamos meter dados de exemplo:

```powershell
npx tsx src/seed.ts
```

Deves ver:
```
Super admin: admin@plataforma.pt
Manager barbearia-classica: gerente@barbearia-classica.pt
... (mais linhas)
Planos criados
Subscrição: barbearia-classica -> Básico
Cliente: cliente@teste.pt
```

### Passo 4: Iniciar o servidor Backend

```powershell
npm run dev
```

Deves ver: `Server running on port 3001`

**Deixa este terminal aberto** e abre um segundo terminal.

### Passo 5: Configurar e iniciar o Frontend

No segundo terminal:

```powershell
cd C:\Users\LUAM\Desktop\Luis\opencode\agendamento-saas\frontend
npm install
npm run dev
```

Deves ver:
```
VITE v6.x.x  ready in XXX ms
➜  Local:   http://localhost:5173/
```

### Passo 6: Abrir no browser

Abre o Chrome ou Edge e vai a: **http://localhost:5173**

---

## 🧪 Credenciais de teste

| Papel | Email | Senha |
|---|---|---|
| Administrador da plataforma | admin@plataforma.pt | 123456 |
| Cliente | cliente@teste.pt | 123456 |
| Gerente Barbearia Clássica | gerente@barbearia-classica.pt | 123456 |
| Gerente Spa Bem-Estar | gerente@spa-bem-estar.pt | 123456 |
| Gerente Salon Charme | gerente@salon-charme.pt | 123456 |

---

## 🖥️ O que vais ver

1. **Página inicial** — Pesquisa negócios, filtra por categoria (Barbearia, Spa, etc.)
2. **Clica num negócio** — Vês banner, logo, serviços com preços, profissionais, horários
3. **Clica "Agendar agora"** — Escolhes serviço → profissional → data → horário
4. **Painel do cliente** — Vês os teus agendamentos e podes cancelar
5. **Painel do gestor** — Vês agendamentos do dia, podes confirmar/cancelar
6. **Admin plataforma** — Vês KPIs, geres planos de subscrição

---

## ❓ Problemas comuns

### "Porta 3001 já está em uso"
No ficheiro `backend/.env`, muda `PORT=3001` para `PORT=3002`

### "ECONNREFUSED" na base de dados
O PostgreSQL não está a correr. Abre "Services" (serviços Windows), procura "postgresql", clica com direito → "Start"

### "Cannot find module @prisma/client"
Corre: `npx prisma generate`

### Erro no seed "Unique constraint failed"
O seed já foi corrido antes. É normal — ele usa `upsert` para evitar duplicados.

---

## 📁 Estrutura do projeto

```
agendamento-saas/
├── backend/
│   ├── prisma/schema.prisma   ← Modelos da base de dados
│   ├── src/
│   │   ├── api/               ← Rotas do servidor (auth, tenants, etc.)
│   │   ├── lib/               ← Config (prisma, jwt, erros)
│   │   └── middleware/        ← Autenticação, validação
│   └── .env                   ← Config da base de dados
├── frontend/
│   ├── src/
│   │   ├── components/        ← Layout, componentes reutilizáveis
│   │   ├── contexts/          ← Autenticação (AuthContext)
│   │   ├── lib/               ← API client
│   │   └── pages/             ← Páginas da aplicação
│   └── package.json
└── README.md
```
