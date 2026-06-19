# Guia de Deploy — Colocar o AgendaPro online

Escolhe a opção que tens disponível:

---

## 🚄 Opção 1: Railway (gratuito para começar — o mais fácil)

**Railway é o mais rápido:** não precisas de configurar nada manualmente. Só ligar o GitHub.

### 1. Criar conta

1. Vai a https://railway.com
2. Clica **"Sign in with GitHub"** — autoriza o Railway a aceder ao teu repositório

### 2. Fazer deploy

1. No dashboard, clica **"New Project" → "Deploy from GitHub repo"**
2. Escolhe o repositório: `crestionapps/agenda-pro-saas`
3. O Railway vai começar o deploy **mas vai falhar** porque falta a base de dados — é normal

### 3. Adicionar PostgreSQL

1. No projeto, clica **"Add a plugin"** (ou o "+" no topo)
2. Escolhe **"Database" → "PostgreSQL"**
3. Aguarda 1-2 minutos até o plugin ficar verde (✓ Running)

### 4. Configurar o Web Service

1. Clica no teu **Web Service** (o que tem o nome do repositório)
2. Vai a **"Settings"** e configura:

   **Root Directory:**
   ```
   backend
   ```

   **Build Command:**
   ```
   npm install && npm run build
   ```

   **Start Command:**
   ```
   npm start
   ```

   **Healthcheck Path:**
   ```
   /api/health
   ```

### 5. Adicionar variáveis de ambiente

Ainda em **Settings → Environment Variables**, clica **"New Variable"**:

| Nome | Valor |
|---|---|
| `JWT_SECRET` | `agendapro_secret_2026` (ou inventa outra) |
| `NODE_VERSION` | `18` |

**Importante:** A `DATABASE_URL` é injetada automaticamente pelo Railway quando adicionas o plugin PostgreSQL — **não precisas** de a configurar manualmente.

### 6. Re-fazer o deploy

1. Vai ao topo do projeto e clica **"Deploy"** (ou faz `git push` no teu repositório)
2. Aguarda o build (3-5 min na primeira vez)
3. Quando o deploy estiver verde (✓), clica no nome do serviço web
4. Vai a **"Settings"** e copia o **URL** (ex: `https://agenda-pro-saas.up.railway.app`)

### 7. Executar o seed (dados de exemplo)

1. No projeto Railway, clica no teu **Web Service**
2. Vai a **"Shell"** (no canto superior direito)
3. Cola e executa:
   ```bash
   cd backend && npx tsx src/seed.ts
   ```
4. Vais ver as mensagens de dados criados (admin, clientes, serviços, etc.)

### 8. Aceder à plataforma

Abre no browser o URL que copiaste (ex: `https://agenda-pro-saas.up.railway.app`)

**Credenciais de teste:**

| Papel | Email | Senha |
|---|---|---|
| Super Admin | admin@plataforma.pt | 123456 |
| Cliente | cliente@teste.pt | 123456 |
| Gerente | gerente@barbearia-classica.pt | 123456 |

---

## 🔄 Atualizar o deploy

Sempre que fizeres alterações, o Railway faz deploy automático quando dás `git push`:

```bash
git add -A
git commit -m "descrição das alterações"
git push
```

Se quiseres forçar um novo deploy manual: clica **"Deploy"** no dashboard.

---

## ⚙️ Variáveis de ambiente

| Variável | Auto | Descrição |
|---|---|---|
| `DATABASE_URL` | ✅ Railway injeta automaticamente | URL do PostgreSQL |
| `JWT_SECRET` | ❌ Tens de criar | Chave para tokens de autenticação |
| `NODE_VERSION` | ❌ Opcional | Versão do Node.js (18) |

---

## 🧠 Dicas

- **Logs:** Vê os logs em tempo real no separador **"Deployments"** → clica no deploy atual
- **Domínio personalizado:** Em **Settings → Domains** podes adicionar o teu próprio domínio
- **Sleep (grátis):** O Railway desliga o serviço após períodos sem uso no plano grátis. Basta acederes ao URL para o reativar.
- **Base de dados:** Os dados persistem mesmo quando o serviço dorme (o PostgreSQL fica ativo)

---

## Solução de problemas

### "Build failed — Cannot find module"
Certifica-te que em **Settings** tens `Root Directory` definido como `backend`.

### "ECONNREFUSED database"
O PostgreSQL pode ainda estar a iniciar. Aguarda 2 min e vai a **"Deploy"** → "Redeploy".

### "Seed não correu"
No **Shell** do Railway, certifica-te que estás dentro da pasta `backend`:
```bash
cd backend && npx tsx src/seed.ts
```

