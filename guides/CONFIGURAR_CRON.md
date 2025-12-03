# Configuración del Cron para Prioridades Automáticas

Esta guía te muestra cómo configurar el cron del servidor para que el sistema de prioridades automáticas se ejecute sin intervención manual.

---

## 🐧 Servidor Linux/Unix

### Paso 1: Abrir el editor de crontab

```bash
crontab -e
```

Este comando abre el editor de tareas programadas de tu usuario actual.

**Nota**: La primera vez que ejecutes este comando, te preguntará qué editor usar. Recomendamos `nano` (más fácil) o `vim`.

---

### Paso 2: Agregar la tarea programada

Al final del archivo, agrega esta línea:

```bash
* * * * * cd /ruta/completa/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

**⚠️ IMPORTANTE**: Reemplaza `/ruta/completa/a/tu/proyecto` con la ruta real de tu proyecto.

#### Ejemplo:

```bash
* * * * * cd /var/www/bmaia_v1_practica && php artisan schedule:run >> /dev/null 2>&1
```

#### ¿Qué significa esta línea?

- `* * * * *` → Ejecutar cada minuto
- `cd /ruta/proyecto` → Ir al directorio del proyecto
- `php artisan schedule:run` → Ejecutar el scheduler de Laravel
- `>> /dev/null 2>&1` → Redirigir salida (no llenar logs del sistema)

---

### Paso 3: Guardar y salir

#### Si usas **nano**:
1. Presiona `CTRL + X`
2. Presiona `Y` (para confirmar)
3. Presiona `ENTER`

#### Si usas **vim**:
1. Presiona `ESC`
2. Escribe `:wq`
3. Presiona `ENTER`

Verás un mensaje como:
```
crontab: installing new crontab
```

---

### Paso 4: Verificar que se guardó correctamente

```bash
crontab -l
```

Este comando lista todas tus tareas programadas. Deberías ver la línea que acabas de agregar:

```bash
* * * * * cd /var/www/bmaia_v1_practica && php artisan schedule:run >> /dev/null 2>&1
```

---

## ✅ Verificación Final

### 1. Espera 1-2 minutos y verifica los logs:

```bash
tail -f /ruta/proyecto/storage/logs/laravel.log
```

Deberías ver entradas como:
```
[2025-11-16 10:15:00] local.INFO: Actualización de prioridades completada
```

### 2. Verifica que el comando se ejecutó:

```bash
php artisan schedule:list
```

Deberías ver:
```
0 * * * *  php artisan tareas:actualizar-prioridad  Next Due: 45 minutes from now
```

---

## 🐛 Solución de Problemas

### El cron no se ejecuta

**Verifica que el servicio cron esté activo:**

```bash
# Ubuntu/Debian
sudo service cron status

# CentOS/RHEL
sudo service crond status
```

Si está detenido, inícialo:

```bash
# Ubuntu/Debian
sudo service cron start

# CentOS/RHEL
sudo service crond start
```

---

### Permisos incorrectos

Asegúrate de que el usuario que ejecuta el cron tenga permisos de escritura:

```bash
# Verificar propietario del proyecto
ls -la /ruta/proyecto

# Ajustar permisos de storage y cache
cd /ruta/proyecto
chmod -R 775 storage bootstrap/cache
```

---

### Ver logs del cron del sistema

```bash
# Ubuntu/Debian
grep CRON /var/log/syslog

# CentOS/RHEL
grep CRON /var/log/cron
```

---

### El comando falla al ejecutarse

**Prueba ejecutarlo manualmente primero:**

```bash
cd /ruta/proyecto
php artisan schedule:run
```

Si funciona manualmente pero no con cron, verifica:
- Que la ruta de `php` sea correcta (puede ser `/usr/bin/php` en vez de `php`)
- Variables de entorno necesarias

**Usar ruta completa de PHP:**

```bash
* * * * * cd /var/www/bmaia_v1_practica && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

Para encontrar la ruta de PHP:
```bash
which php
```

---

## 📋 Comandos Útiles de Crontab

```bash
# Ver tareas programadas
crontab -l

# Editar tareas programadas
crontab -e

# Eliminar todas las tareas programadas (¡cuidado!)
crontab -r

# Editar tareas de otro usuario (requiere sudo)
sudo crontab -u usuario -e
```

---

## 🎯 Resultado Esperado

Una vez configurado correctamente:

✅ El scheduler de Laravel se ejecuta cada minuto
✅ El comando `tareas:actualizar-prioridad` se ejecuta cada hora automáticamente
✅ Las prioridades de las tareas se actualizan sin intervención manual
✅ Los logs registran cada ejecución

---

## 📚 Siguiente Paso

Después de configurar el cron, consulta:
- **`README_PRIORIDADES_AUTOMATICAS.md`** - Visión general del sistema
- **`SCHEDULER_TAREAS.md`** - Configuración avanzada
- **`app/Console/Commands/Como_Usar_Actualizar_Prioridad.md`** - Uso de comandos

---

**Nota**: Esta configuración es para servidores en producción. Para desarrollo local en Windows, usa `ejecutar-scheduler.bat` en lugar de configurar cron.
