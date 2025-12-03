# Botón Editar - Guía Completa

## 📋 Descripción General

El **Botón Editar** permite modificar los nombres de todas las tareas de manera masiva en la Vista Lista. Es una funcionalidad diseñada para agilizar la edición de nombres de múltiples tareas simultáneamente, evitando tener que editar cada tarea individualmente.

---

## 🎯 Características Principales

### 1. **Edición Masiva de Nombres**
- Activar modo edición con un solo clic
- Convertir todos los nombres de tareas en campos editables
- Editar múltiples nombres simultáneamente
- Validación de campos vacíos

### 2. **Interfaz Intuitiva**
- Cambio visual del botón (icono y texto)
- Campos de entrada destacados visualmente
- Focus automático en el primer campo
- Feedback visual de errores

### 3. **Guardado Inteligente**
- Validación antes de guardar
- Solo envía las tareas modificadas al servidor
- Muestra errores específicos por tarea
- Recarga automática después de guardar

### 4. **Seguridad y Validación**
- Protección CSRF
- Validación de campos vacíos
- Manejo de errores del servidor
- Rollback visual en caso de error

---

## 🏗️ Estructura de Archivos

### Vista Principal
**Ubicación**: `resources/views/tareas/index.blade.php`

Contiene el botón de editar:
```html
<button id="btn-editar-nombre" 
        class="btn-miel" 
        type="button" 
        title="Editar nombre de las tareas">
    <i class="fa fa-edit"></i>
    <span>Editar</span>
</button>
```

### Vista Lista
**Ubicación**: `resources/views/tareas/list.blade.php`

Contiene la tabla con los nombres de tareas:
```html
<span class="task-name">{{ $subtarea->nombre }}</span>
```

### Lógica JavaScript
**Ubicación**: `public/js/components/home-user/tasks/list.js`

Función principal: `configurarEditarNombresGlobal()`

### Controlador Backend
**Ubicación**: `app/Http/Controllers/TareasController.php`

Método: `update($id, Request $request)`

### Configuración de Rutas
**Ubicación**: `routes/web.php`

```php
Route::post('/tareas/update/{id}', 
    [TareasController::class, 'update'])
    ->name('tareas.update')
    ->middleware('auth');
```

---

## 📊 Flujo de Funcionamiento

### Flujo Completo de Edición

```
1. Usuario hace clic en botón "Editar"
   └─> Activar modo edición
       ├─> Cambiar ícono: fa-edit → fa-save
       ├─> Cambiar texto: "Editar" → "Guardar"
       └─> Convertir spans a inputs
           ├─> Obtener nombre actual
           ├─> Guardar nombre original en dataset
           ├─> Crear input con valor actual
           ├─> Reemplazar span por input
           └─> Focus en primer input

2. Usuario edita nombres de tareas
   └─> Modificar valores en inputs
       └─> (sin validación en tiempo real)

3. Usuario hace clic en botón "Guardar"
   └─> Validar todos los inputs
       ├─> ¿Hay campos vacíos?
       │   ├─> Sí: Mostrar error y detener
       │   └─> No: Continuar
       │
       └─> Detectar cambios
           ├─> Comparar valor actual vs original
           ├─> Crear array de cambios
           │   └─> { id, nombre, row, input }
           │
           └─> ¿Hay cambios?
               ├─> No: Revertir a modo normal
               └─> Sí: Enviar al servidor
                   └─> Para cada cambio:
                       ├─> POST /tareas/update/{id}
                       ├─> Body: { nombre: nuevoNombre }
                       └─> Esperar respuesta
                           ├─> Éxito: Marcar como actualizado
                           └─> Error: Marcar input con borde rojo

4. Procesar resultados
   └─> ¿Todos exitosos?
       ├─> Sí:
       │   ├─> Mostrar notificación de éxito
       │   └─> Recargar página después de 1 segundo
       │
       └─> No:
           ├─> Mostrar notificación de error
           ├─> Mantener modo edición
           ├─> Resaltar campos con error
           └─> Permitir reintentar
```

### Estados del Botón

