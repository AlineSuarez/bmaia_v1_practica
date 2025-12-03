# Vista Imprimir - Guía Completa

## 📋 Descripción General

La **funcionalidad de Imprimir** permite generar y previsualizar un documento PDF con todas las tareas activas del usuario, organizadas por etapas del proyecto. El sistema ofrece una vista previa en modal antes de imprimir o descargar el documento.

---

## 🎯 Características Principales

### 1. **Generación de PDF**
- Documento PDF profesional con todas las tareas activas
- Organización por etapas del proyecto (Tareas Generales)
- Información completa de cada tarea:
  - Nombre de la tarea
  - Fecha de inicio
  - Fecha límite
  - Prioridad (con indicador visual de color)
  - Estado actual
- Fecha y hora de generación del documento

### 2. **Vista Previa en Modal**
- Previsualización del PDF antes de imprimir/descargar
- Modal de pantalla completa (responsive)
- Iframe para visualización directa del PDF
- Loader visual durante la carga
- Opciones de impresión y descarga

### 3. **Opciones de Exportación**
- **Imprimir directamente** desde el navegador
- **Descargar PDF** al dispositivo local
- **Vista previa** antes de cualquier acción

### 4. **Filtrado Inteligente**
- Solo incluye tareas activas (no archivadas)
- Elimina duplicados automáticamente
- Agrupa por etapa del proyecto
- Ordenación lógica de información

---

## 🏗️ Estructura de Archivos

### Vista Principal
**Ubicación**: `resources/views/tareas/index.blade.php`

Contiene el botón de imprimir y el modal de vista previa:
```html
<button type="button"
    id="btn-preview-pdf"
    class="btn-miel print-button"
    title="Vista previa e imprimir"
    data-pdf-url="{{ route('tareas.imprimirTodas') }}">
    <i class="fa fa-print"></i>
    <span>Imprimir</span>
</button>
```

### Plantilla del PDF
**Ubicación**: `resources/views/documents/tareas-todas.blade.php`

Define la estructura y estilos del documento PDF generado.

### Controlador Backend
**Ubicación**: `app/Http/Controllers/DocumentController.php`

Método: `imprimirTodasSubtareas()`

### Lógica JavaScript
**Ubicación**: `public/js/components/home-user/tareas.js`

Implementa:
- Manejo del modal de vista previa
- Carga del PDF en iframe
- Eventos de impresión y descarga
- Estados de carga (loader)

### Configuración de Rutas
**Ubicación**: `routes/web.php`

```php
Route::get('/tareas/imprimir-todas', 
    [DocumentController::class, 'imprimirTodasSubtareas'])
    ->name('tareas.imprimirTodas')
    ->middleware('auth');
```

---

## 📊 Flujo de Funcionamiento

### Flujo Completo de Impresión

```
1. Usuario hace clic en botón "Imprimir"
   └─> Evento click en #btn-preview-pdf
       └─> Abrir modal #pdfPreviewModal
           ├─> Mostrar loader
           └─> Cargar PDF en iframe
               └─> GET /tareas/imprimir-todas
                   └─> DocumentController@imprimirTodasSubtareas()
                       ├─> Obtener usuario autenticado
                       ├─> Filtrar tareas activas del usuario
                       ├─> Eliminar duplicados
                       ├─> Agrupar por etapa
                       └─> Generar PDF con DomPDF
                           └─> Cargar vista: tareas-todas.blade.php
                               ├─> Aplicar estilos CSS
                               ├─> Renderizar tabla por etapa
                               └─> Retornar PDF como stream
                                   └─> Mostrar en iframe
                                       ├─> Ocultar loader
                                       └─> Usuario puede:
                                           ├─> Imprimir (window.print)
                                           ├─> Descargar (download)
                                           └─> Cerrar modal
```

### Proceso de Generación del PDF

