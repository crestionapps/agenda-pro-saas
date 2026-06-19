# Guia de Deploy — Colocar o AgendaPro online

Escolhe uma das opções abaixo. **Recomendo o Render** (mais simples para iniciantes).

---

## 🚀 Opção 1: Render (recomendado — gratuito)

O Render fornece PostgreSQL grátis + alojamento web.

### 1. Criar a base de dados

1. Vai a https://render.com e cria conta (login com GitHub)
2. Clica **"New +" → "PostgreSQL"**
3. Preenche:
   - **Name:** `agenda-pro-db`
   - **Database:** `agenda_pro`
   - **User:** `postgres`
4. Clica **"Create Database"**
5. Aguarda (2-3 min). Depois copia a **"Internal Database URL"** — guarda isto

### 2. Fazer deploy do backend

1. No Render, clica **"New +" → "Web Service"**
2. Liga o teu repositório GitHub (`crestionapps/agenda-pro-saas`)
3. Preenche:
   - **Name:** `agenda-pro-api`
   - **Root Directory:** `backend` (importante!)
   - **Runtime:** `Node`
   - **Build Command:** `npm install && npm run build`
   - **Start Command:** `npm start`
4. Em **"Advanced"** → **"Add Environment Variable"**:
   - `DATABASE_URL` = cola a "Internal Database URL" do passo 1
   - `JWT_SECRET` = gera uma string aleatória tipo `a7b3x9k2m4n8p1q5` (podes inventar)
   - `NODE_VERSION` = `18`
5. Clica **"Create Web Service"**
6. Aguarda o build (3-5 min)
7. Quando acabar, copia o URL do serviço (ex: `https://agenda-pro-api.onrender.com`)

### 3. Fazer deploy do frontend

**Opção A: Usar o mesmo serviço (mais simples)** — já está configurado!
O backend já serve o frontend automaticamente na rota `/`. Basta acederes ao URL do backend.

**Opção B: Separar o frontend no Vercel (melhor performance)**

1. Vai a https://vercel.com e cria conta (login com GitHub)
2. Clica **"Add New..." → "Project"**
3. Escolhe o repositório `agenda-pro-saas`
4. Em **"Root Directory"** seleciona `frontend`
5. Em **"Build and Output Settings"**:
   - **Framework Preset:** `Vite`
   - A Vercel deteta automaticamente
6. Em **"Environment Variables"**:
   - `VITE_API_URL` = URL do backend + `/api` (ex: `https://agenda-pro-api.onrender.com/api`)
7. Clica **"Deploy"**

### 4. Executar o seed (dados de exemplo)

No Render, vai ao teu Web Service → **"Shell"** no canto superior direito e executa:

```bash
npx tsx src/seed.ts
```

### 5. Aceder

- Se usaste Opção A: `https://agenda-pro-api.onrender.com`
- Se usaste Opção B: `https://agenda-pro-saas.vercel.app`

---

## 🚄 Opção 2: Railway (alternativa simples)

1. Vai a https://railway.com e cria conta (login com GitHub)
2. Clica **"New Project" → "Deploy from GitHub repo"**
3. Escolhe `crestionapps/agenda-pro-saas`
4. Clica **"Add a plugin" → "PostgreSQL"**
5. No serviço web, vai a **"Settings"**:
   - **Root Directory:** `backend`
   - **Build Command:** `npm install && npm run build`
   - **Start Command:** `npm start`
6. Adiciona as variáveis de ambiente (Railway injeta automaticamente a `DATABASE_URL` do PostgreSQL plugin)
7. Clica **"Deploy"**
8. Depois de deploy, vai a **"Shell"** e corre `npx tsx src/seed.ts`

---

## 🐳 Opção 3: VPS com Docker (controlo total)

### Pré-requisitos
- Servidor VPS (DigitalOcean, Hetzner, etc.) com Ubuntu 22+
- Docker e Docker Compose instalados

### 1. Criar ficheiro docker-compose.yml

Na raiz do projeto (`agendamento-saas/`), cria:

```yaml
# docker-compose.yml
services:
  db:
    image: postgres:16
    environment:
      POSTGRES_DB: agenda_pro
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: postgres123
    volumes:
      - pgdata:/var/lib/postgresql/data
    restart: always

  app:
    build: .
    ports:
      - "80:3001"
    environment:
      DATABASE_URL: postgresql://postgres:postgres123@db:5432/agenda_pro
      JWT_SECRET: muda-isto-para-uma-string-segura
      PORT: 3001
    depends_on:
      - db
    restart: always

volumes:
  pgdata:
```

### 2. Criar Dockerfile na raiz do projeto

```dockerfile
# Dockerfile
FROM node:18

WORKDIR /app

# Backend
COPY backend/package.json backend/
RUN cd backend && npm install

COPY backend/ ./backend/
COPY frontend/ ./frontend/

RUN cd frontend && npm install && npm run build
RUN cd backend && npx prisma generate && npm run build

EXPOSE 3001
CMD ["node", "backend/dist/server.js"]
```

### 3. Enviar para o servidor e correr

```bash
# No teu computador
git add -A
git commit -m "Adiciona Dockerfile e docker-compose"
git push

# No servidor VPS
git clone https://github.com/crestionapps/agenda-pro-saas.git
cd agenda-pro-saas
docker compose up -d

# Correr o seed
docker compose exec app npx tsx backend/src/seed.ts
```

---

## ⚙️ Variáveis de ambiente (produção)

| Variável | Obrigatória | Descrição |
|---|---|---|
| `DATABASE_URL` | Sim | URL da base de dados PostgreSQL |
| `JWT_SECRET` | Sim | Chave secreta para tokens JWT |
| `PORT` | Não | Porta do servidor (default: 3001) |

---

## ✅ Checklist de produção

Antes de abrir ao público:

- [ ] Mudar `JWT_SECRET` para uma string longa e aleatória
- [ ] Desativar o registo público ou adicionar confirmação de email
- [ ] Adicionar HTTPS (Render/Railway/Vercel fazem automaticamente)
- [ ] Configurar backups da base de dados
- [ ] Testar com vários utilizadores simultâneos

---

## 🔄 Atualizar o deploy

Sempre que fizeres alterações ao código:

```bash
git add -A
git commit -m "descrição das alterações"
git push
```

No Render/Railway: o deploy é automático ao fazer push para o GitHub.
No VPS: `docker compose up -d --build`