| Estado | Ícono | Texto | Clase CSS | Acción al Click |
|--------|-------|-------|-----------|-----------------|
| **Normal** | `fa-edit` | "Editar" | - | Activar modo edición |
| **Edición** | `fa-save` | "Guardar" | `.activo` | Guardar cambios |
| **Guardando** | (disabled) | "Guardar" | `.activo` | (bloqueado) |

---

## 💻 Implementación JavaScript

### Código Principal

```javascript
// Ubicación: public/js/components/home-user/tasks/list.js

function configurarEditarNombresGlobal() {
    const btn = document.getElementById("btn-editar-nombre");
    if (!btn) return;

    const icon = btn.querySelector("i");
    const spanText = btn.querySelector("span");
    let editMode = false;

    btn.addEventListener("click", async (e) => {
        e.preventDefault();

        const rows = Array.from(document.querySelectorAll(".task-row"));
        if (rows.length === 0) return;

        // MODO 1: Activar edición
        if (!editMode) {
            activarModoEdicion();
            return;
        }

        // MODO 2: Guardar cambios
        await guardarCambios();
    });

    // ===== FUNCIONES AUXILIARES =====

    function activarModoEdicion() {
        editMode = true;
        btn.classList.add("activo");
        
        // Cambiar apariencia del botón
        if (icon && icon.classList.contains("fa-edit")) {
            icon.classList.replace("fa-edit", "fa-save");
        }
        if (spanText) {
            spanText.textContent = " Guardar";
        }

        // Convertir spans a inputs
        rows.forEach((row) => {
            const nameSpan = row.querySelector(".task-name");
            if (!nameSpan) return;

            const original = nameSpan.textContent.trim();
            row.dataset.originalName = original;

            const input = document.createElement("input");
            input.type = "text";
            input.className = "task-name-input input-miel";
            input.value = original;
            input.setAttribute("data-task-id", 
                row.getAttribute("data-task-id"));
            input.style.minWidth = "180px";
            input.autocomplete = "off";

            nameSpan.parentNode.replaceChild(input, nameSpan);
        });

        // Focus en primer input
        const firstInput = document.querySelector(".task-name-input");
        if (firstInput) firstInput.focus();
    }

    async function guardarCambios() {
        btn.disabled = true;

        // Validar campos vacíos
        const inputs = Array.from(
            document.querySelectorAll(".task-name-input")
        );
        
        for (const input of inputs) {
            const val = input.value.trim();
            if (val === "") {
                mostrarNotificacion("warning", 
                    "El nombre no puede quedar vacío");
                input.focus();
                input.style.borderColor = "#dc2626";
                btn.disabled = false;
                return;
            } else {
                input.style.borderColor = "";
            }
        }

        // Detectar cambios
        const cambios = [];
        inputs.forEach((input) => {
            const row = input.closest(".task-row");
            const original = row?.dataset.originalName || "";
            const nuevo = input.value.trim();
            
            if (nuevo !== original) {
                cambios.push({
                    id: input.getAttribute("data-task-id"),
                    nombre: nuevo,
                    row,
                    input
                });
            }
        });

        // Si no hay cambios, solo revertir
        if (cambios.length === 0) {
            revertirTodos();
            finishUI();
            btn.disabled = false;
            return;
        }

        // Enviar actualizaciones
        const csrfToken = TaskConfig.csrfToken || 
            document.querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") || "";

        const resultados = await Promise.all(
            cambios.map(async (c) => {
                try {
                    const url = `${TaskConfig.endpoints.updateTarea}${c.id}`;
                    const res = await fetch(url, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                            "X-Requested-With": "XMLHttpRequest",
                            Accept: "application/json",
                        },
                        body: JSON.stringify({ nombre: c.nombre }),
                    });

                    const payloadText = await res.text();
                    let payload;
                    try {
                        payload = payloadText ? 
                            JSON.parse(payloadText) : {};
                    } catch {
                        payload = payloadText;
                    }

                    if (!res.ok) {
                        return {
                            ok: false,
                            status: res.status,
                            body: payload,
                            item: c,
                        };
                    }

                    return { ok: true, data: payload, item: c };
                } catch (error) {
                    return { ok: false, error, item: c };
                }
            })
        );

        // Procesar resultados
        const fallidos = resultados.filter((r) => !r.ok);
        const exitosos = resultados.filter((r) => r.ok);

        // Si hay errores, mostrarlos
        if (fallidos.length > 0) {
            fallidos.forEach((f) => {
                const input = f.item.input;
                if (input) {
                    input.style.borderColor = "#dc2626";
                    input.focus();
                }
                console.error("Error guardando tarea", {
                    id: f.item.id,
                    status: f.status,
                    body: f.body,
                    error: f.error,
                });
            });
            
            mostrarNotificacion("error", 
                `Error al guardar ${fallidos.length} tarea(s).`);
            btn.disabled = false;
            return;
        }

        // Si todo fue exitoso, recargar página
        mostrarNotificacion("success", 
            "Se han actualizado los nombres correctamente");
        
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

    function revertirTodos() {
        const allInputs = Array.from(
            document.querySelectorAll(".task-name-input")
        );
        
        allInputs.forEach((input) => {
            const row = input.closest(".task-row");
            const fallback = row?.dataset.originalName || 
                input.value.trim();
            
            const span = document.createElement("span");
            span.className = "task-name";
            span.textContent = fallback;
            
            input.parentNode.replaceChild(span, input);
            
            if (row) {
                row.dataset.originalName = fallback;
                row.setAttribute("data-nombre", fallback);
            }
        });
    }

    function finishUI() {
        editMode = false;
        btn.classList.remove("activo");
        
        if (icon && icon.classList.contains("fa-save")) {
            icon.classList.replace("fa-save", "fa-edit");
        }
        if (spanText) {
            spanText.textContent = " Editar";
        }
        
        rows.forEach((r) => delete r.dataset.originalName);
    }
}
```