```
Backend (DocumentController.php):

1. Autenticación
   └─> Auth::user()

2. Consulta a Base de Datos
   └─> SubTarea::with('tareaGeneral')
       ├─> where('user_id', $user->id)
       ├─> where('archivada', false)
       └─> get()

3. Normalización de Datos
   └─> Eliminar duplicados por nombre
       ├─> Convertir nombres a lowercase
       └─> unique('nombre_key')

4. Generación del PDF
   └─> Pdf::loadView('documents.tareas-todas')
       ├─> setPaper('A4', 'portrait')
       ├─> setOptions([...])
       └─> stream('Tareas_Activas.pdf')

5. Retorno
   └─> PDF como stream (no descarga)
```

---

## 🎨 Estructura del Modal

### HTML del Modal

```html
<div class="modal fade" id="pdfPreviewModal" 
     tabindex="-1" 
     role="dialog">
    <div class="modal-dialog modal-xl modal-fullscreen-sm-down">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-file-pdf"></i> 
                    Vista previa - Tareas
                </h5>
                <button type="button" class="btn-close" 
                        data-bs-dismiss="modal">X</button>
            </div>

            <!-- Body con loader e iframe -->
            <div class="modal-body">
                <!-- Loader (oculto por defecto) -->
                <div id="pdf-loader" style="display: none;">
                    <div class="spinner-border"></div>
                    <p>Cargando PDF...</p>
                </div>

                <!-- Iframe para el PDF -->
                <iframe id="pdfPreviewIframe" 
                        src="" 
                        style="width:100%; height:100%;"></iframe>
            </div>

            <!-- Footer con acciones -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" 
                        data-bs-dismiss="modal">Cerrar</button>
                <button type="button" id="btn-print-iframe">
                    <i class="fa fa-print"></i> Imprimir
                </button>
                <button type="button" id="btn-download-pdf">
                    <i class="fa fa-download"></i> Descargar
                </button>
            </div>
        </div>
    </div>
</div>
```

### Estados del Modal

| Estado | Descripción | Elementos Visibles |
|--------|-------------|-------------------|
| **Cerrado** | Modal oculto | Ninguno |
| **Cargando** | Generando PDF | Loader animado |
| **Listo** | PDF visible | Iframe + Botones |
| **Error** | Fallo al cargar | Mensaje de error |

---

## 🔧 Implementación JavaScript

### Código Principal

```javascript
// Ubicación: public/js/components/home-user/tareas.js

(function () {
    const btnPreviewPdf = document.getElementById('btn-preview-pdf');
    const pdfPreviewModal = document.getElementById('pdfPreviewModal');
    const pdfPreviewIframe = document.getElementById('pdfPreviewIframe');
    const pdfLoader = document.getElementById('pdf-loader');
    const btnPrintIframe = document.getElementById('btn-print-iframe');
    const btnDownloadPdf = document.getElementById('btn-download-pdf');

    if (!btnPreviewPdf || !pdfPreviewModal) return;

    // Abrir modal y cargar PDF
    btnPreviewPdf.addEventListener('click', function () {
        const pdfUrl = this.getAttribute('data-pdf-url');
        
        if (!pdfUrl) {
            alert('URL del PDF no disponible');
            return;
        }

        // Abrir modal (Bootstrap 5)
        const modal = new bootstrap.Modal(pdfPreviewModal);
        modal.show();

        // Mostrar loader
        pdfLoader.style.display = 'block';
        pdfPreviewIframe.style.display = 'none';

        // Establecer src del iframe
        pdfPreviewIframe.src = pdfUrl;
    });

    // Evento de carga del iframe
    if (pdfPreviewIframe) {
        pdfPreviewIframe.addEventListener('load', function () {
            // Ocultar loader, mostrar iframe
            pdfLoader.style.display = 'none';
            pdfPreviewIframe.style.display = 'block';
        });
    }

    // Botón Imprimir
    if (btnPrintIframe) {
        btnPrintIframe.addEventListener('click', function () {
            try {
                pdfPreviewIframe.contentWindow.print();
            } catch (error) {
                console.error('Error al imprimir:', error);
                alert('No se pudo imprimir. Descargue el PDF e imprímalo manualmente.');
            }
        });
    }

    // Botón Descargar
    if (btnDownloadPdf) {
        btnDownloadPdf.addEventListener('click', function () {
            const pdfUrl = pdfPreviewIframe.src;
            if (!pdfUrl) {
                alert('No hay PDF para descargar');
                return;
            }

            const link = document.createElement('a');
            link.href = pdfUrl;
            link.download = 'Tareas_Activas.pdf';
            link.click();
        });
    }

    // Limpiar iframe al cerrar modal
    pdfPreviewModal.addEventListener('hidden.bs.modal', function () {
        pdfPreviewIframe.src = '';
        pdfLoader.style.display = 'none';
    });
})();
```

