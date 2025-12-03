# 🔄 Sistema de Renovación de Plan de Trabajo Anual

## 📋 Descripción General

El botón "Renovar Plan de Trabajo" es una funcionalidad que permite a los usuarios trasladar automáticamente todas sus tareas al siguiente período anual, actualizando las fechas para mantener la continuidad del trabajo año tras año.

---

## 🎯 Objetivo

Facilitar la transición entre períodos anuales de trabajo, permitiendo que los usuarios mantengan sus planes de trabajo actualizados sin tener que recrear manualmente cada tarea para el nuevo año.

---

## 📍 Ubicación

### En la Interfaz

El botón se encuentra en el **encabezado de la vista "Lista de Tareas"**:

```
┌─────────────────────────────────────────────┐
│  Lista de Tareas 🔍                        │
│  Gestiona y organiza de manera eficiente...│
│                                             │
│  ┌──────────────────────────┐              │
│  │  🔄 Renovar              │ ← BOTÓN      │
│  │     Plan de Trabajo      │              │
│  │     2026 - 2027         │              │
│  └──────────────────────────┘              │
└─────────────────────────────────────────────┘
```

### Características Visuales

- **Icono**: 🔄 (fa-upload)
- **Texto dinámico**: Muestra el próximo período (año actual + 1 a año actual + 2)
- **Tooltip informativo**: Al pasar el mouse, explica qué hace el botón
- **ID**: `actualizarPlanTrabajoBtn`

---

## 🔧 Componentes del Sistema

### 1. **Frontend (Blade Template)**
- **Archivo**: `resources/views/tareas/list.blade.php`
- **Líneas**: 31-44
- **Elementos**:
  ```php
  <button class="btn-Actualizar" id="actualizarPlanTrabajoBtn">
      <i class="fa fa-upload"></i>
      <span>
          Renovar
          Plan de Trabajo
          {{ $year + 1}} - {{ $year + 2}}
      </span>
      <span class="update-tooltip">
          Prepara tu flujo de trabajo para el 2026 - 2027...
      </span>
  </button>
  ```

### 2. **JavaScript (Lógica de Frontend)**
- **Archivo**: `public/js/components/home-user/tasks/list.js`
- **Función**: `configurarActualizarPlanTrabajo()`
- **Líneas**: 112-251
- **Responsabilidades**:
  - Detectar clic en el botón
  - Mostrar confirmaciones
  - Actualizar fechas de las tareas
  - Comunicarse con el backend
  - Mostrar notificaciones

### 3. **Backend (Controlador)**
- **Archivo**: `app/Http/Controllers/TaskController.php`
- **Método**: `actualizarPlanAnual()`
- **Responsabilidades**:
  - Validar datos recibidos
  - Actualizar tareas en base de datos
  - Retornar respuesta JSON

### 4. **Ruta API**
- **Archivo**: `routes/web.php`
- **Ruta**: `POST /tareas/actualizar-plan-anual`
- **Middleware**: `auth` (requiere autenticación)

---

## 🔄 Flujo Completo del Proceso

### Diagrama de Flujo

```
┌──────────────────────────────────────┐
│  Usuario hace clic en el botón      │
│  "Renovar Plan de Trabajo"          │
└────────────┬─────────────────────────┘
             │
             ▼
┌──────────────────────────────────────┐
│  JavaScript detecta el evento        │
│  configurarActualizarPlanTrabajo()   │
└────────────┬─────────────────────────┘
             │
             ▼
┌──────────────────────────────────────┐
│  Primera Confirmación (SweetAlert)   │
│  "¿Deseas actualizar tu plan de      │
│   trabajo para 2026-2027?"           │
│                                       │
│  [Cancelar]        [Sí, continuar]   │
└────────────┬─────────────────────────┘
             │ Usuario acepta
             ▼
┌──────────────────────────────────────┐
│  Obtener todas las tareas visibles   │
│  en la tabla (filtradas)             │
└────────────┬─────────────────────────┘
             │
             ▼
┌──────────────────────────────────────┐
│  Para cada tarea:                    │
│  • Obtener fecha_inicio              │
│  • Obtener fecha_limite              │
│  • Incrementar año (+1)              │
│  • Crear objeto con nuevas fechas    │
└────────────┬─────────────────────────┘
             │
             ▼
┌──────────────────────────────────────┐
│  Segunda Confirmación                │
│  "Se actualizarán X tareas"          │
│  Mostrar resumen de cambios          │
│                                       │
│  [Cancelar]    [Sí, actualizar]      │
└────────────┬─────────────────────────┘
             │ Usuario acepta
             ▼
┌──────────────────────────────────────┐
│  Enviar petición POST al backend     │
│  Endpoint: /tareas/actualizar-plan-  │
│            anual                     │
│  Body: { tareas: [...] }             │
└────────────┬─────────────────────────┘
             │
             ▼
┌──────────────────────────────────────┐
│  BACKEND: TaskController             │
│  actualizarPlanAnual()               │
│                                       │
│  • Validar datos                     │
│  • Actualizar cada tarea en BD       │
│  • Retornar respuesta                │
└────────────┬─────────────────────────┘
             │
             ▼
┌──────────────────────────────────────┐
│  JavaScript recibe respuesta         │
│                                       │
│  ✅ Éxito:                           │
│     • Actualizar UI                  │
│     • Mostrar notificación éxito     │
│     • Recargar página                │
│                                       │
│  ❌ Error:                           │
│     • Mostrar mensaje de error       │
│     • Log en consola                 │
└──────────────────────────────────────┘
```

