# Setup AgendaPro - Script automático para Windows
Write-Host "=======================================" -ForegroundColor Cyan
Write-Host "   AgendaPro - Setup Automático" -ForegroundColor Cyan
Write-Host "=======================================" -ForegroundColor Cyan
Write-Host ""

# Verificar Node.js
try {
    $nodeVersion = node --version
    Write-Host "[OK] Node.js $nodeVersion" -ForegroundColor Green
} catch {
    Write-Host "[ERRO] Node.js não encontrado!" -ForegroundColor Red
    Write-Host "Descarrega em: https://nodejs.org (versão LTS)" -ForegroundColor Yellow
    exit 1
}

# Verificar npm
try {
    $npmVersion = npm --version
    Write-Host "[OK] npm $npmVersion" -ForegroundColor Green
} catch {
    Write-Host "[ERRO] npm não encontrado!" -ForegroundColor Red
    exit 1
}

# Verificar PostgreSQL
try {
    $psqlVersion = & "psql" --version
    Write-Host "[OK] $psqlVersion" -ForegroundColor Green
} catch {
    Write-Host "[AVISO] PostgreSQL CLI não encontrada no PATH" -ForegroundColor Yellow
    Write-Host "Certifica-te que o PostgreSQL está instalado" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "A instalar dependências do backend..." -ForegroundColor Yellow
Set-Location -Path "$PSScriptRoot\backend"
npm install
if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERRO] Falha ao instalar dependências do backend" -ForegroundColor Red
    exit 1
}
Write-Host "[OK] Dependências do backend instaladas" -ForegroundColor Green

Write-Host ""
Write-Host "A gerar cliente Prisma..." -ForegroundColor Yellow
npx prisma generate
if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERRO] Falha ao gerar Prisma client" -ForegroundColor Red
    exit 1
}
Write-Host "[OK] Prisma client gerado" -ForegroundColor Green

Write-Host ""
Write-Host "A criar tabelas na base de dados..." -ForegroundColor Yellow
npx prisma db push
if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERRO] Falha ao criar tabelas. Verifica o PostgreSQL." -ForegroundColor Red
    Write-Host "1. O PostgreSQL está a correr?" -ForegroundColor Yellow
    Write-Host "2. A base de dados 'agendamento_saas' existe?" -ForegroundColor Yellow
    Write-Host "3. O .env tem a password correta?" -ForegroundColor Yellow
    exit 1
}
Write-Host "[OK] Tabelas criadas" -ForegroundColor Green

Write-Host ""
Write-Host "A inserir dados de exemplo (seed)..." -ForegroundColor Yellow
npx tsx src/seed.ts
if ($LASTEXITCODE -ne 0) {
    Write-Host "[AVISO] Seed pode já ter sido corrido. A continuar..." -ForegroundColor Yellow
}
Write-Host "[OK] Dados de exemplo inseridos" -ForegroundColor Green

Write-Host ""
Write-Host "A instalar dependências do frontend..." -ForegroundColor Yellow
Set-Location -Path "$PSScriptRoot\frontend"
npm install
if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERRO] Falha ao instalar dependências do frontend" -ForegroundColor Red
    exit 1
}
Write-Host "[OK] Dependências do frontend instaladas" -ForegroundColor Green

Write-Host ""
Write-Host "=======================================" -ForegroundColor Green
Write-Host "   Setup completo!" -ForegroundColor Green
Write-Host "=======================================" -ForegroundColor Green
Write-Host ""
Write-Host "Para iniciar, abre DOIS terminais:" -ForegroundColor Cyan
Write-Host ""
Write-Host "Terminal 1 (Backend):" -ForegroundColor White
Write-Host "  cd agendamento-saas/backend" -ForegroundColor Gray
Write-Host "  npm run dev" -ForegroundColor Gray
Write-Host ""
Write-Host "Terminal 2 (Frontend):" -ForegroundColor White
Write-Host "  cd agendamento-saas/frontend" -ForegroundColor Gray
Write-Host "  npm run dev" -ForegroundColor Gray
Write-Host ""
Write-Host "Depois abre http://localhost:5173 no browser" -ForegroundColor Cyan
Write-Host ""
Write-Host "Credenciais de teste:" -ForegroundColor Yellow
Write-Host "  Admin: admin@plataforma.pt / 123456" -ForegroundColor Gray
Write-Host "  Cliente: cliente@teste.pt / 123456" -ForegroundColor Gray
Write-Host "  Gerente: gerente@barbearia-classica.pt / 123456" -ForegroundColor Gray
Write-Host ""

Read-Host "Prima Enter para fechar"
