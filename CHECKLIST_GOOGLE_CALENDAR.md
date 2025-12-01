# ✅ Lista de Verificación - Integración Google Calendar

## 🔍 Checklist de Configuración

### 1. Variables de Entorno (.env)
```bash
# Verificar que existan estas variables:
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URL=http://localhost:8000/auth/google/callback
```

**✓ Verificar:**
```bash
php artisan tinker
>>> config('services.google.client_id')
>>> config('services.google.client_secret')
```

---

### 2. Google Cloud Console

**✓ Google Calendar API habilitada**
- [ ] Ve a https://console.cloud.google.com/apis/library
- [ ] Busca "Google Calendar API"
- [ ] Verifica que esté ENABLED

**✓ OAuth Consent Screen configurado**
- [ ] Ve a APIs & Services > OAuth consent screen
- [ ] Scope agregado: `https://www.googleapis.com/auth/calendar`

**✓ Redirect URIs autorizadas**
- [ ] Ve a APIs & Services > Credentials
- [ ] Edita tu OAuth 2.0 Client
- [ ] Verifica estas URIs:
  ```
  http://localhost:8000/auth/google/callback
  http://localhost:8000/auth/google-calendar/callback
  ```

---

### 3. Base de Datos

**✓ Columnas en tabla users:**
```sql
SELECT 
    COLUMN_NAME, 
    DATA_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'users' 
AND COLUMN_NAME LIKE '%google_calendar%';
```

Deberías ver:
```
google_calendar_token              VARCHAR(255)
google_calendar_refresh_token      TEXT
google_calendar_token_expires_at   TIMESTAMP
google_calendar_synced             TINYINT(1)
```

---

### 4. Rutas Laravel

**✓ Verificar rutas registradas:**
```bash
php artisan route:list --path=auth/google
```

Deberías ver:
```
✓ GET auth/google
✓ GET auth/google-calendar
✓ GET auth/google-calendar/callback
✓ GET auth/google/callback
```

**✓ Verificar ruta de estado:**
```bash
php artisan route:list --name=google.calendar
```

Debería aparecer:
```
✓ GET google-calendar/status
```

---

### 5. Archivos Modificados

**✓ Modelo User.php**
```bash
grep -n "google_calendar_token" app/Models/User.php
```
Debería mostrar que está en el array `$fillable`

**✓ GoogleController.php**
```bash
grep -n "redirectToGoogleCalendar" app/Http/Controllers/Auth/GoogleController.php
grep -n "handleGoogleCalendarCallback" app/Http/Controllers/Auth/GoogleController.php
grep -n "syncTasksToGoogleCalendar" app/Http/Controllers/Auth/GoogleController.php
```

**✓ TaskController.php**
```bash
grep -n "checkGoogleCalendarStatus" app/Http/Controllers/TaskController.php
```

**✓ JavaScript agenda.js**
```bash
grep -n "verificarEstadoGoogleCalendar" public/js/components/home-user/tasks/agenda.js
```

---

### 6. Limpiar Cachés

```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

---

## 🧪 Pruebas Funcionales

### Prueba 1: Verificar Endpoint de Estado
```bash
# Iniciar servidor
php artisan serve

# En otra terminal:
curl http://localhost:8000/google-calendar/status
```

Debería retornar JSON:
```json
{
  "connected": false,
  "synced": false,
  "expires_at": null
}
```

### Prueba 2: Verificar Redirect a Google
1. Ve a http://localhost:8000/tareas
2. Carga la vista de agenda
3. Abre DevTools > Console
4. Deberías ver: `Verificando estado de Google Calendar...`

### Prueba 3: Conectar Google Calendar
1. Haz clic en "Conectar con Google Calendar"
2. Confirma en el popup
3. Deberías ser redirigido a Google OAuth
4. Autoriza los permisos
5. Deberías volver a `/tareas` con mensaje de éxito

### Prueba 4: Verificar Sincronización
1. Ve a https://calendar.google.com
2. Deberías ver tus tareas como eventos
3. Con colores según prioridad

---

## 🐛 Solución de Problemas

### Error: "Client is unauthorized to retrieve access tokens"
**Solución:**
- Ve a Google Cloud Console > OAuth consent screen
- Cambia de Testing a Production O agrega tu email a Test users

### Error: "redirect_uri_mismatch"
**Solución:**
```bash
# Verifica tu GOOGLE_REDIRECT_URL en .env
cat .env | grep GOOGLE_REDIRECT