---

## 📝 Funcionamiento Detallado

### 1. Detección del Clic

```javascript
btn.addEventListener('click', async function () {
    // Se ejecuta cuando el usuario hace clic
});
```

### 2. Primera Confirmación

```javascript
const confirmado = await askConfirm({
    title: '🔄 Actualizar Plan de Trabajo Anual',
    text: '¿Deseas actualizar tu plan de trabajo para el 2026-2027? ' +
          'Todas tus tareas se moverán al próximo año manteniendo ' +
          'sus fechas relativas.',
    confirmText: 'Sí, continuar',
    cancelText: 'Cancelar'
});

if (!confirmado) return; // Usuario canceló
```

### 3. Recopilación de Tareas

```javascript
const tareasActuales = [];
document.querySelectorAll('.task-row').forEach(row => {
    const fechaInicio = row.querySelector('.fecha-inicio').value;
    const fechaLimite = row.querySelector('.fecha-fin').value;
    
    if (!fechaInicio || !fechaLimite) return;
    
    tareasActuales.push({
        id: taskId,
        fecha_inicio_actual: fechaInicio,
        fecha_limite_actual: fechaLimite
    });
});
```

### 4. Cálculo de Nuevas Fechas

```javascript
function incrementYearForDateString(dateStr) {
    // Entrada: "2025-05-15"
    // Parsear fecha
    let dateObj = new Date(dateStr);
    
    // Incrementar año
    dateObj.setFullYear(dateObj.getFullYear() + 1);
    
    // Salida: { iso: "2026-05-15", dmy: "15-05-2026" }
    return {
        iso: "2026-05-15",
        dmy: "15-05-2026"
    };
}
```

**Ejemplo de conversión**:

| Fecha Original | Fecha Nueva |
|---------------|-------------|
| 2025-01-15    | 2026-01-15  |
| 2025-06-30    | 2026-06-30  |
| 2025-12-31    | 2026-12-31  |

### 5. Segunda Confirmación con Resumen

```javascript
const confirmar2 = await askConfirm({
    title: '📋 Resumen de Actualización',
    text: `Se actualizarán ${totalTareas} tareas al período 2026-2027.\n\n` +
          `• Primera tarea: ${primeraTarea.fecha_inicio_actual} → ` +
          `${primeraTarea.fecha_inicio_nueva}\n` +
          `• Última tarea: ${ultimaTarea.fecha_limite_actual} → ` +
          `${ultimaTarea.fecha_limite_nueva}\n\n` +
          `¿Deseas continuar?`,
    confirmText: 'Sí, actualizar ahora',
    cancelText: 'Cancelar'
});
```

**Ejemplo de resumen mostrado**:
```
📋 Resumen de Actualización

Se actualizarán 85 tareas al período 2026-2027.

• Primera tarea: 2025-01-15 → 2026-01-15
• Última tarea: 2025-12-31 → 2026-12-31

¿Deseas continuar?

[Cancelar]  [Sí, actualizar ahora]
```

### 6. Envío al Backend

```javascript
const response = await fetch('/tareas/actualizar-plan-anual', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        tareas: tareasParaActualizar
    })
});
```

**Estructura del Body enviado**:
```json
{
    "tareas": [
        {
            "id": 123,
            "fecha_inicio": "2026-01-15",
            "fecha_limite": "2026-01-31"
        },
        {
            "id": 124,
            "fecha_inicio": "2026-02-01",
            "fecha_limite": "2026-02-28"
        }
        // ... más tareas
    ]
}
```

### 7. Procesamiento Backend

