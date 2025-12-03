# Tour Interactivo - Guía Completa

## 📋 Descripción General

El **Tour Interactivo** es una funcionalidad que guía a los usuarios paso a paso a través de la interfaz de la Vista Lista de Tareas. Utiliza la librería **Intro.js** para crear tooltips informativos que destacan cada elemento de la interfaz y explican su función.

Este sistema de onboarding ayuda a los usuarios nuevos a familiarizarse rápidamente con todas las funcionalidades disponibles en la aplicación.

---

## 🎯 Características Principales

### 1. **Guía Paso a Paso**
- Tooltips informativos en cada elemento clave
- Navegación secuencial por la interfaz
- Overlay oscuro que destaca el elemento activo
- Indicador de progreso visual

### 2. **Controles de Navegación**
- Botón "Siguiente" para avanzar
- Botón "Anterior" para retroceder
- Botón "Salir" para cancelar el tour
- Botón "¡Entendido!" al finalizar

### 3. **Elementos Explicados**
- Tabla de tareas
- Columna de nombres
- Columna de prioridades
- Columna de estados
- Columna de fecha inicio
- Columna de fecha límite
- Columna de acciones

### 4. **Experiencia de Usuario**
- Posicionamiento automático de tooltips
- Prevención de interacción durante el tour
- Diseño responsive y accesible
- Personalización completa en español

---

## 🏗️ Estructura de Archivos

### Vista Principal
**Ubicación**: `resources/views/tareas/list.blade.php`

Contiene el botón de inicio del tour:
```html
<h1 class="header-title">
    <i class="fa-solid fa-list-check"></i>
    Lista de Tareas 
    <i class="fa-solid fa-circle-question" id="startTour"></i>
</h1>
```

### Elementos con Atributos de Tour
Los elementos que se explican en el tour tienen dos atributos especiales:
```html
<div data-intro="Texto explicativo" data-step="1">
    <!-- Contenido del elemento -->
</div>
```

### Script JavaScript
**Ubicación**: `public/js/components/home-user/tasks/list.js`

Contiene la configuración e inicialización de Intro.js.

### Librería Intro.js
**CDN CSS**: `https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/introjs.min.css`  
**CDN JS**: `https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/intro.min.js`

---

## 📊 Flujo de Funcionamiento

### Flujo Completo del Tour

```
1. Usuario hace clic en ícono de interrogación
   └─> Evento click capturado
       └─> Ejecutar intro.start()
           ├─> Crear overlay oscuro
           ├─> Destacar primer elemento
           └─> Mostrar primer tooltip
               ├─> Texto: "Bienvenido al tutorial..."
               └─> Botones: [Salir] [Siguiente →]

2. Usuario navega por los pasos
   └─> Clic en "Siguiente →"
       ├─> Ocultar tooltip actual
       ├─> Destacar siguiente elemento
       └─> Mostrar siguiente tooltip
           ├─> Actualizar indicador de progreso
           └─> Botones: [← Anterior] [Salir] [Siguiente →]

   └─> Clic en "← Anterior"
       ├─> Volver al paso anterior
       └─> Actualizar tooltip y destacado

   └─> Clic en "Salir"
       ├─> Cerrar tour inmediatamente
       ├─> Eliminar overlay
       └─> Restaurar interacción normal

3. Usuario llega al último paso
   └─> Mostrar último tooltip
       └─> Botones: [← Anterior] [¡Entendido!]
           └─> Clic en "¡Entendido!"
               ├─> Completar tour
               ├─> Eliminar overlay
               └─> Restaurar interfaz normal

4. Clic fuera del tooltip (deshabilitado)
   └─> No hace nada (exitOnOverlayClick: false)
```

---

## 💻 Implementación JavaScript

### Configuración Inicial

```javascript
// Ubicación: public/js/components/home-user/tasks/list.js

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Intro.js
    const intro = introJs();
    
    // Configurar opciones personalizadas
    intro.setOptions({
        nextLabel: 'Siguiente →',        // Texto del botón siguiente
        prevLabel: '← Anterior',          // Texto del botón anterior
        skipLabel: 'Salir',               // Texto del botón salir
        doneLabel: '¡Entendido!',         // Texto del botón final
        tooltipPosition: 'auto',          // Posición automática
        showProgress: true,               // Mostrar barra de progreso
        showBullets: false,               // Ocultar bullets de navegación
        exitOnOverlayClick: false,        // No cerrar al hacer clic fuera
        disableInteraction: true,         // Deshabilitar interacción con elementos
        overlayOpacity: 0.7              // Opacidad del overlay (70%)
    });

    // Configurar evento del botón de inicio
    document.getElementById('startTour').addEventListener('click', function() {
        intro.start();
    });
});
```