---

## 🎨 Estados Visuales

### Input de Edición

```css
/* Estilos del input en modo edición */
.task-name-input {
    min-width: 180px;
    padding: 8px 12px;
    border: 2px solid #e5e7eb;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.task-name-input:focus {
    outline: none;
    border-color: #f59e0b;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
}

/* Input con error */
.task-name-input[style*="border-color: rgb(220, 38, 38)"] {
    border-color: #dc2626 !important;
    background-color: #fef2f2;
}
```

### Botón en Modo Activo

```css
.btn-miel.activo {
    background: linear-gradient(135deg, #10b981, #059669);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(16, 185, 129, 0.3);
}

.btn-miel.activo i.fa-save {
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}
```

---

## 🔄 Transformación de Elementos

### De Span a Input

**Antes (Modo Normal):**
```html
<td class="nombre">
    <span class="task-name">Inspeccionar colmenas</span>
</td>
```

**Durante (Modo Edición):**
```html
<td class="nombre">
    <input type="text" 
           class="task-name-input input-miel"
           value="Inspeccionar colmenas"
           data-task-id="123"
           style="min-width: 180px;">
</td>
```

### De Input a Span

**Después de Guardar:**
```html
<td class="nombre">
    <span class="task-name">Inspeccionar colmenas del sector norte</span>
</td>
```

---

## 🔒 Validación y Seguridad

### Validación Frontend

```javascript
// 1. Validar campos vacíos
for (const input of inputs) {
    const val = input.value.trim();
    if (val === "") {
        // Mostrar error
        input.style.borderColor = "#dc2626";
        input.focus();
        return; // Detener guardado
    }
}

// 2. Limpiar estilos de error
input.style.borderColor = "";
```

### Protección CSRF

```javascript
const csrfToken = document.querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");

headers: {
    "X-CSRF-TOKEN": csrfToken
}
```

### Validación Backend

```php
// app/Http/Controllers/TareasController.php

public function update(Request $request, $id)
{
    // Validar entrada
    $validated = $request->validate([
        'nombre' => 'required|string|max:255|min:3'
    ]);
    
    // Verificar pertenencia al usuario
    $subtarea = SubTarea::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();
    
    // Actualizar
    $subtarea->update($validated);
    
    return response()->json([
        'message' => 'Tarea actualizada correctamente',
        'tarea' => $subtarea
    ]);
}
```

---

## 📡 Comunicación con el Backend

### Endpoint de Actualización

**Ruta:** `POST /tareas/update/{id}`