---

## 📄 Estructura del PDF Generado

### Diseño del Documento

```
┌─────────────────────────────────────────────┐
│                                             │
│     Listado de todas mis tareas             │
│                                             │
├─────────────────────────────────────────────┤
│                                             │
│ ► Etapa: Preparación del Apiario            │
│ ┌───────────────────────────────────────┐   │
│ │ Nombre  │ Inicio │ Límite │ Prioridad│   │
│ ├─────────┼────────┼────────┼──────────┤   │
│ │ Tarea 1 │ 01/01  │ 15/01  │ ● Alta  │   │
│ │ Tarea 2 │ 02/01  │ 20/01  │ ● Media │   │
│ └─────────┴────────┴────────┴──────────┘   │
│                                             │
│ ► Etapa: Inspección de Colmenas            │
│ ┌───────────────────────────────────────┐   │
│ │ Nombre  │ Inicio │ Límite │ Prioridad│   │
│ ├─────────┼────────┼────────┼──────────┤   │
│ │ Tarea 3 │ 16/01  │ 31/01  │ ● Urgen.│   │
│ └─────────┴────────┴────────┴──────────┘   │
│                                             │
├─────────────────────────────────────────────┤
│ Generado el 02/12/2025 14:30               │
│ Sistema de Gestión Apícola                  │
└─────────────────────────────────────────────┘
```

### Elementos Visuales

**Indicadores de Prioridad:**
- 🔴 **Urgente**: Círculo rojo
- 🟡 **Alta**: Círculo amarillo
- 🟢 **Media**: Círculo verde
- 🔵 **Baja**: Círculo azul claro

**Secciones:**
1. **Título principal**: "Listado de todas mis tareas"
2. **Bloques por etapa**: Cada etapa en sección separada
3. **Tablas de tareas**: Por cada etapa con sus tareas
4. **Footer**: Fecha de generación

---

## 🎨 Estilos del PDF

### CSS Personalizado

```css
/* Ubicación: resources/views/documents/tareas-todas.blade.php */

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
    margin: 20px;
    color: #333;
}

h1 {
    text-align: center;
    font-size: 18px;
    margin-bottom: 30px;
}

.tarea-general {
    background-color: #f4f4f4;
    padding: 8px;
    margin-top: 20px;
    border-left: 4px solid #0c5460;
    font-weight: bold;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 5px;
}

th, td {
    border: 1px solid #aaa;
    padding: 6px;
    text-align: left;
}

th {
    background-color: #ddd;
}

/* Indicadores de prioridad con círculos */
.prio-dot {
    display: inline-block;
    width: 0.9em;
    height: 0.9em;
    border-radius: 50%;
    margin-right: 0.45em;
    vertical-align: middle;
}

.prioridad-urgente .prio-dot { background-color: red; }
.prioridad-alta .prio-dot { background-color: yellow; }
.prioridad-media .prio-dot { background-color: green; }
.prioridad-baja .prio-dot { background-color: lightblue; }
```

---

## 💻 Implementación Backend

