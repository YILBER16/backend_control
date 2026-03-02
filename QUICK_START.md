# ⚡ Quick Start - 5 Minutos

## Requisitos

- Node.js 14+ instalado ([Descargar](https://nodejs.org))
- Windows/Mac/Linux
- Editor de texto (VS Code recomendado)

## Opción 1: Script Automático ⭐ (RECOMENDADO)

### En Windows PowerShell (Admin):

```powershell
cd e:\control_ciber
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
.\start-all.ps1
```

**Espera 30s** y automáticamente se abren 4 terminales.

---

## Opción 2: Manual (Si el script no funciona)

### Terminal 1: API Backend
```bash
cd e:\control_ciber\api-backend
npm install
npm start
```

Espera a ver: `🚀 API Iniciado en puerto 3000`

### Terminal 2: Servidor Local (Gestiona Sala 1 y 2)
```bash
cd e:\control_ciber\servidor-local
npm install
npm start
```

Espera a ver: `🌐 Servidor Local escuchando en puerto 5000`

Abre en navegador: `http://localhost:5000` (interfaz visual para seleccionar sala)

### Terminal 3: Agente Esclavo (Prueba)
```bash
cd e:\control_ciber\agente-esclavo
npm install
npm start
```

Espera a ver: `✓ Conectado a API`

### Terminal 4: Dashboard
```bash
cd e:\control_ciber\dashboard-frontend
npm install
npm start
```

Debería abrir automáticamente en `http://localhost:3000`

---

## 🎮 Usa el Sistema

### 1. Abre las 2 interfaces

**Interfaz Servidor Local** (para monitoreo):
```
http://localhost:5000
- Botones para seleccionar Sala 1 o Sala 2
- Ve dispositivos conectados en tiempo real
- Muestra estadísticas (online/offline)
```

**Dashboard** (para controlar):
```
http://localhost:3000
- Login: admin / admin123
- Selecciona Sala 1 o 2
- Ejecuta comandos
```

### 2. Flujo de Uso

1. Abre `http://localhost:5000` en una ventana
   - Click en **"Sala 1"** o **"Sala 2"**
   - Monitorea qué está conectado

2. Abre `http://localhost:3000` en otra ventana
   - Login con `admin / admin123`
   - Selecciona misma sala
   - Ejecuta comandos (Apagar, Reiniciar, Lock, etc)

### 3. Prueba un Comando

En Dashboard:
1. Login: `admin / admin123`
2. Selecciona **"Sala 1"**
3. En Terminal 3 (Agente), verás un dispositivo conectado
4. En Dashboard, selecciona ese dispositivo (checkbox)
5. Click en **"🔒 Bloquear"**
6. En terminal del agente verás:
   ```
   ⚙️  EJECUTANDO: lock
   ✓ lock - OK
   ```

---

## 🧐 ¿Qué ves en cada ventana?

### Servidor Local (http://localhost:5000)
```
Interfaz Visual:
┌─────────────────────────────────┐
│  Servidor Local - Control       │
│  [Sala 1] [Sala 2]              │
│  Sala 1                         │
│  3 dispositivos registrados     │
│  ☑ PC_001  ● Activo            │
│  ☑ PC_002  ● Activo            │
│  ☑ PC_003  ○ Inactivo          │
│  En línea: 2                    │
│  Desconectados: 1              │
│  Total: 3                       │
└─────────────────────────────────┘
```

### Dashboard (http://localhost:3000)
```
Login → Sala 1 [Sala 2]
┌─────────────────────────────────┐
│ [Apagar] [Reiniciar] [Lock]     │
│ [Limpiar]                       │
├─────────────────────────────────┤
│ PC_001 [En Línea] ☐             │
│ PC_002 [En Línea] ☐             │
│ PC_003 [Offline]  ☐             │
└─────────────────────────────────┘
```

---

## 📝 Diferencia: Servidor Local vs Dashboard

| Aspecto | Servidor Local (5000) | Dashboard (3000) |
|--------|----------------------|-----------------|
| **Propósito** | Monitoreo local | Control remoto |
| **Interfaz** | Visual simple | Completa con login |
| **Funciones** | Ver estado | Ejecutar comandos |
| **Actualizantes** | Cada 3s | En tiempo real |
| **Dónde está** | Tu PC (red local) | Accesible internet |

---

## 📝 Próximos Pasos

1. **Lee:** `RESUMEN.md` (visión general)
2. **Detalle:** `ARQUITECTURA.md` (cómo funciona)
3. **Instala:** `SETUP_GUIDE.md` (producción)
4. **Estudia:** `GUIA_ESTUDIANTES.md` (proyectos)

---

## 🚨 Problemas Comunes

| Error | Solución |
|-------|----------|
| `Cannot find module 'express'` | Ejecutaste sin `npm install` |
| `EADDRINUSE :::5000` | Puerto 5000 ocupado. Mata: `netstat -ano \| findstr :5000` |
| `Connection refused` | API no está corriendo. Revisa terminal 1 |
| Servidor Local no carga | Espera 3s más, API tarda en sincronizar |

---

## 🎓 Concepto en 30 Segundos

```
TÚ (Profesor)
  ↓ Abre http://localhost:5000 (monitoreo)
  ↓ Ves qué está conectado
  ↓ Abre http://localhost:3000 (control)
  ↓ Seleccionas Sala 1
  ↓ Seleccionas 5 PCs
  ↓ Click "Apagar"
  ↓
API → Servidor Local → 5 PCs Estudiantes se apagan ✓
```

---

## 🏁 ¿Listo?

```
✅ Node.js instalado
✅ Carpeta e:\control_ciber existe
✅ 4 componentes corriendo
✅ Servidor Local accesible (http://5000)
✅ Dashboard accesible (http://3000)
✅ Puedes ejecutar comandos

¡FELICIDADES! El sistema está VIVO 🎉
```

### Próxima meta: 
👉 Ahora lee `ARQUITECTURA.md` para entender QUÉ está pasando atrás de escenas.

---

⏱️ **Total:** 5 minutos  
📊 **Complejidad:** Muy Fácil  
✨ **Satisfacción:** ¡Muchísima!

**¡Adelante!**