**Headers:**
```javascript
{
    "Content-Type": "application/json",
    "X-CSRF-TOKEN": "...",
    "X-Requested-With": "XMLHttpRequest",
    "Accept": "application/json"
}
```

**Body:**
```json
{
    "nombre": "Nuevo nombre de la tarea"
}
```

**Respuesta Exitosa (200):**
```json
{
    "message": "Tarea actualizada correctamente",
    "tarea": {
        "id": 123,
        "nombre": "Nuevo nombre de la tarea",
        "updated_at": "2025-12-02T10:30:00.000000Z"
    }
}
```

**Respuesta con Error (422):**
```json
{
    "message": "Los datos proporcionados no son válidos",
    "errors": {
        "nombre": ["El campo nombre es obligatorio."]
    }
}
```

---

## 🔄 Manejo de Múltiples Peticiones

### Promise.all para Procesamiento Paralelo

```javascript
const resultados = await Promise.all(
    cambios.map(async (c) => {
        try {
            const response = await fetch(url, options);
            return { ok: true, data: await response.json(), item: c };
        } catch (error) {
            return { ok: false, error, item: c };
        }
    })
);

// Separar exitosos y fallidos
const exitosos = resultados.filter(r => r.ok);
const fallidos = resultados.filter(r => !r.ok);
```

### Ventajas del Procesamiento Paralelo

| Aspecto | Secuencial | Paralelo |
|---------|-----------|----------|
| **Tiempo** | N × T segundos | ~T segundos |
| **Experiencia** | Lenta | Rápida |
| **Ejemplo** | 10 tareas × 0.5s = 5s | 10 tareas ≈ 0.5s |

---

## 🐛 Solución de Problemas

### El botón no responde

**Verificar:**
1. ID del botón en HTML:
```html
<button id="btn-editar-nombre">
```

2. Función inicializada:
```javascript
$(document).ready(function () {
    configurarEditarNombresGlobal();
});
```

3. Consola del navegador:
```javascript
const btn = document.getElementById("btn-editar-nombre");
console.log('Botón encontrado:', btn);
```

### Los inputs no se crean

**Verificar:**
1. Elementos `.task-row` existen:
```javascript
const rows = document.querySelectorAll(".task-row");
console.log('Filas encontradas:', rows.length);
```

2. Elementos `.task-name` existen:
```javascript
rows.forEach(row => {
    const nameSpan = row.querySelector(".task-name");
    console.log('Span encontrado:', nameSpan);
});
```

### Error al guardar cambios

**Verificar:**
1. Token CSRF válido:
```javascript
const csrfToken = document.querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");
console.log('CSRF Token:', csrfToken);
```

2. Ruta correcta:
```javascript
console.log('Endpoint:', `${TaskConfig.endpoints.updateTarea}${id}`);
```

3. Respuesta del servidor:
```javascript
const response = await fetch(url, options);
const text = await response.text();
console.log('Respuesta:', text);
```

### La página no se recarga

**Verificar:**
1. Timeout configurado:
```javascript
setTimeout(() => {
    console.log('Recargando página...');
    window.location.reload();
}, 1000);
```

2. Errores en consola que bloquean ejecución

### Campos marcados con error persisten

**Causa:** No se limpia el estilo `borderColor`

**Solución:**
```javascript
// Antes de validar, limpiar todos los errores
inputs.forEach(input => {
    input.style.borderColor = "";
});

// Luego validar
for (const input of inputs) {
    if (input.value.trim() === "") {
        input.style.borderColor = "#dc2626";
        // ...
    }
}
```

---

## 🎯 Mejores Prácticas

### 1. Validación Progresiva

```javascript
// ✅ Validar ANTES de enviar
for (const input of inputs) {
    if (input.value.trim() === "") {
        // Detener y mostrar error
        return;
    }
}

// ❌ No validar
const response = await fetch(url, {
    body: JSON.stringify({ nombre: "" }) // Esto fallará
});
```

### 2. Manejo de Errores Individual

```javascript
// ✅ Marcar solo los inputs con error
fallidos.forEach((f) => {
    f.item.input.style.borderColor = "#dc2626";
});

// ❌ Bloquear todo por un error
if (fallidos.length > 0) {
    revertirTodos(); // Perdería cambios exitosos
}
```