### Controlador: DocumentController

```php
/**
 * Genera un PDF con todas las subtareas activas del usuario
 * 
 * @return \Illuminate\Http\Response
 */
public function imprimirTodasSubtareas()
{
    // 1. Obtener usuario autenticado
    $user = Auth::user();
    
    // 2. Consultar subtareas activas con relación a tarea general
    $subtareas = SubTarea::with('tareaGeneral')
        ->where('user_id', $user->id)
        ->where('archivada', false)
        ->get();

    // 3. Log para debugging
    Log::info('Generando PDF de tareas', [
        'user_id' => $user->id,
        'total_subtareas' => $subtareas->count()
    ]);

    // 4. Normalizar y eliminar duplicados
    $subtareas = $subtareas
        ->map(function ($t) {
            // Crear clave única normalizada
            $t->nombre_key = Str::of($t->nombre)->squish()->lower();
            return $t;
        })
        ->unique('nombre_key')
        ->values();

    // 5. Obtener fecha de generación
    $fechaGeneracion = Carbon::now()
        ->setTimezone('America/Santiago')
        ->format('d/m/Y H:i');

    // 6. Configurar y generar PDF
    $pdf = Pdf::loadView('documents.tareas-todas', 
        compact('subtareas', 'fechaGeneracion'));
    
    $pdf->setPaper('A4', 'portrait');
    $pdf->setOptions([
        'isHtml5ParserEnabled' => true,
        'isPhpEnabled' => false,
        'defaultFont' => 'DejaVu Sans',
        'enable_remote' => false,
    ]);

    // 7. Retornar como stream (para vista previa en iframe)
    return $pdf->stream('Tareas_Activas.pdf');
}
```

### Características de Seguridad

```php
// ✅ Solo tareas del usuario autenticado
->where('user_id', $user->id)

// ✅ Solo tareas activas
->where('archivada', false)

// ✅ Middleware de autenticación en ruta
->middleware('auth')

// ✅ Opciones seguras de DomPDF
'isPhpEnabled' => false,
'enable_remote' => false,
```

---

## 📱 Diseño Responsive del Modal

### Clases de Bootstrap Utilizadas

```html
<div class="modal-dialog modal-xl modal-fullscreen-sm-down">
```

| Breakpoint | Comportamiento |
|------------|----------------|
| **sm-down** (< 576px) | Modal a pantalla completa |
| **md+** (≥ 768px) | Modal extra grande (90% ancho) |
| **lg+** (≥ 992px) | Modal extra grande (80% ancho) |
| **xl+** (≥ 1200px) | Modal extra grande (1140px) |

### Altura Dinámica del Iframe

```css
iframe {
    width: 100%;
    height: 100%;
    min-height: 480px;
    border: none;
}
```

---

## 🔍 Vista del PDF en Blade

### Estructura de la Vista

```blade
<!-- resources/views/documents/tareas-todas.blade.php -->

@forelse ($subtareas->groupBy('tareaGeneral.nombre') as $nombreGeneral => $subtareasAgrupadas)
    <!-- Cabecera de etapa -->
    <div class="tarea-general">{{ $nombreGeneral }}</div>
    
    <!-- Tabla de tareas de esta etapa -->
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Fecha Inicio</th>
                <th>Fecha Límite</th>
                <th>Prioridad</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($subtareasAgrupadas as $tarea)
                <tr>
                    <td>{{ $tarea->nombre }}</td>
                    <td>{{ Carbon::parse($tarea->fecha_inicio)->format('d/m/Y') }}</td>
                    <td>{{ Carbon::parse($tarea->fecha_limite)->format('d/m/Y') }}</td>
                    <td class="prioridad-{{ strtolower($tarea->prioridad ?? 'media') }}">
                        <span class="prio">
                            <span class="prio-dot"></span>
                            {{ ucfirst($tarea->prioridad ?? 'Media') }}
                        </span>
                    </td>
                    <td class="estado">{{ ucfirst($tarea->estado ?? 'Pendiente') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@empty
    <p>No se encontraron subtareas para mostrar.</p>
@endforelse
```