# Debe coincidir exactamente con la URI en Google Console
# Incluye el protocolo (http/https) y puerto si es local
```

### Error: "Token has been expired or revoked"
**Solución:**
- El sistema debería renovar automáticamente
- Si persiste, reconecta: haz clic nuevamente en el botón

### Error 404 en /auth/google-calendar/callback
**Solución:**
```bash
php artisan route:clear
php artisan config:clear
php artisan serve
```

### Tareas no aparecen en Calendar
**Solución:**
1. Verifica logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. Verifica que tengas tareas no archivadas:
   ```sql
   SELECT COUNT(*) FROM sub_tareas 
   WHERE user_id = TU_USER_ID 
   AND archivada = 0;
   ```

3. Verifica en la tabla users:
   ```sql
   SELECT 
       google_calendar_synced,
       google_calendar_token IS NOT NULL as has_token
   FROM users 
   WHERE id = TU_USER_ID;
   ```

---

## 📊 Comandos de Depuración

### Ver logs en tiempo real
```bash
tail -f storage/logs/laravel.log | grep -i "google\|calendar"
```

### Verificar usuario conectado
```bash
php artisan tinker
>>> $user = \App\Models\User::find(TU_USER_ID);
>>> $user->google_calendar_token;
>>> $user->google_calendar_synced;
>>> $user->google_calendar_token_expires_at;
```

### Verificar cantidad de tareas
```bash
php artisan tinker
>>> $user = \App\Models\User::find(TU_USER_ID);
>>> $tareas = \App\Models\SubTarea::where('user_id', $user->id)->where('archivada', false)->get();
>>> $tareas->count();
>>> $tareas->pluck('nombre');
```

### Probar Google Client manualmente
```bash
php artisan tinker
>>> $client = new \Google_Client();
>>> $client->setClientId(config('services.google.client_id'));
>>> $client->setClientSecret(config('services.google.client_secret'));
>>> echo "Configuración OK";
```

---

## ✅ Checklist Final

- [ ] Variables de entorno configuradas
- [ ] Google Calendar API habilitada
- [ ] OAuth Consent Screen con scope calendar
- [ ] Redirect URIs autorizadas
- [ ] Columnas en BD verificadas
- [ ] Rutas Laravel registradas
- [ ] Archivos modificados confirmados
- [ ] Cachés limpiados
- [ ] Prueba de conexión exitosa
- [ ] Tareas visibles en Google Calendar
- [ ] Botón cambia a "Conectado"
- [ ] Sin errores en logs

---

## 🎉 Si Todo Está Verde

¡Felicitaciones! La integración está completa y funcionando.

Ahora tus usuarios pueden:
✅ Conectar su Google Calendar con un clic
✅ Ver todas sus tareas sincronizadas automáticamente
✅ Identificar prioridades por colores
✅ Reconectar cuando lo deseen

---

## 📝 Notas Importantes

1. **Primera conexión**: Google pedirá permisos explícitos
2. **Tokens**: Se renuevan automáticamente cada hora
3. **Re-sincronización**: Solo crea nuevos eventos (no duplica)
4. **Zona horaria**: America/Santiago por defecto
5. **Calendario**: Usa el principal ('primary')
6. **Tareas archivadas**: NO se sincronizan

---

## 📚 Archivos de Referencia

- `GOOGLE_CALENDAR_SETUP.md` - Guía completa de configuración
- `RESUMEN_GOOGLE_CALENDAR.md` - Resumen de implementación
- `DIAGRAMA_FLUJO_GOOGLE_CALENDAR.txt` - Diagrama visual del flujo

---

¿Problemas? Revisa los logs y este checklist paso a paso. 🔍
