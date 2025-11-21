# Sistema de Actualización Automática de Prioridades de Tareas

## 📋 Descripción

Este sistema actualiza automáticamente la prioridad de las tareas según el porcentaje de tiempo transcurrido desde su fecha de inicio hasta su fecha límite.

### Reglas de Actualización:

- **0-24% del tiempo**: Prioridad **Baja**
- **25-49% del tiempo**: Prioridad **Media**
- **50-74% del tiempo**: Prioridad **Alta**
- **75-100% del tiempo**: Prioridad **Urgente**

**Importante**: El sistema solo **aumenta** la prioridad, nunca la disminuye. Por ejemplo, si una tarea ya tiene prioridad "Media", no cambiará hasta que alcance el 50% del tiempo (prioridad "Alta").

---

## 🏗️ Componentes del Sistema

### 1. Command: `ActualizarPrioridadTareas`
**Ubicación**: `app/Console/Commands/ActualizarPrioridadTareas.php`

Este comando contiene la lógica para:
- Calcular el porcentaje de tiempo transcurrido
- Determinar la nueva prioridad
- Actualizar solo si la nueva prioridad es mayor
- Marcar tareas como vencidas si pasan su fecha límite

### 2. Scheduler
**Ubicación**: `routes/console.php`

Programa cada cuánto tiempo se ejecuta el command. Actualmente configurado para ejecutarse **cada hora**.

### 3. Cron del Servidor
Necesario para que Laravel ejecute el scheduler automáticamente.

---

## ⚙️ Configuración del Cron (Servidor)

### Para Servidores Linux/Unix

1. Abre el editor de crontab:
```bash
crontab -e
```

2. Agrega la siguiente línea al final del archivo:
```bash
* * * * * cd /ruta/completa/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

**Reemplaza** `/ruta/completa/a/tu/proyecto` con la ruta real de tu proyecto, por ejemplo:
```bash
* * * * * cd /var/www/bmaia_v1_practica && php artisan schedule:run >> /dev/null 2>&1
```

3. Guarda y cierra el editor (en `vim`: presiona `ESC`, luego escribe `:wq` y `ENTER`)

4. Verifica que el cron esté configurado:
```bash
crontab -l
```

### Para Servidores Windows

**Opción 1: Usar el Programador de Tareas de Windows**

1. Abre el "Programador de tareas" (Task Scheduler)
2. Crea una nueva tarea básica
3. Nombre: "Laravel Scheduler"
4. Desencadenador: "Diario" a las 00:00
5. Acción: "Iniciar un programa"
   - Programa: `php.exe` (ruta completa, ej: `C:\php\php.exe`)
   - Argumentos: `artisan schedule:run`
   - Carpeta: Ruta de tu proyecto (ej: `C:\xampp\htdocs\bmaia_v1_practica`)
6. En "Configuración", marca "Ejecutar la tarea tan pronto como sea posible si se omite un inicio programado"
7. En "Desencadenadores", edita y marca "Repetir la tarea cada: 1 minuto"

**Opción 2: Usar un script .bat**

1. Crea un archivo `scheduler.bat` en tu proyecto:
```batch
@echo off
cd C:\ruta\a\tu\proyecto
php artisan schedule:run
```

2. Programa este archivo .bat para ejecutarse cada minuto usando el Programador de Tareas

### Para Hosting Compartido (cPanel)

1. Accede a tu cPanel
2. Busca "Cron Jobs" o "Tareas Cron"
3. En "Add New Cron Job":
   - **Minuto**: *
   - **Hora**: *
   - **Día**: *
   - **Mes**: *
   - **Día de la semana**: *
   - **Comando**:
   ```bash
   /usr/local/bin/php /home/tuusuario/public_html/artisan schedule:run >> /dev/null 2>&1
   ```
4. Guarda la configuración

---

## 🧪 Pruebas y Verificación

### Ejecutar manualmente el command:
```bash
php artisan tareas:actualizar-prioridad
```

### Verificar que el scheduler reconoce el command:
```bash
php artisan schedule:list
```

### Ver el log de ejecuciones:
Revisa el archivo `storage/logs/laravel.log` para ver los registros de cada actualización.

### Prueba con una tarea específica:

1. Crea una tarea de prueba con:
   - Fecha inicio: Hace 3 días
   - Fecha límite: En 3 días (total: 6 días)
   - Prioridad: Baja

2. Ejecuta el comando manualmente:
```bash
php artisan tareas:actualizar-prioridad
```

3. La tarea debería cambiar a prioridad "Media" (ya que han pasado ~50% del tiempo)

---

## 🔧 Personalizar la Frecuencia de Actualización

Edita el archivo `routes/console.php` y cambia la línea:

```php
Schedule::command('tareas:actualizar-prioridad')->hourly();
```

Por alguna de estas opciones:

```php
// Cada 30 minutos (más reactivo)
Schedule::command('tareas:actualizar-prioridad')->everyThirtyMinutes();

