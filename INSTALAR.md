# Guia de Instalação - AgendaPro (cPanel + MySQL)

## Requisitos
- PHP 8.1+
- MySQL 5.7+
- Composer
- Extensões PHP: BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

---

## Passo 1 - Criar Base de Dados no cPanel

1. No cPanel, abre **"MySQL Databases"** (ou "MySQL Database Wizard")
2. Cria uma base de dados com nome: `agenda_pro`
3. Cria um utilizador: `agenda_pro_user`
4. Define uma password segura e **guarda-a**
5. Adiciona o utilizador à base de dados com **TODOS OS PRIVILÉGIOS**

---

## Passo 2 - Fazer Upload dos Ficheiros

### Opção A (recomendada) - Subdomínio
1. No cPanel, abre **"Subdomains"**
2. Cria um subdomínio: `agenda.seudominio.com`
3. Define a pasta raiz (document root) como: `public_html/agendapro/public`
4. Faz download do ZIP: https://github.com/crestionapps/agenda-pro-saas/archive/refs/heads/master.zip
5. Extrai e envia por FTP a pasta `agenda-pro-saas-master` para `public_html/agendapro/`
6. O site fica disponível em: `https://agenda.seudominio.com`

### Opção B - Pasta normal
1. Faz download do ZIP e extrai
2. Envia tudo por FTP para: `public_html/agendapro/`
3. O site fica em: `https://seudominio.com/agendapro/public`

---

## Passo 3 - Instalar Dependências (via SSH/Terminal)

No cPanel, abre **"Terminal"** ou **"SSH Access"** e corre:

```bash
cd public_html/agendapro
composer install --no-dev
```

Se não tiveres acesso SSH, faz o `composer install` num computador com PHP e envia também a pasta `vendor/` por FTP.

---

## Passo 4 - Configurar o .env

1. Dentro da pasta `agendapro`, **copia** o ficheiro `.env.example` para `.env`
2. **Edita** o `.env` com os dados da base de dados que criaste no passo 1:

```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=agenda_pro
DB_USERNAME=agenda_pro_user
DB_PASSWORD=substituir-por-password-segura
```

3. Altera também o `APP_URL` para o teu domínio:
```
APP_URL=https://agenda.seudominio.com
```

---

## Passo 5 - Configuração Final (via SSH/Terminal)

```bash
cd public_html/agendapro
php artisan key:generate
php artisan migrate
mysql -u agenda_pro_user -p agenda_pro < database/seeds/agenda_pro_seed.sql
```

**Nota**: Se não tiveres SSH, o `.env.example` já tem uma APP_KEY pré-gerada.
Nesse caso, importa o seed pelo phpMyAdmin:
1. Abre o **phpMyAdmin** no cPanel
2. Seleciona a base de dados `agenda_pro`
3. Vai ao separador **"SQL"**
4. Carrega o ficheiro `database/seeds/agenda_pro_seed.sql`
5. Clica em **"Executar"**

---

## Credenciais de Teste

| Papel | Email | Password |
|---|---|---|
| Super Admin | admin@plataforma.pt | 123456 |
| Cliente | cliente@teste.pt | 123456 |
| Gerente | gerente@barbearia-classica.pt | 123456 |
| Admin Negócio | admin@barbearia-classica.pt | 123456 |

---

## Estrutura de Pastas

```
/home/user/
├── public_html/
│   └── agendapro/          ← projeto completo
│       ├── public/          ← pasta pública (document root do subdomínio)
│       ├── app/
│       ├── config/
│       ├── database/
│       ├── resources/
│       ├── routes/
│       └── vendor/
```

---

## Problemas Comuns

**"500 Internal Server Error"**
- Verifica se o PHP é 8.1+
- Verifica se a pasta `vendor/` existe
- Verifica as permissões: `chmod -R 755 storage bootstrap/cache`

**"No such file or directory"**
- Verifica se o `.env` existe e tem os dados corretos
- Verifica se a APP_KEY foi gerada

**"Connection refused" no MySQL**
- Em muitos alojamentos partilhados, o host não é `localhost`
- Verifica no cPanel → "MySQL Databases" qual o hostname correto (ex: `mysql.seudominio.com`)
