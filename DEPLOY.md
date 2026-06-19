# Guia de Deploy — Colocar o AgendaPro online com Railway

**Railway é a opção mais simples e rápida.** Não precisas de configurar nada manualmente — ele deteta o Node.js, cria PostgreSQL automaticamente e faz o deploy.

---

## 🚄 Deploy no Railway (gratuito para começar)

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

## 🐳 Alternativa: Docker / VPS

Se preferires controlo total, vê o [`docker-compose.yml`](docker-compose.yml) para correr com Docker.