### 3. Feedback Visual Inmediato

```javascript
// ✅ Deshabilitar botón durante guardado
btn.disabled = true;

// ✅ Cambiar cursor
btn.style.cursor = "wait";

// ✅ Mostrar notificación al finalizar
mostrarNotificacion("success", "Cambios guardados");
```

### 4. Preservar Datos del Usuario

```javascript
// ✅ Guardar valor original
row.dataset.originalName = original;

// ✅ Permitir cancelar sin pérdida
if (cambios.length === 0) {
    revertirTodos(); // Restaurar valores originales
}
```

---

## 📝 Casos de Uso

### Caso 1: Edición Simple

**Escenario:** Usuario quiere cambiar el nombre de 2 tareas

**Flujo:**
1. Click en "Editar"
2. Modificar 2 nombres
3. Click en "Guardar"
4. Sistema envía 2 peticiones
5. Ambas exitosas
6. Notificación de éxito
7. Página se recarga

### Caso 2: Validación de Campo Vacío

**Escenario:** Usuario borra accidentalmente un nombre

**Flujo:**
1. Click en "Editar"
2. Usuario borra contenido de un input
3. Click en "Guardar"
4. Sistema detecta campo vacío
5. Mostrar alerta
6. Marcar campo con borde rojo
7. Focus en campo problemático
8. Usuario corrige
9. Reintenta guardar

### Caso 3: Error del Servidor

**Escenario:** Servidor responde con error 500

**Flujo:**
1. Click en "Editar"
2. Modificar nombres
3. Click en "Guardar"
4. Petición enviada
5. Servidor responde 500
6. Sistema marca inputs como error
7. Mostrar notificación de error
8. Mantener modo edición
9. Usuario puede reintentar

### Caso 4: Sin Cambios

**Escenario:** Usuario activa edición pero no cambia nada

**Flujo:**
1. Click en "Editar"
2. (Usuario no modifica nada)
3. Click en "Guardar"
4. Sistema detecta 0 cambios
5. Revertir a modo normal
6. No se envía ninguna petición
7. Continuar normalmente

---

## 🔗 Archivos Relacionados

- **Vista principal**: `resources/views/tareas/index.blade.php`
- **Vista lista**: `resources/views/tareas/list.blade.php`
- **JavaScript**: `public/js/components/home-user/tasks/list.js`
- **Controlador**: `app/Http/Controllers/TareasController.php`
- **Modelo**: `app/Models/SubTarea.php`
- **Rutas**: `routes/web.php`
- **Estilos**: `public/css/components/home-user/tasks/list.css`

---

## 📚 Tecnologías Utilizadas

| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| **JavaScript** | ES6+ | Lógica de interacción |
| **jQuery** | 3.x | Manipulación DOM (opcional) |
| **Fetch API** | Nativo | Peticiones HTTP |
| **Laravel** | 10.x | Backend API |
| **CSS3** | - | Estilos y animaciones |

---

## 📞 Referencias Adicionales

Para más información sobre otros componentes del sistema:
- **Vista Lista**: (pendiente documentación específica)
- **Vista Agenda**: `README_AGENDA.md`
- **Vista Imprimir**: `README_IMPRIMIR.md`
- **Sistema de Prioridades**: `README_PRIORIDADES_AUTOMATICAS.md`

---

## 📈 Futuras Mejoras

### Posibles Extensiones

1. **Edición inline:**
   - Doble click en nombre para editar
   - Enter para guardar
   - Escape para cancelar

2. **Historial de cambios:**
   - Registrar cambios de nombres
   - Ver historial por tarea
   - Opción de deshacer

3. **Edición de otros campos:**
   - Fechas
   - Prioridad
   - Estado
   - Descripción

4. **Autoguardado:**
   - Guardar cambios automáticamente cada N segundos
   - Indicador de "Guardando..."
   - Sincronización en tiempo real

5. **Búsqueda y reemplazo:**
   - Buscar texto en nombres
   - Reemplazar en múltiples tareas
   - Expresiones regulares

---

**Estado**: Sistema completamente funcional y documentado  
**Fecha**: Diciembre 2025
**Ultima Modificación**: Diciembre 2025
**Versión**: 1.0