### Opciones Disponibles

| Opción | Tipo | Valor por Defecto | Descripción |
|--------|------|-------------------|-------------|
| `nextLabel` | String | 'Next' | Texto del botón siguiente |
| `prevLabel` | String | 'Back' | Texto del botón anterior |
| `skipLabel` | String | 'Skip' | Texto del botón salir |
| `doneLabel` | String | 'Done' | Texto del botón final |
| `tooltipPosition` | String | 'bottom' | Posición del tooltip: 'top', 'bottom', 'left', 'right', 'auto' |
| `showProgress` | Boolean | false | Mostrar indicador de progreso |
| `showBullets` | Boolean | true | Mostrar bullets de navegación |
| `exitOnOverlayClick` | Boolean | true | Permitir cerrar haciendo clic en el overlay |
| `disableInteraction` | Boolean | false | Deshabilitar interacción con elementos destacados |
| `overlayOpacity` | Number | 0.8 | Opacidad del overlay (0.0 - 1.0) |
| `scrollToElement` | Boolean | true | Hacer scroll automático al elemento |
| `scrollPadding` | Number | 30 | Padding al hacer scroll (en píxeles) |
| `keyboardNavigation` | Boolean | true | Permitir navegación con teclado |
| `showStepNumbers` | Boolean | true | Mostrar números de paso |
| `exitOnEsc` | Boolean | true | Cerrar con tecla ESC |

---

## 🎨 Atributos HTML para el Tour

### data-intro
Define el texto explicativo que aparecerá en el tooltip:

```html
<div data-intro="Este es el texto que verá el usuario">
    Contenido del elemento
</div>
```

### data-step
Define el orden del paso en el tour (numérico):

```html
<div data-intro="Primer paso" data-step="1">Elemento 1</div>
<div data-intro="Segundo paso" data-step="2">Elemento 2</div>
<div data-intro="Tercer paso" data-step="3">Elemento 3</div>
```

### Ejemplo Completo de Configuración

```html
<!-- Paso 1: Contenedor principal -->
<div class="tasks-table-container" 
     id="tasksTableContainer" 
     data-intro="Bienvenido al tutorial de la Lista de Tareas. Te explicaremos cada función paso a paso." 
     data-step="1">
    
    <table class="tasks-table">
        <thead>
            <tr>
                <!-- Paso 2: Columna nombre -->
                <th data-intro="Aquí puedes ver el nombre de cada tarea." 
                    data-step="2">
                    Nombre de Tarea
                </th>
                
                <!-- Paso 3: Columna prioridad -->
                <th data-intro="Esta columna muestra la prioridad asignada a cada tarea. La prioridad indica la importancia: Baja (azul), Media (verde), Alta (amarillo) o Urgente (rojo)." 
                    data-step="3">
                    Prioridad
                </th>
                
                <!-- Paso 4: Columna estado -->
                <th data-intro="Aquí puedes ver y cambiar el estado de cada tarea. El estado muestra el progreso de la tarea: Pendiente, En progreso o Completada." 
                    data-step="4">
                    Estado
                </th>
                
                <!-- Paso 5: Columna fecha inicio -->
                <th data-intro="Esta columna muestra la fecha de inicio asignada a cada tarea. La fecha de inicio indica cuándo se debe comenzar a trabajar en la tarea." 
                    data-step="5">
                    Fecha Inicio
                </th>
                
                <!-- Paso 6: Columna fecha límite -->
                <th data-intro="Esta columna muestra la fecha límite asignada a cada tarea para que sea completada. Es importante cumplir con esta fecha según la prioridad." 
                    data-step="6">
                    Fecha Límite
                </th>
                
                <!-- Paso 7: Columna acciones -->
                <th data-intro="En esta columna encontrarás los botones para guardar los cambios realizados en cada tarea o descartarla si ya no es relevante." 
                    data-step="7">
                    Acciones
                </th>
            </tr>
        </thead>
    </table>
</div>
```

---

## 🎨 Estilos CSS de Intro.js

### Estructura del Tooltip

Intro.js genera automáticamente la siguiente estructura HTML:

