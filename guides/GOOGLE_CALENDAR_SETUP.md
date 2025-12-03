# 📅 Configuración de Google Calendar para BMaia

## ✅ Archivos Modificados

Los siguientes archivos han sido actualizados para soportar la sincronización con Google Calendar:

1. **app/Models/User.php** - Agregados campos de Google Calendar al fillable
2. **app/Http/Controllers/Auth/GoogleController.php** - Métodos de autenticación y sincronización
3. **app/Http/Controllers/TaskController.php** - Método para verificar estado de conexión
4. **routes/web.php** - Rutas para autorización y callback
5. **public/js/components/home-user/tasks/agenda.js** - Conexión del botón con backend

---

## 🔧 Configuración Necesaria

### 1. Variables de Entorno (.env)

Ya tienes configurado en `config/services.php`:
```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URL'),
],
```

Asegúrate de tener en tu `.env`:
```env
GOOGLE_CLIENT_ID=tu_client_id
GOOGLE_CLIENT_SECRET=tu_client_secret
GOOGLE_REDIRECT_URL=http://localhost:8000/auth/google/callback
```

### 2. Activar Google Calendar API

Ve a [Google Cloud Console](https://console.cloud.google.com/):

1. Selecciona tu proyecto existente (o crea uno nuevo)
2. Ve a **APIs & Services** > **Library**
3. Busca **Google Calendar API**
4. Haz clic en **Enable** (Habilitar)

### 3. Configurar OAuth Consent Screen

1. Ve a **APIs & Services** > **OAuth consent screen**
2. Agrega el scope: `https://www.googleapis.com/auth/calendar`
3. Guarda los cambios

### 4. Actualizar Redirect URI

En **APIs & Services** > **Credentials** > Tu OAuth 2.0 Client:

Agrega estas URIs autorizadas:
- `http://localhost:8000/auth/google/callback` (para login)
- `http://localhost:8000/auth/google-calendar/callback` (para calendar sync)

Para producción:
- `https://tudominio.com/auth/google/callback`
- `https://tudominio.com/auth/google-calendar/callback`

---

## 🚀 Cómo Funciona

### Flujo de Sincronización:

1. **Usuario hace clic en "Conectar con Google Calendar"**
   - Se muestra popup de confirmación
   
2. **Usuario confirma**
   - Redirige a `/auth/google-calendar`
   - Google solicita permisos para acceder al calendario
   
3. **Usuario autoriza**
   - Google redirige a `/auth/google-calendar/callback`
   - Se guardan los tokens en la base de datos
   
4. **Sincronización automática**
   - Se crean eventos en Google Calendar por cada tarea
   - Las tareas se marcan con colores según prioridad:
     - 🔵 Baja → Azul lavanda
     - 🟡 Media → Amarillo  
     - 🟠 Alta → Naranja
     - 🔴 Urgente → Rojo

5. **Estado del botón**
   - Si ya está conectado, el botón cambia a verde
   - Permite reconectar para re-sincronizar

---

## 📊 Columnas en Base de Datos

Ya existen en tu tabla `users`:

```sql
google_calendar_token              VARCHAR(255)
google_calendar_refresh_token      TEXT
google_calendar_token_expires_at   TIMESTAMP
google_calendar_synced             TINYINT(1)
```

---

## 🧪 Probar la Integración

### Paso 1: Verificar configuración
```bash
php artisan config:clear
php artisan cache:clear
```

### Paso 2: Ir a la agenda
1. Inicia sesión en la aplicación
2. Ve a la vista de **Agenda de Tareas**
3. Haz clic en **Conectar con Google Calendar**
4. Autoriza los permisos
5. Verifica en tu Google Calendar que aparezcan las tareas

### Paso 3: Verificar logs
Si hay errores, revisa:
```bash
tail -f storage/logs/laravel.log
```

---

## 🔄 Re-sincronizar Tareas

Si el usuario ya está conectado:
- El botón cambia a "Conectado con Google Calendar"
- Hacer clic nuevamente permite re-sincronizar
- Esto crea nuevos eventos (no duplica, Calendar los maneja)

---

## 🐛 Solución de Problemas

### Error: "Token expired"
- El sistema automáticamente renueva tokens usando el refresh token
- Si falla, el usuario debe reconectar

### Error: "Insufficient permissions"
- Verifica que el scope `calendar` esté en OAuth Consent Screen
- Re-autoriza la aplicación

### Tareas no aparecen en Calendar
- Verifica que `fecha_inicio` y `fecha_limite` sean válidas
- Revisa los logs en `storage/logs/laravel.log`

### Error 404 en callback
- Verifica que las rutas estén correctas en `web.php`
- Limpia caché de rutas: `php artisan route:clear`

---

## 📝 Notas Importantes

1. **Tokens de acceso**: Expiran en 1 hora, pero se renuevan automáticamente
2. **Refresh tokens**: Solo se obtienen la primera vez (con `prompt=consent`)
3. **Tareas archivadas**: NO se sincronizan (solo `archivada = false`)
4. **Zona horaria**: Configurada a `America/Santiago`
5. **Calendario**: Se usa el calendario principal (`primary`)

---

## 🎨 Colores de Prioridad en Google Calendar

```
ID 1  → Azul lavanda  (baja)
ID 5  → Amarillo      (media)
ID 6  → Naranja       (alta)
ID 11 → Rojo          (urgente)
```

---

## 📚 Referencias

- [Google Calendar API Docs](https://developers.google.com/calendar/api/v3/reference)
- [Laravel Socialite](https://laravel.com/docs/11.x/socialite)
- [Google API PHP Client](https://github.com/googleapis/google-api-php-client)

---

## ✨ Próximas Mejoras (Opcionales)

- [ ] Sincronización bidireccional (Calendar → BMaia)
- [ ] Actualizar eventos cuando cambie una tarea
- [ ] Eliminar eventos cuando se archive una tarea
- [ ] Seleccionar calendario específico (no solo 'primary')
- [ ] Agregar recordatorios en Calendar
- [ ] Webhook para actualizaciones en tiempo real