```php
public function actualizarPlanAnual(Request $request)
{
    $validated = $request->validate([
        'tareas' => 'required|array',
        'tareas.*.id' => 'required|integer|exists:sub_tareas,id',
        'tareas.*.fecha_inicio' => 'required|date',
        'tareas.*.fecha_limite' => 'required|date'
    ]);

    foreach ($validated['tareas'] as $tareaData) {
        SubTarea::where('id', $tareaData['id'])
            ->where('user_id', auth()->id())
            ->update([
                'fecha_inicio' => $tareaData['fecha_inicio'],
                'fecha_limite' => $tareaData['fecha_limite']
            ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Plan de trabajo actualizado correctamente'
    ]);
}
```

### 8. Actualización de UI

```javascript
if (result.success) {
    // Mostrar notificación de éxito
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: '✅ Plan Actualizado',
            text: result.message,
            timer: 2500
        });
    }
    
    // Recargar página después de 2.5 segundos
    setTimeout(() => {
        window.location.reload();
    }, 2500);
}
```

---

## 🎨 Diseño y Estilos

### CSS del Botón

```css
.btn-Actualizar {
    position: relative;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border: none;
    border-radius: 0.5rem;
    color: white;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-Actualizar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
}
```

### Tooltip del Botón

```css
.update-tooltip {
    position: absolute;
    right: 100%;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.18s, transform 0.22s;
    background: white;
    border: 1px solid rgba(0,0,0,0.12);
    padding: 0.45rem 0.65rem;
    border-radius: 0.5rem;
    white-space: normal;
    max-width: 470px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.btn-Actualizar:hover .update-tooltip {
    opacity: 1;
    transform: translateY(-50%) translateX(-8px);
}
```

---

## ⚙️ Configuración y Personalización

### Cambiar el Período de Años

Por defecto, el sistema incrementa 1 año. Para cambiar esto:

**En el Blade (list.blade.php)**:
```php
{{ $year + 2}} - {{ $year + 3}}  {{-- Para 2 años adelante --}}
```

**En JavaScript (list.js)**:
```javascript
function incrementYearForDateString(dateStr, yearsToAdd = 1) {
    // ...
    dateObj.setFullYear(dateObj.getFullYear() + yearsToAdd);
    // ...
}
```

### Personalizar Mensajes

Editar en `list.js`, líneas 119-251:

```javascript
// Primera confirmación
title: '🔄 Tu Título Personalizado',
text: 'Tu mensaje personalizado...',

// Segunda confirmación
text: `Se actualizarán ${totalTareas} tareas. Tu mensaje...`,

// Notificación de éxito
text: 'Tu mensaje de éxito personalizado'
```

### Cambiar Comportamiento Post-Actualización

```javascript
// Opción 1: No recargar página (mantener en la vista actual)
// setTimeout(() => {
//     window.location.reload();
// }, 2500);

// Opción 2: Redirigir a otra vista
setTimeout(() => {
    window.location.href = '/dashboard';
}, 2500);

// Opción 3: Solo actualizar datos sin recargar
actualizarTablaSinRecargar();
```

---

## 🔐 Seguridad

### Validaciones Implementadas

1. **Autenticación requerida**: Solo usuarios autenticados pueden usar esta función
2. **Validación de propiedad**: El backend verifica que las tareas pertenezcan al usuario
3. **CSRF Token**: Protección contra ataques CSRF
4. **Validación de datos**: Fechas y IDs son validados en el backend
5. **Confirmaciones dobles**: Previene actualizaciones accidentales

### Verificación de Propiedad

```php
SubTarea::where('id', $tareaData['id'])
    ->where('user_id', auth()->id())  // ← Solo tareas del usuario
    ->update([...]);
```

---

## 🐛 Resolución de Problemas

### El botón no responde

1. **Verificar JavaScript cargado**:
   ```javascript
   // Abrir consola del navegador (F12)
   console.log(typeof configurarActualizarPlanTrabajo);
   // Debería mostrar: "function"
   ```

2. **Verificar ID del botón**:
   ```javascript
   console.log(document.getElementById('actualizarPlanTrabajoBtn'));
   // Debería mostrar el elemento button
   ```

3. **Limpiar caché del navegador**: Ctrl + Shift + R

### Las fechas no se actualizan

1. **Verificar formato de fechas**:
   ```javascript
   // Deben ser YYYY-MM-DD
   console.log(row.querySelector('.fecha-inicio').value);
   ```

2. **Revisar permisos de la tabla**:
   ```sql
   -- Verificar que los campos sean editables
   SHOW COLUMNS FROM sub_tareas;
   ```

3. **Ver errores en consola**: F12 → Console

### Error 419 (CSRF Token)

