# Sistema de Prioridades Automáticas

Documentación de comandos para la gestión automática de prioridades de tareas en B-MaiA.

---

## 📋 Descripción General

El sistema de prioridades automáticas ajusta dinámicamente la prioridad de las tareas según el tiempo transcurrido entre la fecha de inicio y la fecha límite:

- **25% del tiempo**: Prioridad sube a **media**
- **50% del tiempo**: Prioridad sube a **alta**
- **75% del tiempo**: Prioridad sube a **urgente**

Cuando una tarea se completa, su prioridad se restaura automáticamente a su **prioridad base** original.

---

## 🔧 Comandos Disponibles

### 1. `tareas:actualizar-prioridad` (Principal)

**Comando principal del sistema** que ejecuta la escalación y restauración de prioridades.

#### Uso básico:
```bash
php artisan tareas:actualizar-prioridad
```

#### Uso con salida detallada:
```bash
php artisan tareas:actualizar-prioridad -v
```

#### ¿Qué hace?
1. **Restaura prioridades** de tareas completadas a su prioridad base
2. **Escala prioridades** de tareas activas según tiempo transcurrido
3. **Respeta fecha de inicio**: No aumenta prioridad antes de la fecha de inicio
4. **Ignora tareas archivadas**: Solo procesa tareas activas

#### Salida ejemplo:
```
🔄 Iniciando actualización de prioridades de tareas...
🔄 Tarea #93: Comprar alimento
   Prioridad restaurada: urgente → baja (Completada)
⬆️ Tarea #45: Inspeccionar colmenas
   Prioridad actualizada: media → alta (56% del tiempo transcurrido)

📊 Resumen de actualización:
+----------------------------------+----------+
| Estado                           | Cantidad |
+----------------------------------+----------+
| Tareas actualizadas              | 15       |
| Tareas restauradas (completadas) | 8        |
| Sin cambios                      | 221      |
| Errores                          | 0        |
| Total procesadas                 | 244      |
+----------------------------------+----------+
```

#### Ejecución automática:
Este comando se ejecuta **automáticamente cada hora** a través del Laravel Scheduler (configurado en `routes/console.php`).

#### Cuándo ejecutarlo manualmente:
- Para probar el funcionamiento
- Después de cambiar fechas de tareas masivamente
- Para forzar actualización inmediata sin esperar al cron

---

### 2. `tareas:verificar` (Debug)

**Comando de diagnóstico** para inspeccionar el estado de las prioridades de tareas.

#### Uso básico:
```bash
php artisan tareas:verificar
```

#### Filtrar por fecha de inicio:
```bash
php artisan tareas:verificar 2025-01-27
```

#### ¿Qué hace?
Muestra información detallada de cada tarea:
- ID de la tarea
- Nombre
- Prioridad actual
- Prioridad base
- Estado (Pendiente, Completada, Vencida)
- Fecha de inicio
- Fecha límite

#### Salida ejemplo:
```
Tareas encontradas con fecha de inicio: 2025-01-27

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ID: 76
Nombre: Cosechar miel
Prioridad: urgente
Prioridad Base: alta
Estado: Vencida
Fecha Inicio: 2025-01-27 00:00:00
Fecha Límite: 2025-05-05 00:00:00
```

#### Cuándo usarlo:
- Para verificar que las prioridades base están correctas
- Para debuggear problemas de escalación
- Para auditar tareas específicas por fecha

---

### 3. `tareas:corregir-prioridad-base` (Mantenimiento)

**Comando de corrección de datos** para restaurar prioridades base incorrectas.

#### Uso:
```bash
php artisan tareas:corregir-prioridad-base
```

#### ¿Qué hace?
1. Busca tareas cuya `prioridad_base` no corresponde con su definición original
2. Compara el nombre de cada tarea con `tareas_predefinidas`
3. Restaura la prioridad base correcta desde la definición
4. Para tareas personalizadas sin definición, establece 'baja' como base

#### Salida ejemplo:
```
🔧 Iniciando corrección de prioridades base...
✅ Corregida: Verificar disponibilidad de alimento en bodega
   Base anterior: urgente → Nueva base: media
✅ Corregida: Comprar alimento
   Base anterior: urgente → Nueva base: baja

📊 Resumen:
+--------------------------+----------+
| Estado                   | Cantidad |
+--------------------------+----------+
| Tareas corregidas        | 141      |
| Sin corrección necesaria | 107      |
| Total procesadas         | 248      |
+--------------------------+----------+
```