// Cada 15 minutos (muy reactivo, mayor carga del servidor)
Schedule::command('tareas:actualizar-prioridad')->everyFifteenMinutes();

// Cada 6 horas (menos carga, menos preciso)
Schedule::command('tareas:actualizar-prioridad')->everySixHours();

// Una vez al día a las 08:00
Schedule::command('tareas:actualizar-prioridad')->dailyAt('08:00');

// Solo en horario laboral (8:00 - 18:00), días laborables
Schedule::command('tareas:actualizar-prioridad')
    ->hourly()
    ->between('8:00', '18:00')
    ->weekdays();
```

---

## 📊 Salida del Command

Cuando ejecutas el command manualmente, verás:

```
🔄 Iniciando actualización de prioridades de tareas...
✅ Tarea #123: Inspección de colmenas
   Prioridad: media → alta (52.34%)
✅ Tarea #124: Revisión de reinas
   Prioridad: baja → media (28.91%)

📊 Resumen de actualización:
+---------------------+---------+
| Estado              | Cantidad|
+---------------------+---------+
| Tareas actualizadas | 2       |
| Sin cambios         | 45      |
| Errores             | 0       |
| Total procesadas    | 47      |
+---------------------+---------+
✅ Proceso completado exitosamente
```

---

## 🐛 Solución de Problemas

### El cron no se ejecuta:

1. Verifica que el cron esté activo:
```bash
sudo service cron status    # Linux
```

2. Revisa los logs del sistema:
```bash
grep CRON /var/log/syslog   # Linux
```

3. Asegúrate de que el usuario tenga permisos:
```bash
ls -la /ruta/a/tu/proyecto/storage/logs
```

### El command da error:

1. Verifica los logs de Laravel:
```bash
tail -f storage/logs/laravel.log
```

2. Ejecuta el command en modo verbose:
```bash
php artisan tareas:actualizar-prioridad -v
```

### Las prioridades no se actualizan:

1. Verifica que las fechas de las tareas estén correctas
2. Asegúrate de que las tareas no estén archivadas o completadas
3. Revisa que el estado de las tareas no sea "Vencida" o "Completada"

---

## 📝 Notas Importantes

- El sistema **NO disminuye** prioridades, solo las aumenta
- Las tareas completadas o archivadas no se actualizan
- Si una tarea pasa su fecha límite, se marca como "Vencida" y su prioridad se establece en "Urgente"
- El command tiene protección contra ejecuciones simultáneas (`withoutOverlapping()`)
- Los logs se guardan automáticamente en `storage/logs/laravel.log`

---

## 🚀 Comandos Útiles

```bash
# Ejecutar el command manualmente
php artisan tareas:actualizar-prioridad

# Ver todos los commands disponibles
php artisan list

# Ver la programación del scheduler
php artisan schedule:list

# Ejecutar el scheduler manualmente (para probar)
php artisan schedule:run

# Limpiar logs antiguos
php artisan log:clear

# Ver ayuda del command
php artisan help tareas:actualizar-prioridad
```

---

## 📞 Soporte

Si tienes problemas con la configuración, verifica:
1. Que el cron del servidor esté configurado correctamente
2. Que Laravel tenga permisos de escritura en `storage/logs`
3. Que las fechas de las tareas estén en formato válido
4. Los logs en `storage/logs/laravel.log`
