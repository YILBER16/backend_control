# 📋 GUÍA DE DESPLIEGUE - Control Ciber Backend

## ✅ ESTADO ACTUAL

El proyecto `backend_control/` está **100% listo** para:
1. ⬆️ Subir a GitHub
2. 🚀 Desplegar en Hostinger

## 🚀 INSTRUCCIONES RÁPIDAS

### OPCIÓN 1: Usando Scripts Automáticos (RECOMENDADO)

#### Paso 1: Subir a GitHub
```pwsh
cd e:\control_ciber\backend_control
powershell -ExecutionPolicy Bypass -File deploy-github.ps1
```
El script te guiará para:
- Crear repo en GitHub (si no existe)
- Ingresar la URL HTTPS
- Hacer push automáticamente

#### Paso 2: Desplegar en Hostinger
```pwsh
cd e:\control_ciber\backend_control
powershell -ExecutionPolicy Bypass -File deploy-hostinger.ps1
```
El script:
- Clona desde GitHub en Hostinger
- Instala dependencias vía Composer
- Ejecuta migraciones
- Configura .env automáticamente
- Limpia caché

### OPCIÓN 2: Manual (Paso por Paso)

#### 1. Crear repositorio en GitHub
```
1. Ve a: https://github.com/new
2. Repository name: backend_control
3. Description: API REST para control de PCs
4. Visibility: Public
5. NO inicializar con README
6. Click "Create repository"
```

#### 2. Subir código desde tu PC
```bash
cd e:\control_ciber\backend_control
git remote add origin https://github.com/TU-USUARIO/backend_control.git
git branch -M main
git push -u origin main
```

#### 3. Desplegar en Hostinger (via SSH)
```bash
ssh -i C:\Users\TU-USUARIO\.ssh\id_rsa -p 65002 u686165552@62.72.62.227 << 'EOF'
cd ~/public_html
rm -rf backend_control
git clone https://github.com/TU-USUARIO/backend_control.git
cd backend_control
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
EOF
```

## 📦 CONTENIDO DE BACKEND_CONTROL

```
backend_control/
├── app/
│   ├── Models/
│   │   ├── Agente.php          ← Modelo de agentes
│   │   └── Comando.php         ← Modelo de comandos
│   └── Http/Controllers/Api/
│       ├── AgenteController.php
│       └── ServidorController.php
├── routes/api.php              ← 7 endpoints REST
├── database/
│   ├── migrations/
│   │   ├── create_agentes_table.php
│   │   └── create_comandos_table.php
│   └── database.sqlite
├── config/                      ← Configuración Laravel
├── resources/                   ← Vistas
├── storage/                     ← Logs, caché
├── .env.example                 ← Plantilla de variables
├── .gitignore                   ← Exclusiones Git
├── composer.json                ← Dependencias PHP
├── artisan                      ← CLI de Laravel
└── deploy-*.ps1/sh             ← Scripts de despliegue
```

## 🌐 URLs DESPUÉS DEL DESPLIEGUE

| Endpoint | URL |
|----------|-----|
| Health Check | `https://salas.edusiga.com/backend_control/api/health` |
| Listar Agentes | `https://salas.edusiga.com/backend_control/api/agentes` |
| API Base | `https://salas.edusiga.com/backend_control/api/` |

## 📝 ENDPOINTS DISPONIBLES

### Agentes (Cliente HTTP)
```
POST /api/esclavo/register       - Registrar agente nuevo
POST /api/esclavo/heartbeat      - Polling c/5s para comandos
POST /api/esclavo/resultado      - Reportar resultado de comando
```

### Servidor (Monitor)
```
POST /api/servidor/enviar-comando - Enviar comando a agente
GET  /api/servidor/estado         - Ver estado de agentes en sala
```

### Info
```
GET /api/health                  - Health check
GET /api/agentes                 - Listar todos los agentes
GET /api                         - Info del API
```

## ⚙️ REQUISITOS

En Hostinger:
- ✅ PHP 8.0+ (tiene 8.2+)
- ✅ Composer
- ✅ SQLite3
- ✅ Git
- ✅ SSH access
- ✅ Mod_rewrite (Apache)

## 🔑 VARIABLES DE ENTORNO (.env)

Después del despliegue, el archivo `.env` se crea automáticamente desde `.env.example` con:

```env
APP_NAME=ControlCiber
APP_ENV=production
APP_DEBUG=false
APP_URL=https://salas.edusiga.com/backend_control

DB_CONNECTION=sqlite
DB_DATABASE=database.sqlite

CACHE_DRIVER=file
SESSION_DRIVER=database
```

## 📊 GIT COMMANDS RÁPIDO

```bash
# Ver estado
git status

# Ver commits
git log --oneline

# Ver remoto
git remote -v

# Ver ramas
git branch -a

# Cambios en la rama actual
git diff

# Revertir cambios
git checkout -- .
```

## 🐛 SOLUCIÓN DE PROBLEMAS

### Error: "Permission denied" en SSH
```bash
# Verificar permisos de clave privada
icacls "C:\Users\TU-USUARIO\.ssh\id_rsa"
# Debe tener solo propietario con acceso
```

### Error: "Repository already exists"
```bash
# Remover remoto antiguo
git remote remove origin

# Agregar remoto correcto
git remote add origin <URL-CORRECTA>
```

### Error: "Database error"
```bash
# En Hostinger: Recrear base de datos
rm database/database.sqlite
php artisan migrate --force
```

### Error: "Permission denied database"
```bash
# Permisos en Hostinger
chmod 644 database/database.sqlite
chmod 755 database/
```

## 📞 CONTACTO & SOPORTE

Crear issue en GitHub: https://github.com/tu-usuario/backend_control/issues

## ✅ CHECKLIST FINAL

- [ ] Repositorio creado en GitHub
- [ ] Código subido via push
- [ ] Desplegado en Hostinger
- [ ] API health check responde (200 OK)
- [ ] Agentes pueden registrarse
- [ ] Comandos se envían correctamente

---

**Última actualización:** 2 de marzo de 2026
**Estado:** ✅ LISTO PARA PRODUCCIÓN