#### ¿Por qué existió este problema?
Cuando se implementó el campo `prioridad_base`, la migración inicial copió el valor de `prioridad` actual. Algunas tareas ya habían sido escaladas automáticamente, por lo que se guardó la prioridad escalada como "base" en lugar de la original.

#### Cuándo usarlo:
- ✅ **Ya se ejecutó una vez** durante la implementación inicial
- 🔄 Después de importar datos de backups antiguos
- 🔄 Si se edita la base de datos manualmente y se corrompen datos
- 🔄 Para auditar integridad de datos después de cambios masivos

#### ¿Es seguro ejecutarlo múltiples veces?
**Sí**. El comando:
- Solo actualiza tareas que realmente necesitan corrección
- No causa efectos secundarios
- Muestra claramente qué cambios realiza

---

## 📊 Niveles de Prioridad

| Nivel    | Valor | Cuándo se aplica                    |
|----------|-------|-------------------------------------|
| baja     | 1     | Prioridad inicial (0-24% tiempo)    |
| media    | 2     | Al pasar 25% del tiempo             |
| alta     | 3     | Al pasar 50% del tiempo             |
| urgente  | 4     | Al pasar 75% del tiempo o vencida   |

---

## 🔄 Flujo de Trabajo Automático

```
1. Tarea creada con prioridad_base = 'media'
   └─> prioridad = 'media' (inicial)

2. Scheduler ejecuta cada hora
   └─> Calcula % tiempo transcurrido

3. Al pasar 50% del tiempo:
   └─> prioridad = 'alta' (escalada)
   └─> prioridad_base = 'media' (sin cambios)

4. Usuario completa la tarea:
   └─> estado = 'completada'

5. Scheduler detecta tarea completada:
   └─> prioridad = 'media' (restaurada desde prioridad_base)
```

---

## ⚙️ Configuración del Scheduler

El comando se ejecuta automáticamente gracias a la configuración en `routes/console.php`:

```php
Schedule::command('tareas:actualizar-prioridad')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();
```

### Para que funcione, debes tener configurado el cron:

**En Linux/macOS:**
```bash
* * * * * cd /ruta/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

**En Windows (Programador de Tareas):**
- Ver archivo: `ejecutar-scheduler.bat`
- O consultar: `INSTALACION_SCHEDULER.md`

---

## 📝 Archivos Relacionados

- **Comando Principal**: `app/Console/Commands/ActualizarPrioridadTareas.php`
- **Comando Debug**: `app/Console/Commands/VerificarTarea.php`
- **Comando Corrección**: `app/Console/Commands/CorregirPrioridadBase.php`
- **Configuración Scheduler**: `routes/console.php`
- **Modelo**: `app/Models/SubTarea.php`
- **Guía de Instalación**: `INSTALACION_SCHEDULER.md`
- **Documentación Técnica**: `SCHEDULER_TAREAS.md`

---

## 🐛 Solución de Problemas

### Las prioridades no se actualizan automáticamente
1. Verifica que el scheduler esté ejecutándose: `php artisan schedule:list`
2. Revisa los logs: `storage/logs/laravel.log`
3. Ejecuta manualmente con `-v` para ver detalles: `php artisan tareas:actualizar-prioridad -v`

### Las tareas completadas no restauran su prioridad
1. Verifica que `prioridad_base` tenga un valor: `php artisan tareas:verificar`
2. Ejecuta corrección si es necesario: `php artisan tareas:corregir-prioridad-base`

### Las tareas aumentan prioridad antes de su fecha de inicio
El comando ya valida esto. Si ocurre:
1. Verifica la fecha de inicio de la tarea en la BD
2. Ejecuta con `-v` para ver qué tareas se procesan
3. Revisa logs para errores



<!------------------------------------------------------------------------------------>
<!------------------------------------------------------------------------------------>
<!------------------------------------------------------------------------------------>
### Tareas con estado "Vencida"
El sistema marca automáticamente como "Vencida" las tareas que superaron su fecha límite sin completarse. Para gestionarlas:

**Ver cuántas tareas están vencidas:**
```bash
php artisan tinker --execute="echo 'Tareas Vencidas: ' . App\Models\SubTarea::where('estado', 'Vencida')->count();"
```

**Ver detalles de tareas vencidas:**
```bash
php artisan tinker --execute="App\Models\SubTarea::where('estado', 'Vencida')->get(['id', 'nombre', 'fecha_limite'])->each(function(\$t) { echo \"ID: {\$t->id} | {\$t->nombre} | Límite: {\$t->fecha_limite}\" . PHP_EOL; });"
```

**Cambiar tareas vencidas a pendiente (si aún son relevantes):**
```bash
php artisan tinker --execute="\$count = App\Models\SubTarea::where('estado', 'Vencida')->update(['estado' => 'Pendiente']); echo \"Tareas actualizadas: \$count\";"
```

---

## 🔍 Comandos Útiles de Consulta

### Consultar estado de tareas

**Contar tareas por estado:**
```bash
php artisan tinker --execute="
\$estados = App\Models\SubTarea::select('estado', DB::raw('count(*) as total'))
    ->groupBy('estado')
    ->get();
