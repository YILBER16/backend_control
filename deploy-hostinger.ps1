# Script PowerShell para desplegar en Hostinger
# Uso: powershell -ExecutionPolicy Bypass -File deploy-hostinger.ps1

Write-Host ""
Write-Host "╔════════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║    Control Ciber - Deploy en Hostinger                ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════════════════════╝" -ForegroundColor Green
Write-Host ""

# Configuración
$sshKey = "C:\Users\$env:USERNAME\.ssh\id_rsa"
$sshHost = "u686165552@62.72.62.227"
$sshPort = "65002"
$repoUrl = Read-Host "URL del repositorio GitHub (ej: https://github.com/usuario/backend_control.git)"
$publicHtmlPath = "~/public_html"

if ([string]::IsNullOrEmpty($repoUrl)) {
    Write-Host "❌ URL vacía. Abortando..." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Desplegando en Hostinger..." -ForegroundColor Yellow
Write-Host "  SSH Host: $sshHost" -ForegroundColor DarkGray
Write-Host "  Repositorio: $repoUrl" -ForegroundColor DarkGray
Write-Host ""

# Script de despliegue
$deployScript = @"
set -e

echo "[1/5] Eliminando carpeta anterior..."
rm -rf $publicHtmlPath/backend_control

echo "[2/5] Clonando desde GitHub..."
cd $publicHtmlPath
git clone $repoUrl backend_control
cd backend_control

echo "[3/5] Instalando dependencias..."
composer install --no-dev --optimize-autoloader

echo "[4/5] Configurando .env..."
cp .env.example .env
php artisan key:generate

echo "[5/5] Ejecutando migraciones..."
touch database/database.sqlite
php artisan migrate --force
php artisan config:clear
php artisan cache:clear

echo ""
echo "✓ Despliegue completado"
echo "API: https://salas.edusiga.com/backend_control/api/health"
"@

# Ejecutar SSH con script
Write-Host "Conectando a servidor..." -ForegroundColor Yellow
$deployScript | ssh -i "$sshKey" -p $sshPort $sshHost

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "╔════════════════════════════════════════════════════════╗" -ForegroundColor Green
    Write-Host "║  ✓ ¡DESPLIEGUE COMPLETADO EN HOSTINGER!             ║" -ForegroundColor Green
    Write-Host "╚════════════════════════════════════════════════════════╝" -ForegroundColor Green
    Write-Host ""
    Write-Host "API disponible en:" -ForegroundColor Cyan
    Write-Host "https://salas.edusiga.com/backend_control/api/" -ForegroundColor Green
    Write-Host ""
    Write-Host "Endpoints:" -ForegroundColor Cyan
    Write-Host "  Health: https://salas.edusiga.com/backend_control/api/health" -ForegroundColor White
    Write-Host "  Agentes: https://salas.edusiga.com/backend_control/api/agentes" -ForegroundColor White
    Write-Host ""
} else {
    Write-Host ""
    Write-Host "❌ Error en el despliegue" -ForegroundColor Red
    Write-Host "Verifica:" -ForegroundColor Yellow
    Write-Host "  - Credenciales SSH correctas" -ForegroundColor White
    Write-Host "  - URL del repositorio válida" -ForegroundColor White
    Write-Host "  - Conexión a Hostinger disponible" -ForegroundColor White
}

Write-Host ""