```html
<!-- Overlay oscuro -->
<div class="introjs-overlay"></div>

<!-- Capa de destacado -->
<div class="introjs-helperLayer"></div>

<!-- Tooltip -->
<div class="introjs-tooltip">
    <!-- Flecha del tooltip -->
    <div class="introjs-arrow"></div>
    
    <!-- Contenido -->
    <div class="introjs-tooltiptext">
        Texto explicativo del paso actual
    </div>
    
    <!-- Barra de progreso (si showProgress: true) -->
    <div class="introjs-progress">
        <div class="introjs-progressbar" style="width: 14.28%;"></div>
    </div>
    
    <!-- Botones de navegación -->
    <div class="introjs-tooltipbuttons">
        <a class="introjs-button introjs-prevbutton">← Anterior</a>
        <a class="introjs-button introjs-skipbutton">Salir</a>
        <a class="introjs-button introjs-nextbutton">Siguiente →</a>
    </div>
</div>
```

### Personalización de Estilos

Para personalizar los estilos, puedes agregar CSS adicional:

```css
/* Personalizar colores del tooltip */
.introjs-tooltip {
    background-color: #ffffff;
    border: 2px solid #f59e0b;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    min-width: 320px;
    max-width: 450px;
}

/* Personalizar texto */
.introjs-tooltiptext {
    color: #374151;
    font-size: 15px;
    line-height: 1.6;
    padding: 16px;
}

/* Personalizar botones */
.introjs-button {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border: none;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.introjs-button:hover {
    background: linear-gradient(135deg, #d97706, #b45309);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

/* Personalizar overlay */
.introjs-overlay {
    background-color: rgba(0, 0, 0, 0.7);
}

/* Personalizar capa de destacado */
.introjs-helperLayer {
    border: 3px solid #f59e0b;
    box-shadow: 0 0 0 5000px rgba(0, 0, 0, 0.7);
    border-radius: 8px;
}

/* Personalizar barra de progreso */
.introjs-progress {
    background-color: #e5e7eb;
    height: 6px;
    border-radius: 3px;
    overflow: hidden;
}

.introjs-progressbar {
    background: linear-gradient(90deg, #f59e0b, #d97706);
    height: 100%;
    transition: width 0.3s ease;
}
```

---

## 🔧 Métodos y API de Intro.js

### Métodos Principales

```javascript
const intro = introJs();

// Iniciar el tour
intro.start();

// Ir al siguiente paso
intro.nextStep();

// Ir al paso anterior
intro.previousStep();

// Ir a un paso específico
intro.goToStep(3);

// Salir del tour
intro.exit();

// Agregar paso programáticamente
intro.addStep({
    element: document.querySelector('#elemento'),
    intro: 'Texto explicativo',
    position: 'right'
});

// Actualizar opciones
intro.setOptions({
    nextLabel: 'Continuar',
    showProgress: false
});

// Obtener paso actual
const stepActual = intro.currentStep();
```

### Callbacks (Eventos)

```javascript
intro.setOptions({
    // Antes de cambiar de paso
    onbeforechange: function(targetElement) {
        console.log('Cambiando a:', targetElement);
    },
    
    // Después de cambiar de paso
    onchange: function(targetElement) {
        console.log('Ahora en:', targetElement);
    },
    
    // Al completar el tour
    oncomplete: function() {
        console.log('Tour completado');
        // Guardar en localStorage que el usuario completó el tour
        localStorage.setItem('tour_completado', 'true');
    },
    
    // Al salir del tour
    onexit: function() {
        console.log('Usuario salió del tour');
    },
    
    // Antes de salir
    onbeforeexit: function() {
        console.log('A punto de salir');
        // Retornar false para prevenir el cierre
        return true;
    }
});
```

---

## 🚀 Casos de Uso Avanzados

### Caso 1: Tour Automático en Primera Visita

```javascript
document.addEventListener('DOMContentLoaded', function() {
    const intro = introJs();
    intro.setOptions({
        nextLabel: 'Siguiente →',
        prevLabel: '← Anterior',
        skipLabel: 'Salir',
        doneLabel: '¡Entendido!',
    });

    // Verificar si es la primera visita
    if (!localStorage.getItem('tour_completado')) {
        // Iniciar tour automáticamente después de 1 segundo
        setTimeout(() => {
            intro.start();
        }, 1000);
    }

    // Marcar como completado al finalizar
    intro.setOptions({
        oncomplete: function() {
            localStorage.setItem('tour_completado', 'true');
        }
    });

    // Botón manual para reiniciar tour
    document.getElementById('startTour').addEventListener('click', function() {
        intro.start();
    });
});
```