foreach(\$estados as \$e) { 
    echo \"\$e->estado: \$e->total\" . PHP_EOL; 
}"
```

**Contar tareas por prioridad:**
```bash
php artisan tinker --execute="
\$prioridades = App\Models\SubTarea::select('prioridad', DB::raw('count(*) as total'))
    ->groupBy('prioridad')
    ->get();
foreach(\$prioridades as \$p) { 
    echo \"\$p->prioridad: \$p->total\" . PHP_EOL; 
}"
```

**Ver tareas con prioridad urgente:**
```bash
php artisan tinker --execute="
App\Models\SubTarea::where('prioridad', 'urgente')
    ->where('archivada', false)
    ->get(['id', 'nombre', 'estado', 'fecha_limite'])
    ->each(function(\$t) { 
        echo \"ID: {\$t->id} | {\$t->nombre} | Estado: {\$t->estado} | Límite: {\$t->fecha_limite}\" . PHP_EOL; 
    });"
```

### Operaciones masivas

**Cambiar estado de múltiples tareas:**
```bash
# Ejemplo: Cambiar todas las tareas "En progreso" a "Pendiente"
php artisan tinker --execute="\$count = App\Models\SubTarea::where('estado', 'En progreso')->update(['estado' => 'Pendiente']); echo \"Actualizadas: \$count\";"
```

**Resetear prioridades a su valor base:**
```bash
php artisan tinker --execute="
App\Models\SubTarea::whereNotNull('prioridad_base')
    ->chunk(100, function(\$tareas) {
        foreach(\$tareas as \$tarea) {
            \$tarea->update(['prioridad' => \$tarea->prioridad_base]);
        }
    });
echo 'Prioridades reseteadas a su valor base';"
```

### Estado del sistema

**Ver tareas que serán procesadas por el scheduler:**
```bash
php artisan tinker --execute="
\$tareas = App\Models\SubTarea::where('archivada', false)
    ->whereNotIn('estado', ['Completada', 'Vencida'])
    ->count();
echo \"Tareas activas que serán procesadas: \$tareas\";"
```

**Verificar tareas sin prioridad_base:**
```bash
php artisan tinker --execute="
\$count = App\Models\SubTarea::whereNull('prioridad_base')
    ->orWhere('prioridad_base', '')
    ->count();
echo \"Tareas sin prioridad_base: \$count\";"
```

---

## ℹ️ Información Adicional

### ¿Qué es el estado "Vencida"?

El estado "Vencida" se asigna automáticamente cuando:
- La `fecha_limite` de la tarea ha pasado
- La tarea NO está completada
- La tarea NO está archivada
- El comando `tareas:actualizar-prioridad` se ejecuta

**Cuando una tarea se marca como "Vencida":**
- Su `estado` cambia a "Vencida"
- Su `prioridad` se establece en "urgente"
- Su `prioridad_base` permanece sin cambios (conserva el valor original)

**Esto sucede en:** `app/Console/Commands/ActualizarPrioridadTareas.php`

### Diferencia entre `prioridad` y `prioridad_base`

- **`prioridad`**: Prioridad actual de la tarea (puede cambiar con el tiempo)
- **`prioridad_base`**: Prioridad original definida al crear la tarea (nunca cambia automáticamente)

**Ejemplo:**
```
Tarea creada:
├─ prioridad: "media"
└─ prioridad_base: "media"

Después del 50% del tiempo:
├─ prioridad: "alta" (escalada automáticamente)
└─ prioridad_base: "media" (sin cambios)

Al completar:
├─ prioridad: "media" (restaurada desde prioridad_base)
└─ prioridad_base: "media" (sin cambios)
```

---

## 📞 Soporte

Para más información técnica, consulta:
- `SCHEDULER_TAREAS.md` - Documentación técnica completa
- `INSTALACION_SCHEDULER.md` - Guía de instalación paso a paso