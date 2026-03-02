#!/bin/bash
# Script para subir a GitHub

echo "================================"
echo "Control Ciber - GitHub Deploy"
echo "================================"
echo ""

# Preguntar por URL del repositorio
read -p "¿Ya creaste el repositorio en GitHub? (si/no): " repo_creado

if [ "$repo_creado" != "si" ]; then
    echo ""
    echo "Por favor:"
    echo "1. Ve a https://github.com/new"
    echo "2. Crea nuevo repositorio: 'backend_control'"
    echo "3. NO inicialices con README (ya tenemos archivos)"
    echo "4. Copia la URL HTTPS del repositorio"
    echo ""
    read -p "Presiona Enter cuando hayas creado el repo..."
fi

read -p "Ingresa la URL del repositorio (ej: https://github.com/tu-usuario/backend_control.git): " repo_url

# Agregar remoto y empujar
git remote add origin "$repo_url"
git branch -M main
git push -u origin main

echo ""
echo "✓ ¡Repositorio subido a GitHub exitosamente!"
echo ""
echo "URL del repositorio: $repo_url"
echo ""