### Caso 2: Tour con Validación de Pasos

```javascript
const intro = introJs();

intro.setOptions({
    onbeforechange: function(targetElement) {
        const stepActual = intro.currentStep();
        
        // Validar en el paso 3 que haya seleccionado un estado
        if (stepActual === 2) { // Índice 2 = paso 3
            const estadoSeleccionado = document.querySelector('.estado').value;
            if (!estadoSeleccionado) {
                alert('Por favor, selecciona un estado antes de continuar');
                return false; // Prevenir avance
            }
        }
        
        return true; // Permitir avance
    }
});
```

### Caso 3: Tour con Pasos Dinámicos

```javascript
const intro = introJs();

// Limpiar pasos existentes
intro.setOptions({
    steps: []
});

// Agregar pasos dinámicamente basados en elementos visibles
const elementosVisibles = document.querySelectorAll('.task-row:not([style*="display: none"])');

intro.addStep({
    element: document.querySelector('.tasks-table-container'),
    intro: 'Estas son tus tareas visibles actualmente',
    position: 'top'
});

elementosVisibles.forEach((elemento, index) => {
    intro.addStep({
        element: elemento,
        intro: `Tarea ${index + 1}: ${elemento.querySelector('.task-name').textContent}`,
        position: 'auto'
    });
});

intro.start();
```

### Caso 4: Tour con Tooltips Personalizados por Paso

```javascript
const intro = introJs();

intro.setOptions({
    onchange: function(targetElement) {
        const stepActual = intro.currentStep();
        
        // Personalizar tooltip según el paso
        const tooltip = document.querySelector('.introjs-tooltip');
        
        if (stepActual === 0) {
            // Paso 1: Tooltip grande
            tooltip.style.maxWidth = '500px';
        } else if (stepActual === 3) {
            // Paso 4: Agregar contenido HTML personalizado
            const tooltipText = tooltip.querySelector('.introjs-tooltiptext');
            tooltipText.innerHTML += '<br><img src="/img/ejemplo-estado.png" style="max-width: 100%; margin-top: 10px;">';
        }
    }
});
```

---

## 🐛 Solución de Problemas

### El tour no inicia

**Verificar:**

1. **Librería cargada correctamente:**
```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/introjs.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/intro.min.js"></script>
```

2. **Elemento de inicio existe:**
```javascript
const boton = document.getElementById('startTour');
console.log('Botón encontrado:', boton); // Debe mostrar el elemento
```

3. **Consola del navegador:**
```javascript
console.log('Intro.js disponible:', typeof introJs); // Debe ser 'function'
```

### Los tooltips no se posicionan correctamente

**Solución:**

1. **Usar posicionamiento automático:**
```javascript
intro.setOptions({
    tooltipPosition: 'auto' // En lugar de 'bottom', 'top', etc.
});
```

2. **Ajustar padding de scroll:**
```javascript
intro.setOptions({
    scrollPadding: 50 // Más espacio alrededor del elemento
});
```

### Los elementos destacados no son visibles

**Verificar:**

1. **Elementos tienen tamaño:**
```css
[data-intro] {
    min-width: 100px;
    min-height: 30px;
}
```

2. **Z-index de elementos:**
```css
.elemento-destacado {
    position: relative;
    z-index: 9999 !important;
}
```

### El overlay cubre el tooltip

**Solución:**

```javascript
intro.setOptions({
    overlayOpacity: 0.5 // Reducir opacidad
});
```

O ajustar el z-index manualmente:

```css
.introjs-overlay {
    z-index: 999998 !important;
}

.introjs-helperLayer {
    z-index: 999999 !important;
}

.introjs-tooltip {
    z-index: 10000000 !important;
}
```

### El tour se rompe con elementos dinámicos

**Causa:** Los elementos cambian después de inicializar el tour.

**Solución:** Re-inicializar el tour después de cambios:

```javascript
function reiniciarTour() {
    // Destruir instancia anterior si existe
    if (typeof intro !== 'undefined') {
        intro.exit();
    }
    
    // Crear nueva instancia
    intro = introJs();
    intro.setOptions({
        // opciones...
    });
    
    intro.start();
}

// Llamar después de actualizar contenido
document.querySelector('.actualizar-tareas').addEventListener('click', function() {
    // Actualizar tareas...
    setTimeout(reiniciarTour, 500);
});
```

---

## 📚 Documentación Oficial de Intro.js

