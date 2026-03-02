# Script PowerShell para subir el proyecto a GitHub
# Uso: powershell -ExecutionPolicy Bypass -File deploy-github.ps1

Write-Host ""
Write-Host "╔════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║      Control Ciber - Deploy a GitHub                  ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

# Verificar que estamos en la carpeta correcta
if (-not (Test-Path "app") -or -not (Test-Path "composer.json")) {
    Write-Host "❌ ERROR: Debes ejecutar este script desde la carpeta backend_control/" -ForegroundColor Red
    exit 1
}

# Verificar Git
if (-not (Get-Command git -ErrorAction SilentlyContinue)) {
    Write-Host "❌ Git no está instalado" -ForegroundColor Red
    exit 1
}

Write-Host "✓ Git detectado" -ForegroundColor Green
Write-Host ""

# Preguntar si ya tiene repo en GitHub
Write-Host "¿Tiene un repositorio en GitHub para este proyecto?" -ForegroundColor Yellow
Write-Host "[1] Sí, dame la URL" -ForegroundColor White
Write-Host "[2] No, ayúdame a crearlo" -ForegroundColor White
Write-Host ""
$choice = Read-Host "Selecciona (1 o 2)"

if ($choice -eq "2") {
    Write-Host ""
    Write-Host "╔════════════════════════════════════════════════════════╗" -ForegroundColor Yellow
    Write-Host "║  PASOS PARA CREAR REPOSITORIO EN GITHUB              ║" -ForegroundColor Yellow
    Write-Host "╚════════════════════════════════════════════════════════╝" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "1. Ve a: https://github.com/new" -ForegroundColor Cyan
    Write-Host "2. Completa con:" -ForegroundColor Cyan
    Write-Host "   Repository name: backend_control" -ForegroundColor White
    Write-Host "   Description: Control remoto de PCs mediante HTTP REST" -ForegroundColor White
    Write-Host "   Visibility: Public (o Private)" -ForegroundColor White
    Write-Host "   ✗ NO CHECK: Add a README file" -ForegroundColor Red
    Write-Host "   ✗ NO CHECK: Add gitignore" -ForegroundColor Red
    Write-Host "3. Click 'Create repository'" -ForegroundColor Cyan
    Write-Host "4. Copia la URL HTTPS del repositorio" -ForegroundColor Cyan
    Write-Host ""
    Read-Host "Presiona Enter cuando hayas creado el repositorio"
    Write-Host ""
}

# Obtener URL del repositorio
Write-Host "Ingresa la URL HTTPS del repositorio en GitHub:" -ForegroundColor Yellow
Write-Host "(ej: https://github.com/tu-usuario/backend_control.git)" -ForegroundColor DarkGray
$repoUrl = Read-Host "URL"

if ([string]::IsNullOrEmpty($repoUrl)) {
    Write-Host "❌ URL vacía. Abortando..." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Conectando con repositorio..." -ForegroundColor Yellow

# Agregar remoto
git remote add origin "$repoUrl" 2>$null
if ($LASTEXITCODE -ne 0) {
    Write-Host "Remoto ya existe, actualizando..." -ForegroundColor DarkYellow
    git remote set-url origin "$repoUrl"
}

# Cambiar rama a main
git branch -M main 2>$null

# Hacer push
Write-Host "Subiendo código..." -ForegroundColor Yellow
git push -u origin main

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "╔════════════════════════════════════════════════════════╗" -ForegroundColor Green
    Write-Host "║  ✓ ¡CÓDIGO SUBIDO A GITHUB EXITOSAMENTE!            ║" -ForegroundColor Green
    Write-Host "╚════════════════════════════════════════════════════════╝" -ForegroundColor Green
    Write-Host ""
    Write-Host "Repositorio: $repoUrl" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Próximo paso: Desplegar en Hostinger" -ForegroundColor Yellow
    Write-Host "Ejecuta: powershell deploy-hostinger.ps1" -ForegroundColor White
} else {
    Write-Host ""
    Write-Host "❌ Error al subir. Verifica:" -ForegroundColor Red
    Write-Host "  - La URL del repositorio es correcta" -ForegroundColor White
    Write-Host "  - Tienes credenciales de Git configuradas" -ForegroundColor White
    Write-Host ""
    Write-Host "Para configurar credenciales:" -ForegroundColor Yellow
    Write-Host "  git config --global user.name 'Tu Nombre'" -ForegroundColor DarkGray
    Write-Host "  git config --global user.email 'tu@email.com'" -ForegroundColor DarkGray
}

Write-Host ""