```javascript
// Verificar que el token existe
console.log(document.querySelector('meta[name="csrf-token"]').content);

// Si es null o undefined, agregar en el blade:
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### Error 403 (Forbidden)

- El usuario intenta actualizar tareas que no le pertenecen
- Verificar autenticación: `auth()->check()`
- Revisar middleware en la ruta

---

## 📊 Casos de Uso

### Caso 1: Renovación de Plan Anual Completo

**Escenario**: Fin de año, el usuario quiere trasladar todas sus 85 tareas al 2026

**Flujo**:
1. Usuario hace clic en "Renovar Plan de Trabajo 2026-2027"
2. Confirma la primera ventana
3. Revisa el resumen: 85 tareas serán actualizadas
4. Confirma la segunda ventana
5. Sistema actualiza todas las tareas
6. Página se recarga con las nuevas fechas

**Resultado**: Todas las tareas ahora tienen fechas de 2026

### Caso 2: Renovación Parcial (Solo Tareas Filtradas)

**Escenario**: Usuario quiere renovar solo las tareas de "Alta" prioridad

**Flujo**:
1. Usuario aplica filtro de prioridad "Alta"
2. Tabla muestra solo 20 tareas de alta prioridad
3. Usuario hace clic en "Renovar Plan de Trabajo"
4. Sistema procesa solo las 20 tareas visibles
5. Confirma y actualiza

**Resultado**: Solo las tareas filtradas fueron actualizadas

### Caso 3: Cancelación del Proceso

**Escenario**: Usuario hace clic por error

**Flujo**:
1. Usuario hace clic en el botón
2. Ve la primera confirmación
3. Hace clic en "Cancelar"
4. Proceso se detiene, no se hace ningún cambio

**Resultado**: No se realiza ninguna actualización

---

## 🎯 Mejores Prácticas

### Para Usuarios

1. **Revisar antes de confirmar**: Leer el resumen de cambios
2. **Filtrar inteligentemente**: Usar filtros si solo quieres actualizar algunas tareas
3. **Hacer backup**: Exportar tareas antes de renovaciones masivas
4. **Ejecutar al final del período**: No renovar a mitad de año

### Para Desarrolladores

1. **Probar en staging**: Nunca probar en producción directamente
2. **Mantener logs**: Registrar todas las actualizaciones masivas
3. **Validar datos**: Asegurar que las fechas sean válidas
4. **Manejar errores**: Capturar y loguear todos los errores
5. **Feedback claro**: Mensajes informativos para el usuario

---

## 📈 Métricas y Monitoreo

### Estadísticas Recomendadas

```javascript
// Log de uso
console.log('Plan actualizado:', {
    usuario_id: userId,
    tareas_actualizadas: tareasActualizadas.length,
    fecha_operacion: new Date(),
    periodo_anterior: '2025-2026',
    periodo_nuevo: '2026-2027'
});
```

### Consultas Útiles

```sql
-- Ver última fecha de actualización de tareas
SELECT MAX(updated_at) as ultima_actualizacion 
FROM sub_tareas 
WHERE user_id = ?;

-- Contar tareas por año
SELECT YEAR(fecha_inicio) as anio, COUNT(*) as total
FROM sub_tareas
WHERE user_id = ?
GROUP BY YEAR(fecha_inicio);
```

---

## 🔄 Actualizaciones Futuras (Roadmap)

### Funcionalidades Planificadas

- [ ] **Previsualización**: Mostrar tabla con cambios antes de aplicar
- [ ] **Deshacer**: Botón para revertir la última renovación
- [ ] **Renovación selectiva**: Checkboxes para elegir tareas específicas
- [ ] **Notificaciones**: Enviar email con resumen de cambios
- [ ] **Historial**: Registro de todas las renovaciones realizadas
- [ ] **Exportar/Importar**: Guardar plan anterior antes de renovar

---

## 🆘 Soporte

### Contacto

- **Email**: soporte@bmaia.com
- **Documentación**: `/docs/plan-trabajo`
- **Issues**: GitHub Issues del proyecto

### Logs para Soporte

Si necesitas ayuda, proporciona:

```javascript
// JavaScript Console (F12)
1. Errores en consola
2. Valor de: document.querySelector('meta[name="csrf-token"]').content
3. Número de tareas afectadas

// Backend
1. Logs de Laravel: storage/logs/laravel.log
2. Líneas con "actualizar-plan-anual"
```

---

## 📚 Referencias

- **Código Frontend**: `public/js/components/home-user/tasks/list.js` (líneas 112-251)
- **Código Backend**: `app/Http/Controllers/TaskController.php` → `actualizarPlanAnual()`
- **Vista**: `resources/views/tareas/list.blade.php` (líneas 31-44)
- **Ruta**: `routes/web.php` → `POST /tareas/actualizar-plan-anual`

---

**Última actualización**: Diciembre 2025  
**Versión**: 1.0  
**Mantenedor**: Equipo de Desarrollo BMAIA
