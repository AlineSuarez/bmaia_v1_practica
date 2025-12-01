# 📊 Cambio de Estado en Timeline

## 📋 Índice
- [Descripción General](#-descripción-general)
- [Ubicación en la Interfaz](#-ubicación-en-la-interfaz)
- [Componentes del Sistema](#-componentes-del-sistema)
- [Flujo Completo de Cambio de Estado](#-flujo-completo-de-cambio-de-estado)
- [Funcionalidad Detallada](#-funcionalidad-detallada)
- [Estados y Transiciones](#-estados-y-transiciones)
- [Integración con Google Calendar](#-integración-con-google-calendar)
- [Diseño y Estilos](#-diseño-y-estilos)
- [Casos de Uso](#-casos-de-uso)
- [Solución de Problemas](#-solución-de-problemas)
- [Mejores Prácticas](#-mejores-prácticas)

---

## 🎯 Descripción General

La vista **Timeline** (Línea de Tiempo) permite visualizar y gestionar las tareas organizadas por etapas del proyecto. Una de sus funcionalidades principales es el **cambio de estado de tareas**, que permite marcar tareas como Pendiente, En Progreso o Completada mediante un sistema de selección intuitivo con confirmación en dos pasos.

### Características Principales

- ✅ **Cambio de estado por select**: Cada tarea tiene un dropdown para cambiar su estado
- 🔄 **Sistema de confirmación**: Los cambios se acumulan y se confirman en bloque
- 📊 **Actualización automática de progreso**: La barra de progreso se actualiza en tiempo real
- 🎨 **Feedback visual inmediato**: Las tarjetas cambian de apariencia según el estado
- 🗓️ **Sincronización con Google Calendar**: Las tareas completadas se eliminan del calendario
- ⚡ **Operaciones en lote**: Permite cambiar múltiples tareas simultáneamente

---

## 📍 Ubicación en la Interfaz

### Acceso a la Vista Timeline

1. **Ruta**: `/tareas/timeline`
2. **Navegación**: Menú principal → "Línea de tiempo"
3. **Icono**: 🕒 (reloj con flecha circular)
4. **Header**: "Línea de tiempo" con subtítulo "Visualiza el avance y estado de tus tareas por etapas"

### Estructura Visual

```
┌─────────────────────────────────────────────────┐
│  🕒 Línea de tiempo                            │
│  Visualiza el avance y estado de tus tareas... │
├─────────────────────────────────────────────────┤
│                                                 │
│  ┌─────────────────────────────────────────┐   │
│  │ Etapa 1: Diseño                    75% │   │ ← Click para expandir/colapsar
│  │ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓░░░░░               │   │
│  ├─────────────────────────────────────────┤   │
│  │ 📋 Crear mockups                        │   │
│  │ 🟡 Media  [Estado: Completada ▼]       │   │ ← Select para cambiar estado
│  │ 📅 Inicio: 01/12  Límite: 15/12        │   │
│  ├─────────────────────────────────────────┤   │
│  │ 📋 Definir paleta de colores            │   │
│  │ 🔴 Alta   [Estado: En progreso ▼]      │   │
│  │ 📅 Inicio: 05/12  Límite: 10/12        │   │
│  ├─────────────────────────────────────────┤   │
│  │                                         │   │
│  │ 3 de 5 completadas                      │   │
│  │ [✓ Aplicar cambios (2)]   [x Cancelar] │   │ ← Botones de acción
│  └─────────────────────────────────────────┘   │
└─────────────────────────────────────────────────┘
```

---

## 🔧 Componentes del Sistema

### 1. Frontend (Blade Template)

**Archivo**: `resources/views/tareas/timeline.blade.php`

#### Estructura de una Tarjeta de Tarea

```html
<div class="task-mini-card" data-task-id="{{ $subtarea->id }}">
    <div class="task-header">
        <!-- Placeholder para mantener alineación -->
        <div class="task-checkbox-placeholder" aria-hidden="true"></div>
        
        <!-- Nombre de la tarea -->
        <div class="task-name">{{ $subtarea->nombre }}</div>
        
        <div class="task-meta">
            <div class="task-badges">
                <!-- Badge de prioridad (visual) -->
                <span class="prioridad-badge" data-prioridad="{{ strtolower($subtarea->prioridad) }}">
                    <span class="prio">
                        <span class="prio-dot"></span>
                        {{ ucfirst($subtarea->prioridad) }}
                    </span>
                </span>

                <!-- Select de estado (interactivo) -->
                <select 
                    class="estado-badge estado-{{ strtolower(str_replace(' ', '', $subtarea->estado)) }}"
                    data-id="{{ $subtarea->id }}" 
                    data-current-state="{{ $subtarea->estado }}"
                    aria-label="Cambiar estado">
                    <option value="Pendiente" {{ $subtarea->estado === 'Pendiente' ? 'selected' : '' }}>
                        Pendiente
                    </option>
                    <option value="En progreso" {{ $subtarea->estado === 'En progreso' ? 'selected' : '' }}>
                        En progreso
                    </option>
                    <option value="Completada" {{ $subtarea->estado === 'Completada' ? 'selected' : '' }}>
                        Completada
                    </option>
                </select>
            </div>
            
            <!-- Fechas de la tarea -->
            <div class="task-dates">
                <!-- Fecha de inicio y fecha límite -->
            </div>
        </div>
    </div>
</div>
```

#### Botones de Acción en el Footer

```html
<div class="etapa-footer">
    <div class="etapa-summary">
        {{ $completadas }} de {{ $total }} completadas
    </div>
    
    <div class="etapa-actions">
        <button 
            class="btn-etapa completar-seleccionadas-btn" 
            data-etapa-id="{{ $tareaGeneral->id }}" 
            disabled>
            ✓ Completar (<span class="completar-count">0</span>)
        </button>
    </div>
</div>
```

### 2. JavaScript (Lógica de Interacción)

**Archivo**: `resources/views/tareas/timeline.blade.php` (inline script)

#### Funciones Principales

```javascript
// 1. Actualizar estado de una tarea (petición PATCH)
function actualizarEstadoTarea(subtareaId, nuevoEstado) {
    // Marca la tarjeta como "updating"
    // Envía petición PATCH a /tareas/{id}/update-status
    // Actualiza la interfaz en caso de éxito
    // Recalcula el progreso de la etapa
}

// 2. Actualizar interfaz de una tarea
function actualizarInterfazTarea(subtareaId, nuevoEstado) {
    // Cambia la clase CSS de la tarjeta (completed/no completed)
    // Actualiza el valor del select
    // Actualiza las clases del badge de estado
}

// 3. Recalcular progreso de todas las etapas
function recalcularProgreso() {
    // Cuenta tareas totales y completadas
    // Calcula porcentaje de progreso
    // Actualiza barra de progreso y textos
}

// 4. Actualizar botones de acción
function actualizarBotones() {
    // Detecta cambios pendientes (valor select ≠ data-current-state)
    // Habilita/deshabilita botón "Completar"/"Aplicar cambios"
    // Actualiza el contador de cambios pendientes
}
```

### 3. Backend (Laravel Controller)

**Archivo**: `app/Http/Controllers/TaskController.php`

#### Método Principal: `updateStatus()`

```php
public function updateStatus(Request $request, $id)
{
    // 1. Validar datos de entrada
    $request->validate([
        'estado' => 'required|string|in:Pendiente,En progreso,Completada'
    ]);

    // 2. Buscar subtarea
    $subtarea = SubTarea::findOrFail($id);
    $estadoAnterior = $subtarea->estado;

    // 3. Actualizar estado
    $subtarea->estado = $request->estado;
    $subtarea->save();

    // 4. Eliminar de Google Calendar si se marca como Completada
    if ($request->estado === 'Completada') {
        $this->eliminarDeGoogleCalendar($subtarea);
    }

    // 5. Retornar respuesta JSON
    return response()->json([
        'success' => true,
        'message' => 'Estado actualizado correctamente',
        'estado' => $subtarea->estado
    ]);
}
```

#### Método de Eliminación de Google Calendar

```php
private function eliminarDeGoogleCalendar(SubTarea $subtarea)
{
    try {
        // Configurar cliente de Google
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/google-calendar/credentials.json'));
        
        // Buscar evento por nombre y fecha
        $service = new Google_Service_Calendar($client);
        $events = $service->events->listEvents(
            'primary',
            ['q' => $subtarea->nombre]
        );

        // Eliminar eventos coincidentes
        foreach ($events->getItems() as $event) {
            $service->events->delete('primary', $event->getId());
        }
    } catch (\Exception $e) {
        \Log::error('Error al eliminar de Google Calendar: ' . $e->getMessage());
    }
}
```

### 4. Estilos CSS

**Archivo**: `public/css/components/home-user/tasks/timeline.css`

#### Variables CSS

```css
:root {
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --error-color: #ef4444;
    --info-color: #3b82f6;
    --transition: 0.2s ease-in-out;
}
```

#### Estilos de Estados

```css
/* Estado Pendiente */
.estado-pendiente {
    background: #fffbeb;
    color: #d97706;
    border-color: #fed7aa;
}

/* Estado En Progreso */
.estado-enprogreso {
    background: #eff6ff;
    color: #2563eb;
    border-color: #bfdbfe;
}

/* Estado Completada */
.estado-completada {
    background: #f0fdf4;
    color: #16a34a;
    border-color: #bbf7d0;
}

/* Tarjeta completada */
.task-mini-card.completed {
    border-left-color: var(--success-color);
    background: linear-gradient(135deg, var(--bg-primary) 0%, rgba(16, 185, 129, 0.05) 100%);
}
```

---

## 🔄 Flujo Completo de Cambio de Estado

### Diagrama de Flujo

```
┌─────────────────────────────────────────────────────────┐
│ 1. USUARIO CAMBIA SELECT DE ESTADO                     │
│    (click en dropdown, selecciona nuevo estado)         │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 2. EVENTO 'change' SE DISPARA                           │
│    - Detecta cambio en select.estado-badge              │
│    - Compara valor actual vs data-current-state         │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 3. FUNCIÓN actualizarBotones() SE EJECUTA              │
│    - Cuenta selects con cambios pendientes              │
│    - Habilita botón "Aplicar cambios (N)"               │
│    - Determina si todos son "Completada" o mixto        │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 4. USUARIO HACE CLICK EN "APLICAR CAMBIOS (N)"         │
│    - Botón cambia a estado de confirmación             │
│    - Aparecen botones [x Cancelar] [Confirmar (N)]     │
└────────────────────┬────────────────────────────────────┘
                     │
          ┌──────────┴──────────┐
          │                     │
          ▼                     ▼
┌──────────────────┐   ┌──────────────────┐
│ USUARIO CANCELA  │   │ USUARIO CONFIRMA │
│  - Restaura      │   │  - Ejecuta       │
│    botón orig.   │   │    peticiones    │
│  - No hay cambios│   │    PATCH         │
└──────────────────┘   └────────┬─────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────┐
│ 5. EJECUTAR PETICIONES FETCH (Promise.all)             │
│    Para cada cambio pendiente:                          │
│    - Marca tarjeta como .updating (spinner)             │
│    - Envía PATCH /tareas/{id}/update-status             │
│    - Payload: { estado: "Nuevo Estado" }                │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 6. BACKEND PROCESA PETICIÓN (TaskController)           │
│    - Valida datos de entrada                            │
│    - Busca subtarea por ID                              │
│    - Actualiza campo 'estado' en base de datos          │
│    - Si estado = "Completada":                          │
│      └─> Llama eliminarDeGoogleCalendar()               │
│    - Retorna JSON { success: true, estado: "..." }      │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 7. FRONTEND RECIBE RESPUESTA                            │
│    Si success = true:                                    │
│    - Ejecuta actualizarInterfazTarea()                  │
│      └─> Actualiza clases CSS de tarjeta                │
│      └─> Actualiza valor del select                     │
│      └─> Actualiza data-current-state                   │
│    - Ejecuta recalcularProgreso()                       │
│      └─> Recalcula % de tareas completadas              │
│      └─> Actualiza barra de progreso                    │
│    - Remueve clase .updating                            │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 8. RECARGA DE PÁGINA (location.reload)                 │
│    - Garantiza sincronización completa                  │
│    - Muestra estado actualizado desde base de datos     │
│    - Resetea contadores y botones                       │
└─────────────────────────────────────────────────────────┘
```

### Flujo con Google Calendar

```
[Estado cambiado a "Completada"]
            │
            ▼
[Backend: eliminarDeGoogleCalendar()]
            │
            ├─> Configurar Google_Client
            │   └─> Cargar credentials.json
            │
            ├─> Buscar eventos en calendario
            │   └─> Query: nombre de la tarea
            │   └─> Filtrar por fecha si coincide
            │
            ├─> Eliminar eventos encontrados
            │   └─> service->events->delete()
            │
            └─> Log de errores si falla
                └─> No bloquea la actualización
```

---

## ⚙️ Funcionalidad Detallada

### 1. Detección de Cambios Pendientes

El sistema detecta cambios comparando el valor actual del select con el atributo `data-current-state`:

```javascript
function actualizarBotones() {
    const selects = etapaCard.querySelectorAll('.estado-badge');
    
    // Filtrar selects con cambios
    const cambios = Array.from(selects).filter(sel => {
        const original = sel.getAttribute('data-current-state');
        return sel.value !== original;
    });
    
    if (cambios.length > 0) {
        const allToComplete = cambios.every(s => s.value === 'Completada');
        const label = allToComplete ? '✓ Completar' : 'Aplicar cambios';
        btnCompletar.disabled = false;
        btnCompletar.innerHTML = `${label} (<span class="completar-count">${cambios.length}</span>)`;
    } else {
        btnCompletar.disabled = true;
        btnCompletar.innerHTML = '✓ Completar (<span class="completar-count">0</span>)';
    }
}
```

### 2. Sistema de Confirmación en Dos Pasos

#### Paso 1: Click en "Aplicar cambios"

```javascript
btnCompletar.addEventListener('click', function(e) {
    e.preventDefault();
    
    // Ocultar botón original con animación fade-out
    this.classList.add('fade-out');
    setTimeout(() => {
        this.style.display = 'none';
        
        // Crear botones de confirmación
        const btnCancelar = document.createElement('button');
        btnCancelar.className = 'btn-etapa cancelar fade-in';
        btnCancelar.textContent = 'x Cancelar';
        
        const btnConfirmar = document.createElement('button');
        btnConfirmar.className = 'btn-etapa confirmar fade-in';
        btnConfirmar.textContent = `¿Confirmar cambios (${cambios.length})?`;
        
        // Agregar a la interfaz
        actions.appendChild(btnCancelar);
        actions.appendChild(btnConfirmar);
    }, 250);
});
```

#### Paso 2a: Confirmación (Guardar)

```javascript
btnConfirmar.addEventListener('click', () => {
    // Crear array de promesas
    const promises = cambios.map(sel => {
        const subtareaId = sel.getAttribute('data-id');
        const nuevoEstado = sel.value;
        
        // Marcar tarjeta como updating
        const card = document.querySelector(`[data-task-id="${subtareaId}"]`);
        if (card) card.classList.add('updating');
        
        return actualizarEstadoTarea(subtareaId, nuevoEstado)
            .catch(err => ({ success: false, error: err }));
    });
    
    // Cambiar texto del botón
    btnConfirmar.textContent = 'Procesando...';
    btnConfirmar.disabled = true;
    
    // Ejecutar todas las peticiones
    Promise.all(promises).then(results => {
        location.reload(); // Recargar para mostrar estado real
    }).catch(() => {
        location.reload(); // Recargar incluso si hay errores
    });
});
```

#### Paso 2b: Cancelación

```javascript
btnCancelar.addEventListener('click', () => {
    // Animar salida de botones de confirmación
    btnConfirmar.classList.add('fade-out');
    btnCancelar.classList.add('fade-out');
    
    setTimeout(() => {
        btnConfirmar.remove();
        btnCancelar.remove();
        
        // Restaurar botón original
        originalBtn.style.display = '';
        originalBtn.classList.remove('fade-out');
        originalBtn.classList.add('fade-in');
        
        // Recalcular estado de botones
        actualizarBotones();
    }, 250);
});
```

### 3. Actualización de Progreso en Tiempo Real

```javascript
function recalcularProgreso() {
    document.querySelectorAll('.etapa-card').forEach(etapaCard => {
        // Contar tareas
        const totalTasks = etapaCard.querySelectorAll('.task-mini-card').length;
        const completedTasks = etapaCard.querySelectorAll('.task-mini-card.completed').length;
        
        // Calcular porcentaje
        const progreso = totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0;
        
        // Actualizar elementos visuales
        const progressFill = etapaCard.querySelector('.progress-fill');
        const progressText = etapaCard.querySelector('.progress-text');
        const etapaSummary = etapaCard.querySelector('.etapa-summary');
        
        if (progressFill) progressFill.style.width = `${progreso}%`;
        if (progressText) progressText.textContent = `${progreso}%`;
        if (etapaSummary) etapaSummary.textContent = `${completedTasks} de ${totalTasks} completadas`;
    });
}
```

### 4. Feedback Visual de Estados

#### Durante la actualización (Spinner)

```css
.task-mini-card.updating {
    opacity: 0.6;
    pointer-events: auto;
    position: relative;
}

.task-mini-card.updating::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 16px;
    height: 16px;
    border: 2px solid var(--border-light);
    border-top-color: var(--primary-color);
    border-radius: 50%;
    animation: tareas-spin 1s linear infinite;
}

@keyframes tareas-spin {
    to { transform: translate(-50%, -50%) rotate(360deg); }
}
```

#### En caso de error (Shake animation)

```css
.task-mini-card.error {
    border-left-color: var(--error-color);
    background: linear-gradient(135deg, var(--bg-primary) 0%, rgba(239, 68, 68, 0.05) 100%);
    animation: tareas-shake 0.3s ease-in-out;
}

@keyframes tareas-shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-2px); }
    75% { transform: translateX(2px); }
}
```

---

## 🔀 Estados y Transiciones

### Estados Disponibles

| Estado | Color | Icono | Descripción |
|--------|-------|-------|-------------|
| **Pendiente** | 🟡 Amarillo | - | Tarea no iniciada |
| **En progreso** | 🔵 Azul | - | Tarea en desarrollo |
| **Completada** | 🟢 Verde | ✓ | Tarea finalizada |

### Matriz de Transiciones

```
         │ Pendiente │ En progreso │ Completada │
─────────┼───────────┼─────────────┼────────────┤
Pendiente│     -     │      ✓      │     ✓      │
─────────┼───────────┼─────────────┼────────────┤
En prog. │     ✓     │      -      │     ✓      │
─────────┼───────────┼─────────────┼────────────┤
Completada│    ✓     │      ✓      │     -      │
─────────┴───────────┴─────────────┴────────────┘
```

**Nota**: Todas las transiciones están permitidas. El usuario puede mover una tarea de cualquier estado a cualquier otro.

### Efectos de Cada Transición

```
PENDIENTE → EN PROGRESO
├─> Color del badge: amarillo → azul
├─> Barra de progreso: sin cambio
└─> Google Calendar: sin acción

PENDIENTE → COMPLETADA
├─> Color del badge: amarillo → verde
├─> Barra de progreso: +1 completada
├─> Tarjeta: borde izquierdo verde
├─> Google Calendar: eliminar evento
└─> Background: gradiente verde sutil

EN PROGRESO → COMPLETADA
├─> Color del badge: azul → verde
├─> Barra de progreso: +1 completada
├─> Tarjeta: borde izquierdo verde
├─> Google Calendar: eliminar evento
└─> Background: gradiente verde sutil

COMPLETADA → PENDIENTE/EN PROGRESO
├─> Color del badge: verde → amarillo/azul
├─> Barra de progreso: -1 completada
├─> Tarjeta: sin borde especial
├─> Google Calendar: sin acción (no se re-agrega)
└─> Background: normal
```

---

## 🗓️ Integración con Google Calendar

### Configuración Requerida

**Archivo de credenciales**: `storage/app/google-calendar/credentials.json`

```json
{
  "type": "service_account",
  "project_id": "tu-proyecto-id",
  "private_key_id": "...",
  "private_key": "-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n",
  "client_email": "tu-email@proyecto.iam.gserviceaccount.com",
  "client_id": "...",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token"
}
```

### Flujo de Eliminación

```php
private function eliminarDeGoogleCalendar(SubTarea $subtarea)
{
    try {
        // 1. Configurar cliente
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/google-calendar/credentials.json'));
        $client->addScope(Google_Service_Calendar::CALENDAR);
        
        // 2. Autenticar
        $accessToken = $this->obtenerAccessToken($client);
        $client->setAccessToken($accessToken);
        
        // 3. Buscar eventos
        $service = new Google_Service_Calendar($client);
        $optParams = [
            'q' => $subtarea->nombre,
            'timeMin' => Carbon::parse($subtarea->fecha_inicio)->toRfc3339String(),
            'timeMax' => Carbon::parse($subtarea->fecha_limite)->toRfc3339String(),
        ];
        
        $events = $service->events->listEvents('primary', $optParams);
        
        // 4. Eliminar eventos coincidentes
        foreach ($events->getItems() as $event) {
            if ($this->eventoCoincide($event, $subtarea)) {
                $service->events->delete('primary', $event->getId());
                \Log::info("Evento eliminado de Google Calendar: {$event->getSummary()}");
            }
        }
        
    } catch (\Exception $e) {
        \Log::error('Error al eliminar de Google Calendar: ' . $e->getMessage());
        // No se lanza excepción para no bloquear la actualización de estado
    }
}
```

### Criterios de Coincidencia

```php
private function eventoCoincide($event, $subtarea): bool
{
    // 1. Nombre debe coincidir exactamente
    if ($event->getSummary() !== $subtarea->nombre) {
        return false;
    }
    
    // 2. Fecha de inicio debe estar en el rango
    $eventStart = Carbon::parse($event->getStart()->getDateTime());
    $tareaInicio = Carbon::parse($subtarea->fecha_inicio);
    
    if (!$eventStart->isSameDay($tareaInicio)) {
        return false;
    }
    
    return true;
}
```

### Logs de Google Calendar

Los eventos de Google Calendar se registran en:

**Archivo**: `storage/logs/laravel.log`

```
[2024-12-01 10:30:15] local.INFO: Evento eliminado de Google Calendar: Crear mockups
[2024-12-01 10:30:16] local.ERROR: Error al eliminar de Google Calendar: Invalid credentials
```

---

## 🎨 Diseño y Estilos

### Paleta de Colores por Estado

```css
/* Pendiente - Amarillo/Naranja */
.estado-pendiente {
    background: #fffbeb;     /* Fondo crema suave */
    color: #d97706;          /* Texto naranja oscuro */
    border-color: #fed7aa;   /* Borde naranja claro */
}

/* En progreso - Azul */
.estado-enprogreso {
    background: #eff6ff;     /* Fondo azul muy claro */
    color: #2563eb;          /* Texto azul */
    border-color: #bfdbfe;   /* Borde azul claro */
}

/* Completada - Verde */
.estado-completada {
    background: #f0fdf4;     /* Fondo verde muy claro */
    color: #16a34a;          /* Texto verde */
    border-color: #bbf7d0;   /* Borde verde claro */
}
```

### Animaciones de Transición

```css
/* Fade In (aparición) */
@keyframes tareas-fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in {
    animation: tareas-fadeIn 0.3s ease-out;
}

/* Fade Out (desaparición) */
.fade-out {
    opacity: 0;
    transform: scale(0.95);
    pointer-events: none;
    transition: all 0.25s cubic-bezier(0.4, 2, 0.6, 1);
}
```

### Estilos de Botones

```css
/* Botón principal (Completar / Aplicar cambios) */
.btn-etapa {
    background: #fbbf24;      /* Amarillo/naranja */
    color: #22223b;           /* Texto oscuro */
    padding: 0.5rem 1.2rem;
    border-radius: 0.5rem;
    font-weight: 600;
    min-width: 160px;
    transition: all 0.25s;
}

/* Botón confirmar (verde) */
.btn-etapa.confirmar {
    background: #10b981;      /* Verde */
    color: #fff;
    min-width: 140px;
}

.btn-etapa.confirmar:hover {
    background: #059669;      /* Verde más oscuro */
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
}

/* Botón cancelar (gris) */
.btn-etapa.cancelar {
    background: #f3f4f6;      /* Gris claro */
    color: #374151;           /* Texto gris oscuro */
    min-width: 110px;
}

/* Botón deshabilitado */
.btn-etapa:disabled {
    background: #e5e7eb;
    color: #b0b0b0;
    cursor: not-allowed;
    opacity: 0.7;
}
```

### Responsividad

```css
/* Móviles (< 480px) */
@media (max-width: 480px) {
    .etapa-actions {
        flex-direction: column;
        align-items: stretch;
        gap: var(--space-2);
    }
    
    .btn-etapa {
        width: 100%;
        min-width: unset;
    }
}

/* Tablets (480px - 768px) */
@media (max-width: 768px) {
    .etapa-footer {
        flex-direction: column;
        gap: var(--space-2);
    }
    
    .progress-bar-custom {
        width: 80px;
        height: 6px;
    }
}
```

---

## 💼 Casos de Uso

### Caso 1: Completar una Tarea Individual

**Escenario**: El usuario termina de diseñar los mockups y quiere marcar la tarea como completada.

**Pasos**:
1. Ubicar la tarea "Crear mockups" en la etapa "Diseño"
2. Hacer click en el select de estado (actualmente "En progreso")
3. Seleccionar "Completada" del menú desplegable
4. Observar que el botón del footer cambia a "✓ Completar (1)"
5. Hacer click en "✓ Completar (1)"
6. Hacer click en "¿Confirmar cambios (1)?"
7. Esperar a que se procese (spinner visible)
8. La página se recarga automáticamente
9. La tarea ahora aparece con borde verde y badge verde "Completada"
10. La barra de progreso de la etapa aumentó
11. El evento fue eliminado de Google Calendar

### Caso 2: Cambiar Múltiples Tareas a Diferentes Estados

**Escenario**: El usuario quiere actualizar varias tareas con estados mixtos.

**Pasos**:
1. Cambiar "Tarea A" de "Pendiente" → "En progreso"
2. Cambiar "Tarea B" de "En progreso" → "Completada"
3. Cambiar "Tarea C" de "Pendiente" → "Completada"
4. El botón muestra "Aplicar cambios (3)"
5. Hacer click en "Aplicar cambios (3)"
6. Hacer click en "¿Confirmar cambios (3)?"
7. Se ejecutan 3 peticiones PATCH simultáneamente
8. La página se recarga
9. Todas las tareas se actualizaron correctamente
10. Solo las tareas marcadas como "Completada" se eliminaron del calendario

### Caso 3: Cancelar Cambios Pendientes

**Escenario**: El usuario cambia de opinión antes de guardar.

**Pasos**:
1. Cambiar "Tarea D" de "Pendiente" → "Completada"
2. El botón muestra "✓ Completar (1)"
3. Hacer click en "✓ Completar (1)"
4. Aparecen botones "x Cancelar" y "¿Confirmar cambios (1)?"
5. Hacer click en "x Cancelar"
6. Los botones desaparecen con animación
7. Reaparece el botón original "✓ Completar (0)" deshabilitado
8. El select todavía muestra "Completada" pero NO se guardó
9. Usuario puede volver a cambiar el select a "Pendiente" manualmente

### Caso 4: Manejar Error de Red

**Escenario**: La conexión falla durante la actualización.

**Pasos**:
1. Usuario cambia estado y confirma
2. Petición PATCH falla (error 500 o timeout)
3. La tarjeta muestra animación de error (shake + borde rojo)
4. Después de 2 segundos, se remueve el indicador de error
5. La página NO se recarga automáticamente en este caso
6. El estado NO se guardó en la base de datos
7. Usuario puede intentar nuevamente

---

## 🛠️ Solución de Problemas

### Problema 1: Los cambios no se guardan

**Síntomas**: El select cambia pero al recargar vuelve al estado original.

**Causas posibles**:
- ❌ Endpoint PATCH `/tareas/{id}/update-status` no está configurado en rutas
- ❌ Token CSRF inválido o expirado
- ❌ Permisos de base de datos insuficientes
- ❌ Validación falla en el backend

**Solución**:
```bash
# 1. Verificar rutas
php artisan route:list | grep update-status

# 2. Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# 3. Verificar logs
tail -f storage/logs/laravel.log

# 4. Probar endpoint manualmente
curl -X PATCH http://localhost/tareas/1/update-status \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: tu-token" \
  -d '{"estado": "Completada"}'
```

### Problema 2: Botón "Aplicar cambios" no aparece

**Síntomas**: Cambias el select pero el botón permanece deshabilitado.

**Causas posibles**:
- ❌ Atributo `data-current-state` no está presente en el select
- ❌ Función `actualizarBotones()` no se ejecuta
- ❌ Event listener de 'change' no está configurado

**Solución**:
```javascript
// Verificar en consola del navegador
document.querySelectorAll('.estado-badge').forEach(sel => {
    console.log('Select ID:', sel.dataset.id);
    console.log('Current state:', sel.dataset.currentState);
    console.log('Current value:', sel.value);
});

// Verificar event listeners
const sel = document.querySelector('.estado-badge');
console.log(getEventListeners(sel)); // Chrome DevTools
```

### Problema 3: Spinner se queda girando indefinidamente

**Síntomas**: La clase `.updating` no se remueve de la tarjeta.

**Causas posibles**:
- ❌ Petición PATCH no retorna respuesta
- ❌ Promise no se resuelve correctamente
- ❌ Bloque `.finally()` no se ejecuta

**Solución**:
```javascript
// Agregar timeout a la petición
function actualizarEstadoTarea(subtareaId, nuevoEstado) {
    const taskCard = document.querySelector(`[data-task-id="${subtareaId}"]`);
    if (taskCard) taskCard.classList.add('updating');

    // Timeout de 10 segundos
    const timeoutPromise = new Promise((_, reject) => 
        setTimeout(() => reject(new Error('Timeout')), 10000)
    );

    const fetchPromise = fetch(`/tareas/${subtareaId}/update-status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ estado: nuevoEstado })
    }).then(response => response.json());

    return Promise.race([fetchPromise, timeoutPromise])
        .catch(error => {
            console.error('Error:', error);
            return { success: false, error: error.message };
        })
        .finally(() => {
            if (taskCard) taskCard.classList.remove('updating');
        });
}
```

### Problema 4: Google Calendar no se actualiza

**Síntomas**: La tarea se marca como completada pero sigue en el calendario.

**Causas posibles**:
- ❌ Archivo `credentials.json` no existe o está mal configurado
- ❌ Permisos de Google Calendar API insuficientes
- ❌ Nombre de tarea no coincide exactamente
- ❌ Token de acceso expirado

**Solución**:
```bash
# 1. Verificar que el archivo existe
ls -la storage/app/google-calendar/credentials.json

# 2. Verificar logs
tail -f storage/logs/laravel.log | grep "Google Calendar"

# 3. Probar conexión
php artisan tinker
>>> $client = new Google_Client();
>>> $client->setAuthConfig(storage_path('app/google-calendar/credentials.json'));
>>> $client->addScope(Google_Service_Calendar::CALENDAR);
>>> // Si no hay error, la configuración es correcta
```

### Problema 5: Barra de progreso no se actualiza

**Síntomas**: Completas tareas pero el porcentaje no cambia.

**Causas posibles**:
- ❌ Función `recalcularProgreso()` no se llama después de actualizar
- ❌ Clase `.completed` no se agrega a la tarjeta
- ❌ Selectores CSS incorrectos

**Solución**:
```javascript
// Forzar recálculo manual en consola
function recalcularProgreso() {
    document.querySelectorAll('.etapa-card').forEach(etapaCard => {
        const totalTasks = etapaCard.querySelectorAll('.task-mini-card').length;
        const completedTasks = etapaCard.querySelectorAll('.task-mini-card.completed').length;
        const progreso = totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0;

        console.log(`Etapa: ${totalTasks} tareas, ${completedTasks} completadas, ${progreso}% progreso`);

        const progressFill = etapaCard.querySelector('.progress-fill');
        const progressText = etapaCard.querySelector('.progress-text');
        
        if (progressFill) {
            progressFill.style.width = `${progreso}%`;
            console.log('Progress bar actualizada');
        }
        if (progressText) {
            progressText.textContent = `${progreso}%`;
            console.log('Progress text actualizado');
        }
    });
}

// Ejecutar
recalcularProgreso();
```

---

## ✅ Mejores Prácticas

### 1. Siempre Confirmar Antes de Guardar

El sistema de confirmación en dos pasos previene cambios accidentales:
- ✅ Permite revisar los cambios antes de aplicarlos
- ✅ Da la opción de cancelar sin afectar la base de datos
- ✅ Agrupa múltiples cambios en una sola operación

### 2. Aprovechar el Cambio en Lote

En lugar de cambiar y confirmar una tarea a la vez:
- ✅ Cambia múltiples selects de estados
- ✅ Confirma todos los cambios de una vez
- ✅ Reduce la cantidad de recargas de página
- ✅ Ahorra tiempo en proyectos con muchas tareas

### 3. Revisar Google Calendar Periódicamente

Aunque la eliminación es automática:
- ✅ Verifica que los eventos se eliminaron correctamente
- ✅ Revisa los logs si encuentras eventos duplicados
- ✅ Sincroniza manualmente si detectas inconsistencias

### 4. Usar Estados Intermedios

No saltes directamente de "Pendiente" a "Completada":
- ✅ Usa "En progreso" mientras trabajas en la tarea
- ✅ Da visibilidad al equipo sobre qué estás haciendo
- ✅ Facilita el seguimiento del progreso real

### 5. Monitorear el Progreso de las Etapas

La barra de progreso es una herramienta visual poderosa:
- ✅ Observa qué etapas están atrasadas
- ✅ Identifica cuellos de botella
- ✅ Planifica mejor la distribución de tareas

### 6. Mantener la Consistencia de Nombres

Para que Google Calendar funcione correctamente:
- ✅ No cambies el nombre de la tarea después de crearla
- ✅ Usa nombres descriptivos y únicos
- ✅ Evita caracteres especiales que puedan causar problemas

---

## 🔐 Seguridad

### Validación Backend

```php
// app/Http/Controllers/TaskController.php
public function updateStatus(Request $request, $id)
{
    // 1. Validar entrada
    $validated = $request->validate([
        'estado' => 'required|string|in:Pendiente,En progreso,Completada'
    ]);

    // 2. Verificar autorización
    $subtarea = SubTarea::findOrFail($id);
    if ($subtarea->user_id !== auth()->id()) {
        abort(403, 'No autorizado');
    }

    // 3. Actualizar con datos validados
    $subtarea->estado = $validated['estado'];
    $subtarea->save();

    return response()->json(['success' => true]);
}
```

### Protección CSRF

Todas las peticiones incluyen el token CSRF:

```javascript
fetch(`/tareas/${subtareaId}/update-status`, {
    method: 'PATCH',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Token de Laravel
    },
    body: JSON.stringify({ estado: nuevoEstado })
})
```

### Sanitización de Datos

Los valores del select están restringidos:

```html
<select class="estado-badge">
    <option value="Pendiente">Pendiente</option>
    <option value="En progreso">En progreso</option>
    <option value="Completada">Completada</option>
    <!-- Solo estos 3 valores son válidos -->
</select>
```

---

## 📊 Métricas y Análisis

### Datos que se Pueden Extraer

```sql
-- 1. Tareas completadas por etapa
SELECT 
    tg.nombre AS etapa,
    COUNT(*) AS total_tareas,
    SUM(CASE WHEN st.estado = 'Completada' THEN 1 ELSE 0 END) AS completadas,
    ROUND(SUM(CASE WHEN st.estado = 'Completada' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) AS porcentaje
FROM tareas_generales tg
LEFT JOIN sub_tareas st ON st.tarea_general_id = tg.id
WHERE st.archivada = FALSE
GROUP BY tg.id, tg.nombre
ORDER BY porcentaje DESC;

-- 2. Tiempo promedio para completar tareas
SELECT 
    AVG(DATEDIFF(updated_at, created_at)) AS dias_promedio
FROM sub_tareas
WHERE estado = 'Completada' 
AND updated_at > created_at;

-- 3. Tareas por estado
SELECT 
    estado,
    COUNT(*) AS cantidad,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM sub_tareas WHERE archivada = FALSE), 2) AS porcentaje
FROM sub_tareas
WHERE archivada = FALSE
GROUP BY estado;
```

---

## 📝 Changelog

### Versión 1.0 (Actual)
- ✅ Sistema de cambio de estado con confirmación en dos pasos
- ✅ Actualización en lote de múltiples tareas
- ✅ Integración con Google Calendar (eliminación automática)
- ✅ Barra de progreso en tiempo real
- ✅ Feedback visual con animaciones
- ✅ Responsivo en todos los dispositivos

### Versión 0.9 (Beta)
- Cambio individual de estados sin confirmación
- Recarga completa de página después de cada cambio
- Sin integración con Google Calendar

---

## 🚀 Roadmap Futuro

### Funcionalidades Planificadas

1. **Arrastrar y soltar** (Drag & Drop)
   - Cambiar estado arrastrando la tarjeta
   - Reordenar tareas dentro de la etapa

2. **Sincronización bidireccional con Google Calendar**
   - No solo eliminar, sino también actualizar eventos
   - Cambios en el calendario se reflejan en la app

3. **Historial de cambios**
   - Ver quién cambió el estado y cuándo
   - Revertir cambios accidentales

4. **Notificaciones en tiempo real**
   - Avisos cuando un compañero cambia el estado
   - Actualización automática sin recargar

5. **Estadísticas avanzadas**
   - Gráficos de progreso por etapa
   - Predicción de fecha de finalización

---

## 📚 Referencias

- [Laravel Documentation - Controllers](https://laravel.com/docs/controllers)
- [Google Calendar API - PHP Client](https://developers.google.com/calendar/api/quickstart/php)
- [MDN - Fetch API](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API)
- [CSS Animations](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_Animations)

---

**Documentación creada**: Diciembre 2024  
**Última actualización**: Diciembre 2024  
**Versión**: 1.0