### Quero reiniciar a base de dados (reset)
No Railway, remove o plugin PostgreSQL e adiciona de novo. Depois corre o seed novamente.

---

## 🌐 Opção 2: Hostinger VPS (se já tiveres alojamento)

Hostinger partilhado (plano mais barato) **não funciona** — não tem Node.js nem PostgreSQL.

Precisas de um **VPS** (KVM VPS a partir de ~5€/mês) ou **Cloud Hosting**.

### 1. Aceder ao servidor

No hPanel da Hostinger, vai a **"VPS" → "Manage"** e clica **"SSH Access"**. Copia o IP, utilizador e password.

No teu computador, abre o terminal (PowerShell) e:

```bash
ssh root@IP_DO_TEU_VPS
```

(Cola o IP da Hostinger. Se pedir password, usa a que está no hPanel.)

### 2. Instalar o necessário

Dentro do servidor (já SSH), executa uma linha de cada vez:

```bash
# Atualizar pacotes
apt update && apt upgrade -y

# Instalar Node.js 18
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs

# Instalar PostgreSQL
apt install -y postgresql postgresql-contrib

# Instalar Git
apt install -y git

# Instalar PM2 (para manter o servidor sempre a correr)
npm install -g pm2

# Verificar versões
node --version
npm --version
psql --version
```

### 3. Configurar PostgreSQL

```bash
# Iniciar PostgreSQL
systemctl start postgresql
systemctl enable postgresql

# Criar base de dados
sudo -u postgres psql -c "CREATE USER app_user WITH PASSWORD 'postgres123';"
sudo -u postgres psql -c "CREATE DATABASE agenda_pro OWNER app_user;"
sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE agenda_pro TO app_user;"
```

### 4. Clonar o projeto e instalar

```bash
cd /var/www
git clone https://github.com/crestionapps/agenda-pro-saas.git
cd agenda-pro-saas/backend

# Criar .env com a base de dados
cat > .env << 'EOF'
DATABASE_URL="postgresql://app_user:postgres123@localhost:5432/agenda_pro"
JWT_SECRET="agendapro_secret_hostinger_2026"
PORT=3001
EOF

# Instalar dependências e criar tabelas
npm install
npx prisma generate
npx prisma db push

# Inserir dados de exemplo
npx tsx src/seed.ts

# Compilar o TypeScript
npm run build
```

### 5. Instalar frontend

```bash
cd /var/www/agenda-pro-saas/frontend
npm install
npm run build
```

### 6. Iniciar com PM2 (fica sempre a correr)

```bash
# Iniciar o servidor
cd /var/www/agenda-pro-saas/backend
pm2 start dist/server.js --name agenda-pro

# Guardar a lista para iniciar automaticamente ao reiniciar
pm2 save
pm2 startup
```

### 7. (Opcional) Configurar Nginx + domínio

Se quiseres aceder pelo teu domínio (ex: `agendapro.pt`) em vez de IP:3001:

```bash
apt install -y nginx
```

Depois cria o ficheiro de configuração:

```bash
cat > /etc/nginx/sites-available/agendapro << 'EOF'
server {
    listen 80;
    server_name TEU-DOMINIO.pt;

    location / {
        proxy_pass http://localhost:3001;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
    }
}
EOF

# Ativar o site
ln -s /etc/nginx/sites-available/agendapro /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl restart nginx

# Se tiveres domínio, instala SSL gratuito
apt install -y certbot python3-certbot-nginx
certbot --nginx -d TEU-DOMINIO.pt
```

### 8. Aceder

- Sem domínio: `http://IP_DO_SERVIDOR:3001`
- Com domínio: `http://TEU-DOMINIO.pt` (ou `https://` se instalaste SSL)

### Comandos úteis para manter o servidor

```bash
pm2 logs            # Ver logs em tempo real
pm2 status          # Ver se está a correr
pm2 restart agenda-pro  # Reiniciar o servidor
pm2 stop agenda-pro     # Parar o servidor
```

---

## 🐳 Opção 3: Docker (qualquer VPS)

Se preferires Docker em vez de instalar tudo manualmente:

1. Instala Docker no VPS:
   ```bash
   curl -fsSL https://get.docker.com | bash
   ```

2. Clona o projeto:
   ```bash
   git clone https://github.com/crestionapps/agenda-pro-saas.git
   cd agenda-pro-saas
   ```

3. Edita o `docker-compose.yml` — muda o `JWT_SECRET` para algo seguro.

4. Inicia:
   ```bash
   docker compose up -d
   ```

5. Corre o seed:
   ```bash
   docker compose exec app npx tsx backend/src/seed.ts
   ```

6. Acede a `http://IP_DO_SERVIDOR`
