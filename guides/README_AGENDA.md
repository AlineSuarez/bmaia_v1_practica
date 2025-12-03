# Vista Agenda - Guía Completa

## 📋 Descripción General

La **Vista Agenda** es una interfaz de calendario interactivo que permite visualizar, gestionar y sincronizar tareas con Google Calendar. Combina un calendario mensual visual con una lista dinámica de tareas del día seleccionado.

---

## 🎯 Características Principales

### 1. **Calendario Mensual Interactivo**
- Visualización clara de días con tareas programadas
- Navegación entre meses (anterior/siguiente)
- Selector rápido de meses (dropdown)
- Indicador visual del día actual
- Badge numérico mostrando cantidad de tareas por día
- Badge verde cuando todas las tareas del día están completadas

### 2. **Lista de Tareas Diaria**
- Visualización de tareas filtradas por fecha seleccionada
- Información completa de cada tarea:
  - Nombre de la tarea
  - Fecha de inicio
  - Fecha límite
  - Prioridad (barra de color)
  - Estado actual (con ícono)

### 3. **Gestión de Estados**
- Cambio rápido de estado mediante popover interactivo
- Tres estados disponibles:
  - **Pendiente** (gris)
  - **En progreso** (azul)
  - **Completada** (verde)
- Actualización automática en tiempo real
- Sincronización con Google Calendar al cambiar estado

### 4. **Integración con Google Calendar**
- Conexión con cuenta de Google
- Sincronización bidireccional de tareas
- Creación automática de eventos en Google Calendar
- Actualización de eventos al cambiar prioridad/estado
- Eliminación de eventos al completar tareas
- Opciones de resincronización y eliminación masiva

---

## 🏗️ Estructura de Archivos

### Vista Principal
**Ubicación**: `resources/views/tareas/agenda.blade.php`

Contiene:
- Estructura HTML del calendario
- Contenedor de lista de tareas
- Modal de progreso de sincronización
- Scripts de inicialización

### Estilos
**Ubicación**: `public/css/components/home-user/tasks/agenda.css`

Define:
- Variables CSS (colores, espaciados, transiciones)
- Estilos del calendario mensual
- Estilos de las tarjetas de tareas
- Estilos del sistema de estados (pills + popovers)
- Modales y popups
- Responsive design

### Lógica JavaScript
**Ubicación**: `public/js/components/home-user/tasks/agenda.js`

Implementa:
- Renderizado dinámico del calendario
- Gestión de navegación entre meses
- Filtrado de tareas por fecha
- Sistema de cambio de estados
- Integración con Google Calendar API
- Sincronización con progreso visual

---

## 📊 Flujo de Funcionamiento

### Inicialización

```
1. Cargar página agenda
   └─> Ejecutar DOMContentLoaded
       ├─> inicializarAgenda()
       │   ├─> Seleccionar día actual
       │   ├─> Mostrar tareas de hoy
       │   └─> Verificar estado Google Calendar
       │
       ├─> configurarEventos()
       │   ├─> Botones navegación meses
       │   ├─> Dropdown selector meses
       │   └─> Click en días del calendario
       │
       └─> renderCalendario()
           ├─> Calcular días del mes
           ├─> Renderizar grid 7x6
           └─> Marcar días con tareas
```

### Visualización de Tareas

```
Usuario selecciona un día
   └─> Click en elemento .day
       └─> mostrarTareasDelDia(fecha)
           ├─> Filtrar tareas por fecha
           ├─> Actualizar subtítulo
           └─> Renderizar lista de tareas
               └─> crearElementoTarea(tarea)
                   ├─> Asignar color de prioridad
                   ├─> Asignar ícono de estado
                   ├─> Crear pill de estado
                   └─> Configurar popover
```

### Cambio de Estado

```
Usuario click en pill de estado
   └─> Abrir popover con opciones
       └─> Usuario selecciona nuevo estado
           ├─> Enviar petición AJAX al servidor
           │   POST /subtareas/{id}/actualizar-estado
           │   Body: { estado: "Completada" }
           │
           ├─> Servidor actualiza base de datos
           │   └─> SubTareasController@actualizarEstado()
           │
           ├─> Actualizar UI inmediatamente
           │   ├─> Cambiar color del pill
           │   ├─> Cambiar ícono
           │   └─> Actualizar texto
           │
           └─> Sincronizar con Google Calendar
               ├─> Si estado = "Completada"
               │   └─> Eliminar evento de GCalendar
               │
               └─> Si estado != "Completada"
                   └─> Actualizar evento en GCalendar
```

---

## 🎨 Sistema de Prioridades

### Colores de Barra

| Prioridad | Color | Clase CSS | Variable |
|-----------|-------|-----------|----------|
| Baja | Azul claro | `.blue` | `lightblue` |
| Media | Verde | `.green` | `#3cc64a` |
| Alta | Amarillo | `.yellow` | `yellow` |
| Urgente | Rojo | `.red` | `red` |

