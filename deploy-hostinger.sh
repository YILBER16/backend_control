#!/bin/bash
# Script de despliegue en Hostinger
# Uso: ssh user@host 'bash deploy-hostinger.sh'

set -e

echo "================================"
echo "Control Ciber - Deploy Hostinger"
echo "================================"
echo ""

# Variables
GITHUB_REPO="$1"
DEPLOY_DIR="$HOME/public_html/backend_control"

if [ -z "$GITHUB_REPO" ]; then
    echo "ERROR: Debes pasar la URL del repositorio como argumento"
    echo "Uso: bash deploy-hostinger.sh https://github.com/usuario/backend_control.git"
    exit 1
fi

echo "[1/5] Eliminando carpeta anterior..."
rm -rf "$DEPLOY_DIR"
mkdir -p "$DEPLOY_DIR"

echo "[2/5] Clonando desde GitHub..."
git clone "$GITHUB_REPO" "$DEPLOY_DIR"
cd "$DEPLOY_DIR"

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
echo "================================"
echo "✓ ¡Despliegue completado!"
echo "================================"
echo ""
echo "API disponible en:"
echo "https://salas.edusiga.com/backend_control/api/health"
echo ""
