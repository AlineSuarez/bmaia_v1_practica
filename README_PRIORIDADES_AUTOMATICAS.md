# Sistema de Prioridades Automáticas - Guía Rápida

✅ **Sistema instalado y operativo**

---

## 📚 Documentación Disponible

### 📖 Para Desarrolladores
**`app/Console/Commands/Como_Usar_Actualizar_Prioridad.md`**
- Guía completa de los 3 comandos disponibles
- Ejemplos prácticos de uso
- Cuándo usar cada comando
- Salidas y resultados esperados

### 🔧 Para Administradores/DevOps
**`SCHEDULER_TAREAS.md`**
- Configuración detallada del servidor
- Setup de cron en Linux/Windows/cPanel
- Troubleshooting avanzado
- Personalización de frecuencias

---

## ⚡ Comandos Rápidos

```bash
# Actualizar prioridades (ejecutar cada hora automáticamente)
php artisan tareas:actualizar-prioridad -v

# Verificar estado de tareas
php artisan tareas:verificar [fecha-inicio]

# Corregir prioridades base incorrectas (mantenimiento)
php artisan tareas:corregir-prioridad-base
```

---

## 🚀 Inicio Rápido

### Windows (Desarrollo)
1. Doble clic en: `actualizar-prioridades.bat`
2. O desde terminal: `php artisan tareas:actualizar-prioridad`

### Linux/Producción
Configurar cron (ver `SCHEDULER_TAREAS.md`):
```bash
* * * * * cd /ruta/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📋 Componentes del Sistema

### Archivos Principales

| Archivo | Propósito |
|---------|-----------|
| `app/Console/Commands/ActualizarPrioridadTareas.php` | Comando principal de escalación |
| `app/Console/Commands/VerificarTarea.php` | Comando de diagnóstico |
| `app/Console/Commands/CorregirPrioridadBase.php` | Comando de corrección de datos |
| `routes/console.php` | Configuración del scheduler |
| `database/migrations/*_add_prioridad_base_to_sub_tareas_table.php` | Migración de BD |

### Comandos Batch (Windows)

| Archivo | Uso |
|---------|-----|
| `actualizar-prioridades.bat` | Ejecutar actualización manual |
| `ejecutar-scheduler.bat` | Modo continuo (simula cron) |

---

## 📊 Funcionamiento

```
┌─────────────────────────────────────────────────────┐
│  Tarea creada con prioridad_base = "media"          | 
│  └─> prioridad actual = "media"                     │
└─────────────────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────────┐
│  Scheduler ejecuta diariamente a las 02:00 AM      │
│  └─> Calcula % tiempo transcurrido                  │
└─────────────────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────────┐
│  50% del tiempo transcurrido                        │
│  └─> prioridad = "alta" (escalada)                  │
│  └─> prioridad_base = "media" (sin cambios)         │
└─────────────────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────────┐
│  Usuario completa la tarea                          │
│  └─> estado = "completada"                          │
└─────────────────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────────┐
│  Scheduler detecta tarea completada                 │
│  └─> prioridad = "media" (restaurada)               │
└─────────────────────────────────────────────────────┘
```

### Reglas de Escalación

| % Tiempo | Prioridad |
|----------|-----------|
| 0-24%    | 🟢 Baja   |
| 25-49%   | 🟡 Media  |
| 50-74%   | 🟠 Alta   |
| 75-100%  | 🔴 Urgente|

---

## ✅ Verificación Rápida

```bash
# Ver comandos programados
php artisan schedule:list

# Ejecutar manualmente con detalles
php artisan tareas:actualizar-prioridad -v

# Ver logs
tail -f storage/logs/laravel.log

# Modificar Estados (ejemplo Venciada --> Pendiente)
php artisan tinker --execute="App\Models\SubTarea::where('estado', 'Vencida')->update(['estado' => 'Pendiente']); echo 'Tareas actualizadas: ' . App\Models\SubTarea::where('estado', 'Pendiente')->count();"
```

---

## 🔗 Enlaces Útiles

- **Uso de comandos**: `app/Console/Commands/Como_Usar_Actualizar_Prioridad.md`
- **Configuración servidor**: `SCHEDULER_TAREAS.md`
- **Código fuente**: `app/Console/Commands/ActualizarPrioridadTareas.php`

---

## 📝 Notas Importantes

- ✅ El sistema **solo aumenta** prioridades automáticamente
- ✅ Al completar una tarea, **restaura a prioridad_base**
- ✅ Las tareas **no escalan antes de su fecha_inicio**
- ✅ Las tareas archivadas o completadas **no se procesan**
- ✅ Protección contra **ejecuciones simultáneas** incluida

---

**Estado**: Sistema completamente funcional y documentado
**Fecha de Creación**: Noviembre 2025
**Ultima Modificación**: Diciembre 2025