### Agrupación por Etapa

```php
// Laravel Eloquent Collection
$subtareas->groupBy('tareaGeneral.nombre')

// Resultado:
[
    'Preparación del Apiario' => [
        SubTarea { id: 1, nombre: 'Tarea 1' },
        SubTarea { id: 2, nombre: 'Tarea 2' }
    ],
    'Inspección de Colmenas' => [
        SubTarea { id: 3, nombre: 'Tarea 3' }
    ]
]
```

---

## 🐛 Solución de Problemas

### El PDF no se genera

**Verificar:**

1. **Paquete DomPDF instalado:**
```bash
composer require barryvdh/laravel-dompdf
```

2. **Configuración en config/app.php:**
```php
'providers' => [
    Barryvdh\DomPDF\ServiceProvider::class,
],

'aliases' => [
    'PDF' => Barryvdh\DomPDF\Facade\Pdf::class,
],
```

3. **Ruta registrada correctamente:**
```bash
php artisan route:list | grep imprimirTodas
```

### El modal no se abre

**Verificar:**

1. **Bootstrap 5 cargado:**
```html
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js"></script>
```

2. **ID del botón correcto:**
```javascript
const btnPreviewPdf = document.getElementById('btn-preview-pdf');
console.log('Botón encontrado:', btnPreviewPdf);
```

3. **Evento registrado:**
```javascript
btnPreviewPdf.addEventListener('click', function() {
    console.log('Click detectado');
});
```

### El iframe no muestra el PDF

**Posibles causas:**

1. **Navegador bloquea contenido:**
```javascript
// Verificar en consola del navegador
console.error('Blocked loading mixed active content');
```

**Solución:** Usar HTTPS o configurar excepciones del navegador

2. **Error 500 en el servidor:**
```bash
# Ver logs de Laravel
tail -f storage/logs/laravel.log
```

3. **Permisos de archivo:**
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### El botón "Imprimir" no funciona

**Verificar:**

1. **Iframe tiene contenido:**
```javascript
console.log('Iframe src:', pdfPreviewIframe.src);
console.log('Iframe contentWindow:', pdfPreviewIframe.contentWindow);
```

2. **Política CORS:**
```
Error: SecurityError: Blocked a frame with origin
```

**Solución:** El PDF debe servirse desde el mismo dominio

3. **Navegador soporta print():**
```javascript
if (pdfPreviewIframe.contentWindow && 
    typeof pdfPreviewIframe.contentWindow.print === 'function') {
    pdfPreviewIframe.contentWindow.print();
} else {
    alert('Su navegador no soporta impresión directa');
}
```

### Tareas duplicadas en el PDF

**Causa:** Registros duplicados en la base de datos

**Solución implementada:**
```php
$subtareas = $subtareas
    ->map(function ($t) {
        $t->nombre_key = Str::of($t->nombre)->squish()->lower();
        return $t;
    })
    ->unique('nombre_key')
    ->values();
```

### El PDF se descarga en lugar de mostrarse

**Causa:** Método `download()` en lugar de `stream()`

**Correcto:**
```php
// ✅ Para vista previa
return $pdf->stream('Tareas_Activas.pdf');

// ❌ Para descarga directa
return $pdf->download('Tareas_Activas.pdf');
```

---

## 🎯 Mejores Prácticas

### Performance

1. **Eager Loading:**
```php
SubTarea::with('tareaGeneral') // ✅ N+1 evitado
    ->where('user_id', $user->id)
    ->get();
```

2. **Filtrado en BD:**
```php
->where('archivada', false) // ✅ Filtrar en BD
// No hacer: $subtareas->filter(fn($t) => !$t->archivada)
```

