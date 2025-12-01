<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token()}}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- CSS y JS de Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />

    <!-- Incluye jQuery y jQuery UI -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Incluir Quill desde su CDN -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

    <!-- CSS de DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@8.0.0/dist/css/shepherd.css" />
    @stack('styles')

    <!-- Flatpickr CSS y JS Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>


    <!-- reactividad-->
    <script src="//unpkg.com/alpinejs" defer></script>

</head>

<body>

    @auth
        <script>
            window.appLocale = "{{ app()->getLocale() }}";
            window.appDateFormat = "{{ config('app.date_format', 'DD/MM/YYYY') }}";
            window.translations = {!! json_encode(__('messages')) !!};

            window.trans = function (key) {
                return window.translations[key] || key;
            };
        </script>
    @endauth


    <!-- Axios desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Header -->
    @include('partials.header')

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @include('partials.sidebar')
            <!-- Contenido Principal -->
        </div>
    </div>

    <!-- Chat flotante -->
    <div id="virtual-assistant-chat" class="assistant-chat" style="display:none;">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Chat con B-MaIA</h5>
                <button id="close-chat" class="btn-close" onclick="toggleChat()"
                    style="color:aliceblue;background-color:orange;" aria-label="Close">
                    <span class="fa fa-times"></span>
                </button>
            </div>
            <div class="card-body chat-window" id="chat-messages">
                <!-- Los mensajes se cargarán aquí dinámicamente -->
            </div>
            <div class="card-footer">
                <form id="chat-form" class="d-flex gap-2">
                    <input type="text" class="form-control" id="message-input" placeholder="Escribe tu mensaje...">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/shepherd.js@8.0.0/dist/js/shepherd.min.js"></script>

    {{-- Sistema de Notificaciones de Alertas --}}
    @auth
    <script>
        // Configurar Toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "8000",
            "extendedTimeOut": "3000"
        };

        // IDs de alertas ya mostradas
        let alertasVistas = JSON.parse(localStorage.getItem('alertasVistas_{{ auth()->id() }}') || '[]');
        let ultimaVerificacion = null;

        // Verificar nuevas alertas cada 30 segundos
        function verificarNuevasAlertas() {
            fetch('/alerts', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(alertas => {
                // Filtrar solo alertas de cambio de prioridad recientes (últimas 24 horas)
                const ahora = new Date();
                const hace24h = new Date(ahora.getTime() - (24 * 60 * 60 * 1000));
                
                const alertasNuevas = alertas.filter(alerta => {
                    const fechaAlerta = new Date(alerta.created_at);
                    return alerta.type === 'priority_change' && 
                           !alertasVistas.includes(alerta.id) &&
                           fechaAlerta > hace24h;
                });

                // Mostrar cada alerta nueva
                alertasNuevas.forEach(alerta => {
                    mostrarNotificacionAlerta(alerta);
                    alertasVistas.push(alerta.id);
                });

                // Guardar IDs vistos en localStorage
                localStorage.setItem('alertasVistas_{{ auth()->id() }}', JSON.stringify(alertasVistas));
            })
            .catch(error => console.error('Error verificando alertas:', error));
        }

        function mostrarNotificacionAlerta(alerta) {
            const iconos = {
                'urgente': '🔴',
                'alta': '🟡',
                'media': '🟢',
                'baja': '🔵'
            };

            const icono = iconos[alerta.priority] || '⚠️';
            const mensaje = `${icono} ${alerta.description}`;

            // Tipo de notificación según prioridad
            const tipoNotificacion = {
                'urgente': 'error',
                'alta': 'warning',
                'media': 'info',
                'baja': 'info'
            };

            toastr[tipoNotificacion[alerta.priority] || 'info'](mensaje, alerta.title);
        }

        // Verificar al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            verificarNuevasAlertas();
            
            // Verificar cada 30 segundos
            setInterval(verificarNuevasAlertas, 30000);
        });
    </script>
    @endauth

    <!-- JavaScript de DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.7.3/proj4.js"></script>

    <script src="/js/scripts.js"></script>
    @yield('optional-scripts')
    @stack('scripts')

    @php
        $userId = auth()->check() ? auth()->user()->id : null;
        $secret = 'c8d90s0wkuati88jobrhewy4g8v3dcf3';
        $userHash = $userId ? hash_hmac('sha256', $userId, $secret) : null;
    @endphp
</body>

</html>