### Mapeo en JavaScript

```javascript
const prioridadColores = {
    'baja': 'blue',
    'media': 'green',
    'alta': 'yellow',
    'urgente': 'red'
};
```

---

## 🔄 Sistema de Estados

### Estados Disponibles

| Estado | Ícono | Clase CSS | Color |
|--------|-------|-----------|-------|
| Pendiente | `fa-hourglass-start` | `.pending` | Gris |
| En progreso | `fa-spinner` | `.in-progress` | Azul |
| Completada | `fa-check-circle` | `.completed` | Verde |

### Componente Status Pill

El pill de estado es un componente interactivo que muestra:
- Ícono del estado actual
- Texto del estado
- Flecha indicadora (caret-down)
- Popover con opciones al hacer click

#### Estructura HTML

```html
<div class="status-pill completed" data-estado="Completada">
    <i class="fa fa-check-circle"></i>
    <span class="status-text">Completada</span>
    <i class="fa fa-caret-down"></i>
    <div class="status-popover hidden">
        <button class="status-option" data-estado="Pendiente">Pendiente</button>
        <button class="status-option" data-estado="En progreso">En progreso</button>
        <button class="status-option" data-estado="Completada">Completada</button>
    </div>
</div>
```

---

## 🔗 Integración con Google Calendar

### Flujo de Conexión

```
1. Usuario click en "Conectar con Google Calendar"
   └─> mostrarPopupConfirmacion()
       └─> Mostrar popup de confirmación
           └─> Usuario confirma
               └─> Redirigir a /google-calendar/connect
                   └─> GoogleCalendarController@redirectToGoogle()
                       └─> OAuth2 Flow de Google
                           ├─> Usuario autoriza
                           └─> Callback a /google-calendar/callback
                               └─> Guardar tokens en BD
                                   └─> Redirigir a agenda con flag
                                       └─> iniciarSincronizacionConProgreso()
```

### Sincronización Automática

**Ubicación**: `app/Console/Commands/ActualizarPrioridadTareas.php`

Cuando el scheduler actualiza prioridades automáticamente:

```php
// Si la tarea está completada
if (in_array($tarea->estado, ['Completada', 'Completado'])) {
    // Eliminar de Google Calendar
    $this->eliminarDeGoogleCalendar($tarea);
}

// Si cambia la prioridad
if ($resultado['actualizado']) {
    // Sincronizar cambio con Google Calendar
    $this->sincronizarConGoogleCalendar($tarea);
}
```

### Mapeo de Colores en Google Calendar

| Prioridad | Color ID | Color Visual |
|-----------|----------|--------------|
| Baja | 7 | Turquesa |
| Media | 2 | Verde claro |
| Alta | 5 | Amarillo |
| Urgente | 11 | Rojo |

---

## 🗓️ Renderizado del Calendario

### Estructura del Grid

El calendario se renderiza como un grid de 7 columnas × 6 filas (42 días):

```
┌─────────────────────────────────────────────┐
│  Lu  Ma  Mi  Ju  Vi  Sa  Do                 │
├─────────────────────────────────────────────┤
│  28  29  30  31  01  02  03  ← Días del mes│
│  04  05  06  07  08  09  10     anterior   │
│  11  12  13  14  15  16  17                 │
│  18  19  20  21  22  23  24                 │
│  25  26  27  28  29  30  01  ← Días del mes│
│  02  03  04  05  06  07  08     siguiente  │
└─────────────────────────────────────────────┘
```

### Lógica de Renderizado

```javascript
function renderCalendario() {
    // 1. Actualizar título del mes
    monthYear.textContent = `${nombresMeses[currentMonth]} ${currentYear}`;
    
    // 2. Calcular primer día y días totales del mes
    const primerDia = new Date(currentYear, currentMonth, 1);
    const ultimoDia = new Date(currentYear, currentMonth + 1, 0);
    const diasEnMes = ultimoDia.getDate();
    
    // 3. Ajustar para que Lunes = 0
    let primerDiaSemana = primerDia.getDay();
    primerDiaSemana = primerDiaSemana === 0 ? 6 : primerDiaSemana - 1;
    
    // 4. Renderizar días del mes anterior (grises)
    // 5. Renderizar días del mes actual (normales)
    // 6. Renderizar días del mes siguiente (grises)
    // 7. Marcar día actual (.today)
    // 8. Marcar día seleccionado (.selected)
    // 9. Agregar badges de cantidad de tareas
}
```

### Elemento de Día

Cada día tiene:
- Clase base: `.day`
- Clases adicionales:
  - `.empty` - Días de otros meses
  - `.today` - Día actual
  - `.selected` - Día seleccionado
  - `.has-tasks` - Tiene tareas programadas
  - `.completed-tasks` - Todas las tareas completadas

