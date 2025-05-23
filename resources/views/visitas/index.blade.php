@extends('layouts.app')

@push('styles')
    <link href="{{ asset('css/components/home-user/cuaderno-de-campo.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="apiary-dashboard">
        <!-- Encabezado con efecto parallax sutil -->
        <header class="dashboard-header">
            <div class="header-backdrop"></div>
            <div class="header-content">
                <div class="header-icon-wrapper">
                    <div class="header-icon">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
                <div class="header-text">
                    <h1>Cuaderno de Campo</h1>
                    <p>Gestiona y documenta el manejo y observaciones de tus apiarios</p>
                </div>
            </div>
        </header>

        <!-- Panel de control con búsqueda y filtros -->
        <div class="control-panel">
            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="apiarySearch" placeholder="Buscar apiario por nombre..."
                        onkeyup="filterApiaries()">
                    <button class="clear-search" onclick="clearSearch()" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="search-results" id="searchResults">
                    <span id="resultsCount"></span>
                </div>
            </div>

            <div class="view-controls">
                <button class="view-btn active" data-view="card" title="Vista de tarjetas">
                    <i class="fas fa-th-large"></i>
                </button>
                <button class="view-btn" data-view="list" title="Vista de lista">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>

        <!-- Contenedor principal de apiarios -->
        <div class="apiary-section">
            @if($apiarios->count() > 0)
                <div class="apiaries-container card-view" id="apiariesContainer">
                    @foreach($apiarios as $apiario)
                        <div class="apiary-card" data-name="{{ strtolower($apiario->nombre) }}">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-archive"></i>
                                </div>
                                <div class="card-title">
                                    <h2>{{ $apiario->nombre }}</h2>
                                    <span class="card-id">#{{ $apiario->id }}</span>
                                </div>
                                <div class="card-menu">
                                    <button class="menu-trigger">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="card-menu-dropdown">
                                        {{-- Enlace Editar --}}
                                        {{-- Asegúrate de que la variable $apiario esté disponible en tu vista --}}
                                        <a href="{{ route('apiarios.edit', $apiario->id) }}" class="menu-item">
                                            <i class="fas fa-edit"></i> Editar apiario
                                        </a>
                                        {{-- Enlace Ver Historial (este ya estaba correcto) --}}
                                        <a href="{{ route('visitas.historial', $apiario->id) }}" class="menu-item">
                                            <i class="fas fa-history"></i> Ver historial
                                        </a>
                                        <hr class="menu-divider">
                                    
                                        {{-- Formulario para la acción de eliminar --}}
                                        {{-- Usa la ruta 'apiarios.destroy' y el método DELETE simulado --}}
                                        <form action="{{ route('apiarios.destroy', $apiario->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este apiario? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE') {{-- Esto le dice a Laravel que es una solicitud DELETE --}}
                                            {{-- Usamos un botón con los estilos del elemento del menú para que se vea como un enlace --}}
                                            <button type="submit" class="menu-item text-danger" style="border: none; background: none; width: 100%; text-align: left; padding: 0.5rem 1rem; cursor: pointer;">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="card-stats">
                                    <div class="stat">
                                        <div class="stat-icon">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <div class="stat-content">
                                            <span class="stat-label">Última visita</span>
                                            <span class="stat-value">{{-- Fecha última visita --}}</span>
                                        </div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-icon">
                                            <i class="fas fa-clipboard-list"></i>
                                        </div>
                                        <div class="stat-content">
                                            <span class="stat-label">Registros</span>
                                            <span class="stat-value">{{-- Número de registros --}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-actions">
                                    <button class="action-btn primary" data-bs-toggle="modal"
                                        data-bs-target="#modalRegistro{{ $apiario->id }}">
                                        <i class="fas fa-plus"></i>
                                        <span>Nuevo registro</span>
                                    </button>

                                    <div class="action-dropdown">
                                        <button type="button" class="action-btn secondary dropdown-toggle"
                                            id="downloadBtn{{ $apiario->id }}">
                                            <i class="fas fa-download"></i>
                                            <span>Descargar</span>
                                        </button>
                                        <div class="dropdown-panel" id="downloadPanel{{ $apiario->id }}">
                                            <div class="dropdown-header">
                                                <i class="fas fa-file-export"></i>
                                                <span>Exportar documentos</span>
                                            </div>
                                            <div class="dropdown-options">
                                                <a href="{{ route('generate.document.visitas', $apiario->id) }}"
                                                    class="dropdown-option" data-download="visitas">
                                                    <div class="option-icon"><i class="fas fa-clipboard-list"></i></div>
                                                    <div class="option-content">
                                                        <span class="option-title">Registro de Visitas</span>
                                                        <span class="option-desc">Historial completo de visitas</span>
                                                    </div>
                                                </a>
                                                <a href="{{ route('generate.document.inspeccion', $apiario->id) }}"
                                                    class="dropdown-option">
                                                    <div class="option-icon"><i class="fas fa-search"></i></div>
                                                    <div class="option-content">
                                                        <span class="option-title">Registro de Inspección</span>
                                                        <span class="option-desc">Detalles de inspecciones</span>
                                                    </div>
                                                </a>
                                                <a href="{{ route('generate.document.medicamentos', $apiario->id) }}"
                                                    class="dropdown-option">
                                                    <div class="option-icon"><i class="fas fa-syringe"></i></div>
                                                    <div class="option-content">
                                                        <span class="option-title">Registro de Medicamentos</span>
                                                        <span class="option-desc">Tratamientos aplicados</span>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal para registro -->
                        <div class="modal fade custom-modal" id="modalRegistro{{ $apiario->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <div class="modal-header-content">
                                            <div class="modal-icon">
                                                <i class="fas fa-clipboard-list"></i>
                                            </div>
                                            <div class="modal-title-container">
                                                <h5 class="modal-title">Nuevo registro</h5>
                                                <p class="modal-subtitle">{{ $apiario->nombre }}</p>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="modal-text">Selecciona el tipo de registro que deseas realizar:</p>

                                        <div class="registration-options">
                                            <a href="{{ url('visitas/create1/' . $apiario->id) }}" class="registration-option">
                                                <div class="option-icon"><i class="fas fa-clipboard"></i></div>
                                                <div class="option-content">
                                                    <span class="option-title">Registro de Visitas</span>
                                                    <p class="option-description">Documentación general de visitas al apiario</p>
                                                </div>
                                                <div class="option-indicator"><i class="fas fa-chevron-right"></i></div>
                                            </a>

                                            <a href="{{ url('visitas/create/' . $apiario->id) }}"
                                                class="registration-option featured">
                                                <div class="option-icon"><i class="fas fa-search"></i></div>
                                                <div class="option-content">
                                                    <span class="option-title">Inspección de Apiario</span>
                                                    <p class="option-description">Evaluación detallada de colmenas y marcos</p>
                                                </div>
                                                <div class="option-indicator"><i class="fas fa-chevron-right"></i></div>
                                            </a>

                                            <a href="{{ url('visitas/create2/' . $apiario->id) }}" class="registration-option">
                                                <div class="option-icon"><i class="fas fa-syringe"></i></div>
                                                <div class="option-content">
                                                    <span class="option-title">Uso de Medicamentos</span>
                                                    <p class="option-description">Control de tratamientos y medicamentos aplicados
                                                    </p>
                                                </div>
                                                <div class="option-indicator"><i class="fas fa-chevron-right"></i></div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Mensaje de no resultados en la búsqueda -->
                <div class="empty-state" id="noResults" style="display: none;">
                    <div class="empty-state-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="empty-state-title">No se encontraron apiarios</h3>
                    <p class="empty-state-text">No hay apiarios que coincidan con tu búsqueda. Intenta con otro término.</p>
                    <button class="btn-reset" onclick="clearSearch()">
                        <i class="fas fa-redo"></i> Reiniciar búsqueda
                    </button>
                </div>

            @else
                <!-- Estado vacío - sin apiarios -->
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-archive"></i>
                    </div>
                    <h3 class="empty-state-title">No hay apiarios registrados</h3>
                    <p class="empty-state-text">Aún no has registrado ningún apiario en el sistema. Crea uno nuevo para
                        comenzar.</p>
                    <a href="{{ route('apiarios.create') }}" class="btn-primary">
                        <i class="fas fa-plus"></i> Crear nuevo apiario
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        let activeDropdown = null;
        let activeMenu = null;

        function filterApiaries() {
            const searchInput = document.getElementById('apiarySearch');
            const clearButton = document.querySelector('.clear-search');
            const filter = searchInput.value.toLowerCase().trim();
            const apiaryCards = document.querySelectorAll('.apiary-card');
            const noResults = document.getElementById('noResults');
            const resultsCount = document.getElementById('resultsCount');
            const container = document.getElementById('apiariesContainer');

            clearButton.style.display = filter ? 'flex' : 'none';

            let count = 0;

            apiaryCards.forEach(card => {
                const apiaryName = card.getAttribute('data-name');
                if (apiaryName.includes(filter)) {
                    card.style.display = '';
                    count++;

                    // Reiniciar la animación para que aparezca gradualmente
                    card.style.animation = 'none';
                    card.offsetHeight; // Forzar reflow
                    card.style.animation = 'cardFadeIn 0.4s forwards';
                } else {
                    card.style.display = 'none';
                }
            });

            // Actualizar contador de resultados
            if (filter) {
                resultsCount.textContent = `${count} ${count === 1 ? 'apiario encontrado' : 'apiarios encontrados'}`;
            } else {
                resultsCount.textContent = '';
            }

            // Mostrar mensaje si no hay resultados
            if (count === 0) {
                noResults.style.display = 'block';
                container.style.display = 'none';
            } else {
                noResults.style.display = 'none';
                container.style.display = '';
            }
        }

        // Limpiar búsqueda
        function clearSearch() {
            const searchInput = document.getElementById('apiarySearch');
            searchInput.value = '';
            filterApiaries();
            searchInput.focus();
        }

        // Alternar entre vistas de tarjeta y lista
        function toggleView(viewType) {
            const container = document.getElementById('apiariesContainer');
            const viewButtons = document.querySelectorAll('.view-btn');

            // Actualizar botones
            viewButtons.forEach(btn => {
                btn.classList.toggle('active', btn.getAttribute('data-view') === viewType);
            });

            // Actualizar vista
            container.className = `apiaries-container ${viewType}-view`;
        }

        // Manejar dropdowns de descargas
        function toggleDropdown(id, event) {
            // Prevenir cualquier comportamiento predeterminado
            event.preventDefault();

            const dropdown = document.getElementById(`downloadPanel${id}`);
            const container = dropdown.closest('.action-dropdown');

            // Cerrar dropdown activo si existe
            if (activeDropdown && activeDropdown !== container) {
                activeDropdown.classList.remove('active');
            }

            // Alternar estado del dropdown actual
            container.classList.toggle('active');

            // Actualizar referencia al dropdown activo
            activeDropdown = container.classList.contains('active') ? container : null;

            // Importante: detener propagación del evento
            event.stopPropagation();
        }

        // Manejar menús de tarjeta
        function toggleCardMenu(element, event) {
            const menuContainer = element.closest('.card-menu');

            // Cerrar menú activo si existe
            if (activeMenu && activeMenu !== menuContainer) {
                activeMenu.classList.remove('active');
            }

            // Alternar estado del menú actual
            menuContainer.classList.toggle('active');

            // Actualizar referencia al menú activo
            activeMenu = menuContainer.classList.contains('active') ? menuContainer : null;

            // Importante: detener propagación del evento
            event.stopPropagation();
        }

        // Cerrar dropdowns al hacer clic en cualquier parte del documento
        document.addEventListener('click', function (e) {
            // Cerrar el dropdown de descargas si está activo
            if (activeDropdown) {
                activeDropdown.classList.remove('active');
                activeDropdown = null;
            }

            // Cerrar el menú de tarjeta si está activo
            if (activeMenu) {
                activeMenu.classList.remove('active');
                activeMenu = null;
            }
        });

        // Configuración al cargar el documento
        document.addEventListener('DOMContentLoaded', function () {
            // Evitar que los clics dentro de los menús los cierren
            document.querySelectorAll('.card-menu-dropdown, .dropdown-panel').forEach(menu => {
                menu.addEventListener('click', function (e) {
                    e.stopPropagation();
                });
            });

            // Configurar botones de vista
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    toggleView(this.getAttribute('data-view'));
                });
            });

            // Configurar botones de menú
            document.querySelectorAll('.menu-trigger').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    toggleCardMenu(this, e);
                });
            });

            // Actualizar los event listeners para los botones de descarga
            document.querySelectorAll('[id^="downloadBtn"]').forEach(btn => {
                const id = btn.id.replace('downloadBtn', '');
                btn.addEventListener('click', function (e) {
                    toggleDropdown(id, e);
                });
            });

            // Agregar manejo específico para los enlaces de descarga
            document.querySelectorAll('.dropdown-option').forEach(option => {
                option.addEventListener('click', function (e) {
                    // No prevenir el comportamiento predeterminado aquí para permitir la navegación
                    // Solo detenemos la propagación para evitar que se cierre el menú inmediatamente
                    e.stopPropagation();
                });
            });

            // Efecto parallax para el encabezado
            const headerElement = document.querySelector('.dashboard-header');
            const headerContent = document.querySelector('.header-content');

            window.addEventListener('scroll', function () {
                const scrollPosition = window.scrollY;
                if (scrollPosition < 300) {
                    headerElement.style.backgroundPosition = `0 ${scrollPosition * 0.15}px`;
                    headerContent.style.transform = `translateY(${scrollPosition * 0.1}px)`;
                }
            });

            // Efecto de revelación para títulos y elementos al hacer scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Agregar clase para observar elementos
            document.querySelectorAll('.apiary-card, .dashboard-header, .control-panel').forEach(el => {
                el.classList.add('reveal-element');
                observer.observe(el);
            });

            // Efecto de hover 3D para tarjetas
            document.querySelectorAll('.apiary-card').forEach(card => {
                card.addEventListener('mousemove', function (e) {
                    const rect = this.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;

                    const xPercent = x / rect.width;
                    const yPercent = y / rect.height;

                    const rotateX = (0.5 - yPercent) * 8; // Rotación de -4 a 4 grados
                    const rotateY = (xPercent - 0.5) * 8; // Rotación de -4 a 4 grados

                    this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
                });

                card.addEventListener('mouseleave', function () {
                    this.style.transform = '';
                });
            });
        });
    </script>
@endsection