### Recursos Principales

| Recurso | URL |
|---------|-----|
| **Sitio Oficial** | [https://introjs.com/](https://introjs.com/) |
| **Documentación** | [https://introjs.com/docs](https://introjs.com/docs) |
| **GitHub** | [https://github.com/usablica/intro.js](https://github.com/usablica/intro.js) |
| **Ejemplos** | [https://introjs.com/example/hello-world/index.html](https://introjs.com/example/hello-world/index.html) |
| **CDN** | [https://cdnjs.com/libraries/intro.js](https://cdnjs.com/libraries/intro.js) |

### Secciones Importantes de la Documentación

1. **Getting Started** - Instalación y configuración básica
   - [https://introjs.com/docs/intro/start](https://introjs.com/docs/intro/start)

2. **Options** - Lista completa de opciones disponibles
   - [https://introjs.com/docs/intro/options](https://introjs.com/docs/intro/options)

3. **API** - Métodos y funciones disponibles
   - [https://introjs.com/docs/intro/api](https://introjs.com/docs/intro/api)

4. **Themes** - Temas predefinidos y personalización
   - [https://introjs.com/docs/themes/list](https://introjs.com/docs/themes/list)

5. **Examples** - Ejemplos de implementación
   - [https://introjs.com/docs/examples/basic/hello-world](https://introjs.com/docs/examples/basic/hello-world)

### Tutoriales Recomendados

1. **Tutorial Básico:**
   ```
   https://www.sitepoint.com/creating-product-tours-with-intro-js/
   ```

2. **Tutorial Avanzado:**
   ```
   https://www.digitalocean.com/community/tutorials/how-to-create-a-product-tour-with-intro-js
   ```

3. **Integración con Frameworks:**
   - React: `https://github.com/HiDeoo/intro.js-react`
   - Vue: `https://github.com/alex-oleshkevich/vue-introjs`
   - Angular: `https://github.com/DaniloNovakovic/angular-intro.js`

---

## 🎓 Mejores Prácticas

### 1. Textos Claros y Concisos

```javascript
// ✅ Bueno: Claro y directo
data-intro="Haz clic en Guardar para aplicar los cambios"

// ❌ Malo: Muy largo y confuso
data-intro="En esta sección podrás encontrar el botón de guardar que te permitirá guardar todos los cambios que hayas realizado en la tarea, incluyendo el nombre, la prioridad, el estado y las fechas, pero recuerda que..."
```

### 2. Orden Lógico de Pasos

```html
<!-- ✅ Orden visual de arriba hacia abajo, izquierda a derecha -->
<div data-step="1">Header</div>
<div data-step="2">Filtros</div>
<div data-step="3">Tabla</div>
<div data-step="4">Paginación</div>

<!-- ❌ Orden aleatorio que confunde al usuario -->
<div data-step="3">Tabla</div>
<div data-step="1">Header</div>
<div data-step="4">Paginación</div>
<div data-step="2">Filtros</div>
```

### 3. Limitar Número de Pasos

```javascript
// ✅ Tour corto y enfocado (5-7 pasos ideales)
// Máximo 10 pasos para no abrumar al usuario

// ❌ Tour muy largo (más de 15 pasos)
// El usuario abandona antes de terminar
```

### 4. Permitir Saltar el Tour

```javascript
// ✅ Siempre permitir salir
intro.setOptions({
    exitOnEsc: true,
    exitOnOverlayClick: true, // Para tours opcionales
    skipLabel: 'Saltar'
});

// ❌ Forzar completar el tour
intro.setOptions({
    exitOnEsc: false,
    exitOnOverlayClick: false,
    hideNext: true, // Sin botón de salir
});
```

### 5. Guardar Progreso del Usuario

```javascript
intro.setOptions({
    oncomplete: function() {
        // Guardar que completó el tour
        localStorage.setItem('tour_lista_tareas_completado', 'true');
        localStorage.setItem('tour_fecha_completado', new Date().toISOString());
    },
    
    onexit: function() {
        // Guardar hasta qué paso llegó
        localStorage.setItem('tour_ultimo_paso', intro.currentStep());
    }
});

// Al iniciar, verificar si ya completó el tour
if (localStorage.getItem('tour_lista_tareas_completado')) {
    // No mostrar automáticamente, solo si hace clic en el botón
    console.log('Usuario ya completó el tour');
}
```

---

## 🔄 Integración con Otros Componentes

### Con Sistema de Filtros

```javascript
// Iniciar tour cuando el usuario aplica primer filtro
document.querySelector('.filter-btn').addEventListener('click', function() {
    const primeraVez = !localStorage.getItem('filtros_tour_visto');
    
    if (primeraVez) {
        const tourFiltros = introJs();
        tourFiltros.setOptions({
            steps: [
                {
                    element: '.filter-buttons',
                    intro: 'Usa estos filtros para ver tareas específicas'
                },
                {
                    element: '.header-semaphore',
                    intro: 'O filtra por prioridad haciendo clic aquí'
                }
            ]
        });
        
        tourFiltros.start();
        localStorage.setItem('filtros_tour_visto', 'true');
    }
});
```

### Con Paginación

```javascript
intro.setOptions({
    onbeforechange: function(targetElement) {
        // Si el siguiente paso está en otra página, cambiar de página primero
        const stepActual = intro.currentStep();
        
        if (stepActual === 5 && AppState.paginacion.paginaActual !== 2) {
            // Cambiar a página 2
            AppState.paginacion.paginaActual = 2;
            paginarTabla();
            
            // Esperar a que se renderice la página
            setTimeout(() => {
                intro.refresh(); // Actualizar posiciones
            }, 300);
        }
    }
});
```

---

## 📱 Responsividad

### Ajustes para Móviles

```javascript
// Detectar dispositivo móvil
const esMobile = window.innerWidth < 768;

intro.setOptions({
    // En móvil, tooltips más pequeños
    tooltipClass: esMobile ? 'introjs-tooltip-mobile' : '',
    
    // Posición preferente en móvil
    tooltipPosition: esMobile ? 'bottom' : 'auto',
    
    // Textos más cortos en móvil
    // (configurar con atributos data-intro-mobile)
});
```

### CSS Responsivo

```css
/* Ajustes para móviles */
@media (max-width: 768px) {
    .introjs-tooltip {
        max-width: 90vw !important;
        font-size: 14px;
    }
    
    .introjs-tooltiptext {
        padding: 12px;
    }
    
    .introjs-button {
        padding: 8px 16px;
        font-size: 13px;
    }
    
    .introjs-tooltip.introjs-tooltip-mobile {
        min-width: 280px;
    }
}
```

---

## 🔗 Archivos Relacionados

- **Vista principal**: `resources/views/tareas/list.blade.php`
- **JavaScript**: `public/js/components/home-user/tasks/list.js`
- **CSS de Intro.js**: CDN o `public/css/libs/introjs.min.css`
- **Librería Intro.js**: CDN o `public/js/libs/intro.min.js`

---

## 📚 Tecnologías Utilizadas

| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| **Intro.js** | 7.2.0 | Sistema de tours interactivos |
| **JavaScript** | ES6+ | Lógica de configuración |
| **HTML5** | - | Atributos de datos (data-*) |
| **CSS3** | - | Personalización de estilos |
| **Font Awesome** | 6.x | Ícono de interrogación |

---

## 📞 Referencias Adicionales

Para más información sobre otros componentes del sistema:
- **Vista Lista**: (archivo actual)
- **Vista Agenda**: `README_AGENDA.md`
- **Vista Imprimir**: `README_IMPRIMIR.md`
- **Botón Editar**: `README_EDITAR.md`
- **Sistema de Prioridades**: `README_PRIORIDADES_AUTOMATICAS.md`

---

## 📈 Futuras Mejoras

### Posibles Extensiones

1. **Tours Múltiples:**
   - Tour básico para nuevos usuarios
   - Tour avanzado para funciones especiales
   - Tour de novedades para usuarios existentes

2. **Interactividad:**
   - Requerir que el usuario complete acciones
   - Validar que completó cada paso antes de continuar
   - Gamificación con badges o logros

3. **Personalización por Usuario:**
   - Tours diferentes según el rol del usuario
   - Recordar preferencias de tour
   - Permitir reactivar tours completados

4. **Analytics:**
   - Rastrear cuántos usuarios completan el tour
   - Identificar pasos donde los usuarios abandonan
   - Medir tiempo promedio de completación

5. **Tours Contextuales:**
   - Mostrar tours solo cuando sea relevante
   - Tours activados por eventos específicos
   - Sugerencias inteligentes basadas en uso

---

**Estado**: Sistema completamente funcional y documentado  
**Librería**: Intro.js v7.2.0  
**Fecha**: Diciembre 2025
**Ultima Modificación**: Diciembre 2025
**Versión**: 1.0