```html
<div class="day today selected has-tasks" data-tasks="3">
    <span class="day-number">15</span>
</div>
```

El badge se genera con CSS `::after`:
```css
.day.has-tasks::after {
    content: attr(data-tasks);
    /* Círculo naranja con número */
}
```

---

## 📡 Endpoints API Utilizados

### Backend Laravel

| Método | Ruta | Controlador | Descripción |
|--------|------|-------------|-------------|
| GET | `/tareas/agenda` | `TareasController@agenda` | Renderiza vista con tareas |
| POST | `/subtareas/{id}/actualizar-estado` | `SubTareasController@actualizarEstado` | Actualiza estado de tarea |
| GET | `/google-calendar/status` | `GoogleCalendarController@getStatus` | Verifica si está conectado |
| GET | `/google-calendar/connect` | `GoogleCalendarController@redirectToGoogle` | Inicia OAuth2 |
| GET | `/google-calendar/callback` | `GoogleCalendarController@handleCallback` | Procesa callback OAuth2 |
| POST | `/google-calendar/sync` | `GoogleCalendarController@syncTasks` | Sincroniza tareas |
| DELETE | `/google-calendar/delete-tasks` | `GoogleCalendarController@deleteTasks` | Elimina tareas del calendario |

---

## 🎭 Interacciones del Usuario

### Navegación del Calendario

**1. Cambiar Mes (Botones):**
```javascript
// Mes anterior
document.getElementById('prevMonth').addEventListener('click', () => {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    renderCalendario();
});
```

**2. Selector Rápido de Meses:**
```javascript
// Click en icono de calendario
navegadorMeses.addEventListener('click', (e) => {
    e.stopPropagation();
    opcionesMeses.classList.toggle('hidden');
});

// Seleccionar mes del dropdown
document.querySelectorAll('.mes').forEach((mesElement, index) => {
    mesElement.addEventListener('click', () => {
        currentMonth = index;
        renderCalendario();
        opcionesMeses.classList.add('hidden');
    });
});
```

### Selección de Día

```javascript
// Click en día del calendario
dayElement.addEventListener('click', () => {
    // Actualizar selectedDate
    selectedDate = new Date(currentYear, currentMonth, dia);
    
    // Re-renderizar calendario para actualizar .selected
    renderCalendario();
    
    // Mostrar tareas del día
    mostrarTareasDelDia(selectedDate);
});
```

### Cambio de Estado de Tarea

```javascript
// 1. Usuario click en pill
pill.addEventListener('click', (e) => {
    // Abrir popover
    popover.classList.remove('hidden');
});

// 2. Usuario selecciona opción
statusOption.addEventListener('click', async (e) => {
    const nuevoEstado = e.target.dataset.estado;
    
    // 3. Enviar petición AJAX
    const response = await fetch(`/subtareas/${tareaId}/actualizar-estado`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ estado: nuevoEstado })
    });
    
    // 4. Actualizar UI
    aplicarCambioVisual(pill, nuevoEstado);
    
    // 5. Cerrar popover
    popover.classList.add('hidden');
});
```

---

## 🔒 Seguridad

### Protección CSRF

Todas las peticiones POST incluyen el token CSRF:

```javascript
// Token disponible globalmente
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Incluido en headers de fetch
headers: {
    'X-CSRF-TOKEN': csrfToken
}
```

### Validación de Datos

En el controlador:
```php
public function actualizarEstado(Request $request, $id)
{
    $request->validate([
        'estado' => 'required|in:Pendiente,En progreso,Completada'
    ]);
    
    // Verificar pertenencia de la tarea al usuario
    $subtarea = SubTarea::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();
        
    // Actualizar...
}
```

---

## 🎨 Personalización Visual

### Variables CSS

Todas las variables están definidas en `:root`:

```css
:root {
    /* Colores principales */
    --primary-color: #f59e0b;
    --primary-dark: #d97706;
    --success-color: #10b981;
    --error-color: #ef4444;
    
    /* Espaciado */
    --spacing-xs: 0.25rem;
    --spacing-sm: 0.5rem;
    --spacing-md: 1rem;
    --spacing-lg: 1.5rem;
    
    /* Tipografía */
    --font-size-base: 1rem;
    --font-size-lg: 1.125rem;
    
    /* Transiciones */
    --transition-fast: 0.15s ease-in-out;
    --transition-normal: 0.3s ease-in-out;
}
```

### Personalizar Colores de Prioridad

En `agenda.js`:
```javascript
const prioridadColores = {
    'baja': 'blue',      // Cambiar a otro color
    'media': 'green',
    'alta': 'yellow',
    'urgente': 'red'
};
```