3. **Caché de PDFs (opcional):**
```php
$cacheKey = "pdf_tareas_{$user->id}_" . md5(json_encode($subtareas->pluck('id')));

return Cache::remember($cacheKey, 300, function () use ($pdf) {
    return $pdf->stream();
});
```

### Seguridad

1. **Autenticación obligatoria:**
```php
->middleware('auth')
```

2. **Validar pertenencia:**
```php
->where('user_id', $user->id)
```

3. **Deshabilitar ejecución PHP:**
```php
'isPhpEnabled' => false
```

### Experiencia de Usuario

1. **Loader visible:**
```javascript
pdfLoader.style.display = 'block';
```

2. **Mensajes claros:**
```javascript
alert('No se pudo cargar el PDF. Por favor, intente nuevamente.');
```

3. **Limpiar estado al cerrar:**
```javascript
pdfPreviewModal.addEventListener('hidden.bs.modal', function () {
    pdfPreviewIframe.src = '';
});
```

---

## 📝 Casos de Uso

### Caso 1: Imprimir Tareas del Mes

**Flujo:**
1. Usuario abre vista de tareas
2. Click en botón "Imprimir"
3. Vista previa del PDF en modal
4. Click en "Imprimir"
5. Diálogo de impresión del navegador
6. Selección de impresora y configuración
7. Impresión física del documento

### Caso 2: Descargar para Compartir

**Flujo:**
1. Usuario abre vista de tareas
2. Click en botón "Imprimir"
3. Vista previa del PDF
4. Click en "Descargar"
5. PDF descargado a carpeta local
6. Usuario puede compartir el archivo

### Caso 3: Revisión Sin Impresión

**Flujo:**
1. Usuario abre vista de tareas
2. Click en botón "Imprimir"
3. Vista previa del PDF
4. Usuario revisa la información
5. Click en "Cerrar"
6. Regresa a la vista de tareas

---

## 🔗 Archivos Relacionados

- **Vista principal**: `resources/views/tareas/index.blade.php`
- **Plantilla PDF**: `resources/views/documents/tareas-todas.blade.php`
- **Controlador**: `app/Http/Controllers/DocumentController.php`
- **JavaScript**: `public/js/components/home-user/tareas.js`
- **Rutas**: `routes/web.php`
- **Modelo**: `app/Models/SubTarea.php`
- **Paquete DomPDF**: `vendor/barryvdh/laravel-dompdf`

---

## 📚 Tecnologías Utilizadas

| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| **Laravel** | 10.x | Framework PHP backend |
| **DomPDF** | 2.x | Generación de PDFs |
| **Bootstrap** | 5.x | Modal responsive |
| **JavaScript** | ES6+ | Interactividad frontend |
| **Blade** | - | Motor de plantillas |
| **Carbon** | 2.x | Manejo de fechas |

---

## 📞 Referencias Adicionales

Para más información sobre otros componentes del sistema:
- **Vista Agenda**: `README_AGENDA.md`
- **Sistema de Prioridades**: `README_PRIORIDADES_AUTOMATICAS.md`
- **Google Calendar**: `GOOGLE_CALENDAR_SETUP.md`

---

## 📈 Futuras Mejoras

### Posibles Extensiones

1. **Filtros personalizados:**
   - Por rango de fechas
   - Por prioridad específica
   - Por estado de tarea

2. **Personalización del PDF:**
   - Logo del usuario
   - Colores corporativos
   - Campos personalizados

3. **Formatos adicionales:**
   - Exportar a Excel
   - Exportar a CSV
   - Exportar a JSON

4. **Plantillas múltiples:**
   - Vista resumida
   - Vista detallada
   - Vista por fechas

5. **Compartir directamente:**
   - Enviar por email
   - Compartir link temporal
   - Integración con Dropbox/Drive

---

**Estado**: Sistema completamente funcional y documentado  
**Fecha**: Diciembre 2025
**Ultima Modificación**: Diciembre 2025
**Versión**: 1.0