En `agenda.css`:
```css
.priority-bar.blue { background-color: lightblue; }
.priority-bar.green { background-color: #3cc64a; }
.priority-bar.yellow { background-color: yellow; }
.priority-bar.red { background-color: red; }
```

---

## 🐛 Solución de Problemas

### El calendario no se renderiza

**Verificar:**
1. Que `window.tareasData` esté disponible
2. Que el DOM esté completamente cargado
3. Consola del navegador para errores JavaScript

```javascript
console.log('Tareas cargadas:', window.tareasData);
```

### Las tareas no se muestran al seleccionar día

**Verificar:**
1. Formato de fechas en la base de datos
2. Zona horaria del servidor
3. Función `obtenerTareasDelDia()`:

```javascript
function obtenerTareasDelDia(fecha) {
    console.log('Buscando tareas para:', fecha);
    const tareas = tareasAgenda.filter(tarea => {
        const fechaInicio = new Date(tarea.fecha_inicio);
        console.log('Comparando:', fechaInicio, 'con', fecha);
        return (
            fechaInicio.getFullYear() === fecha.getFullYear() &&
            fechaInicio.getMonth() === fecha.getMonth() &&
            fechaInicio.getDate() === fecha.getDate()
        );
    });
    console.log('Tareas encontradas:', tareas.length);
    return tareas;
}
```

### El cambio de estado no funciona

**Verificar:**
1. Token CSRF presente en la página:
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

2. Ruta correcta en `web.php`:
```php
Route::post('/subtareas/{id}/actualizar-estado', [SubTareasController::class, 'actualizarEstado'])
    ->name('subtareas.actualizar-estado');
```

3. Método en el controlador existe y es accesible

### Google Calendar no sincroniza

**Verificar:**
1. Credenciales de Google en `.env`:
```env
GOOGLE_CLIENT_ID=tu_client_id
GOOGLE_CLIENT_SECRET=tu_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/google-calendar/callback
```

2. Tokens guardados en base de datos:
```sql
SELECT google_calendar_token, google_calendar_refresh_token 
FROM users 
WHERE id = YOUR_USER_ID;
```

3. Logs del servidor:
```bash
tail -f storage/logs/laravel.log
```

---

## 📝 Notas Importantes

### Manejo de Fechas

- **JavaScript:** Usa objetos `Date` nativos en hora local
- **Backend:** Almacena en formato `YYYY-MM-DD` en MySQL
- **Comparación:** Siempre comparar año, mes y día por separado para evitar problemas de zona horaria

```javascript
// ✅ Correcto
fecha1.getFullYear() === fecha2.getFullYear() &&
fecha1.getMonth() === fecha2.getMonth() &&
fecha1.getDate() === fecha2.getDate()

// ❌ Incorrecto (puede fallar por horas)
fecha1.toISOString() === fecha2.toISOString()
```

### Actualización de UI

Cuando se actualiza el estado de una tarea:
1. **Primero** actualizar UI (feedback inmediato)
2. **Luego** enviar petición al servidor
3. **Si falla**, revertir cambios en UI

```javascript
// Guardar estado anterior
const estadoAnterior = pill.dataset.estado;

// Actualizar UI optimistamente
aplicarCambioVisual(pill, nuevoEstado);

try {
    // Enviar al servidor
    await fetch(...);
} catch (error) {
    // Revertir si falla
    aplicarCambioVisual(pill, estadoAnterior);
    alert('Error al actualizar estado');
}
```

### Performance

Para proyectos con muchas tareas:
- Considerar paginación
- Implementar carga lazy de tareas
- Cachear renderizado de calendario
- Usar `requestAnimationFrame` para animaciones

---

## 🔗 Archivos Relacionados

- **Vista**: `resources/views/tareas/agenda.blade.php`
- **Estilos**: `public/css/components/home-user/tasks/agenda.css`
- **JavaScript**: `public/js/components/home-user/tasks/agenda.js`
- **Controlador Principal**: `app/Http/Controllers/TareasController.php`
- **Controlador Estados**: `app/Http/Controllers/SubTareasController.php`
- **Controlador Google**: `app/Http/Controllers/GoogleCalendarController.php`
- **Modelo**: `app/Models/SubTarea.php`
- **Rutas**: `routes/web.php`
- **Command Scheduler**: `app/Console/Commands/ActualizarPrioridadTareas.php`

---

## 📞 Soporte

Para más información sobre otros componentes del sistema:
- **Sistema de Prioridades**: `README_PRIORIDADES_AUTOMATICAS.md`
- **Scheduler de Tareas**: `SCHEDULER_TAREAS.md`
- **Google Calendar Setup**: `GOOGLE_CALENDAR_SETUP.md`

---

**Estado**: Sistema completamente funcional y documentado  
**Fecha**: Diciembre 2025
**Ultima Modificación**: Diciembre 2025
