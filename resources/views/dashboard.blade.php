@extends('layouts.app')

@section('title', 'B-Maia - Panel de Indicadores')

@section('content')

    @php
        // Opcional: si prefieres filtrar por los tres tipos “buenos”
        $tiposOk = [
            'Visita General',
            'Inspección de Visita',
            'Uso de Medicamentos',
        ];
    @endphp

    <div class="container dashboard-container">
        <!-- Header del Dashboard -->
        <div class="row dashboard-header animate">
            <div class="col-md-12 text-center">
                <h1 class="dashboard-title">Panel de Indicadores</h1>
                <p class="dashboard-subtitle">Visualiza y analiza el estado actual de tus apiarios y colmenas. Obtén
                    información valiosa para optimizar la producción y salud de tus abejas.</p>
            </div>
        </div>

        <!-- Tarjetas de información clave -->
        <div class="row mb-2">
            <!-- Total de Apiarios -->
            <div class="col-12 col-md-4 animate delay-1">
                <div class="info-card info-card-sm">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="info-card-icon">
                            <i class="fas fa-archive"></i>
                        </div>
                        <h3 class="info-card-title">Total de Apiarios</h3>
                        <div class="info-card-value">
                            {{ $apiariosBase + $apiariosTemporales }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Apiarios Base -->
            <div class="col-6 col-md-4 animate delay-3">
                <div class="info-card info-card-sm">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="info-card-icon">
                            <i class="fas fa-route"></i>
                        </div>
                        <h3 class="info-card-title">Apiarios Base</h3>
                        <div class="info-card-value">
                            {{ $apiariosBase }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Apiarios Temporales -->
            <div class="col-6 col-md-4 animate delay-4">
                <div class="info-card info-card-sm">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="info-card-icon">
                            <i class="fas fa-truck-moving"></i>
                        </div>
                        <h3 class="info-card-title">Apiarios Temporales</h3>
                        <div class="info-card-value">
                            {{ $apiariosTemporales }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Indicadores -->
        <div class="row mt-2 mb-4 indicadores-row">
            <div class="indicador-boton animate delay-1 active" data-indicador="global">
                <i class="fas fa-globe"></i>
                <span class="d-none d-md-block">Productividad Global</span>
            </div>
            <div class="indicador-boton animate delay-2" data-indicador="por-apiario">
                <i class="fas fa-chart-line"></i>
                <span class="d-none d-md-block">Productividad por Apiario</span>
            </div>
            <div class="indicador-boton animate delay-3" data-indicador="plan-anual">
                <i class="fas fa-calendar-alt"></i>
                <span class="d-none d-md-block">Plan de Trabajo Anual</span>
            </div>
            <div class="indicador-boton animate delay-4" data-indicador="movimiento-colmenas">
                <i class="fas fa-truck-moving"></i>
                <span class="d-none d-md-block">Movimiento de Colmenas</span>
            </div>
            <div class="indicador-boton animate delay-5" data-indicador="gestion-apiario">
                <i class="fas fa-tasks"></i>
                <span class="d-none d-md-block">Gestión por Apiario</span>
            </div>
        </div>

        <!-- Botones para ver gráficos del indicador -->
        <div class="text-center my-4">
            <div class="w-100 d-flex justify-content-between align-items-center">
                <button class="btn btn-warning boton-modal-indicador d-none" data-indicador="global" data-bs-toggle="modal" data-bs-target="#modalGraficosIndicador">
                    <i class="fas fa-chart-line"></i> 
                    <span class="d-none d-md-inline">
                        Ver Indicador Global
                    </span>
                </button>

                <button class="btn btn-warning animate" id="btnDescargarPDFGlobal">
                    <i class="fas fa-file-pdf"></i>
                    <span class="d-none d-md-inline">
                        Generar PDF
                    </span>
                </button>
            </div>

            <button class="btn btn-warning boton-modal-indicador d-none" data-indicador="por-apiario" data-bs-toggle="modal" data-bs-target="#modalGraficosIndicador">
                <i class="fas fa-chart-line"></i> 
                <span class="d-none d-md-inline">
                    Ver Indicador por Apiario
                </span>
            </button>

            <button class="btn btn-warning boton-modal-indicador d-none" data-indicador="plan-anual" data-bs-toggle="modal" data-bs-target="#modalGraficosIndicador">
                <i class="fas fa-chart-line"></i> 
                <span class="d-none d-md-inline">
                    Ver Plan de Trabajo Anual
                </span>
            </button>

            <button class="btn btn-warning boton-modal-indicador d-none" data-indicador="movimiento-colmenas" data-bs-toggle="modal" data-bs-target="#modalGraficosIndicador">
                <i class="fas fa-chart-line"></i> 
                <span class="d-none d-md-inline">
                    Ver Movimiento de Colmenas
                </span>
            </button>

            <button class="btn btn-warning boton-modal-indicador d-none" data-indicador="gestion-apiario" data-bs-toggle="modal" data-bs-target="#modalGraficosIndicador">
                <i class="fas fa-chart-line"></i>
                <span class="d-none d-md-inline">
                    Ver Gestión por Apiario
                </span>
            </button>
        </div>
  
        <!-- Modal con todos los gráficos -->
        <div class="modal fade" id="modalGraficosIndicador" tabindex="-1" aria-labelledby="modalGraficosIndicadorLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark d-flex justify-content-between">
                    <h5 class="modal-title me-4" id="modalGraficosIndicadorLabel">
                        <i class="fas fa-chart-bar"></i> Gráficos del Indicador
                    </h5>

                    <button class="btn btn-dark btn-sm ms-4 d-none" id="btnDescargarPDF">
                      <i class="fa-solid fa-file-pdf"></i> Exportar PDF
                    </button>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Loader dentro del modal -->
                    <div id="modalLoader" class="modal-loader d-none">
                        <div class="modal-loader-content">
                            <div class="modal-loader-spinner"></div>
                            <p class="modal-loader-message">Cargando gráficos…</p>
                        </div>
                    </div>

                    <!-- Contenedor dinámico -->
                    <div id="contenedorModalGraficos" class="row"></div>

                    <!-- PAGINACION PARA LOS GRAFICOS -->
                    <div id="paginadorGraficos" class="text-center mt-3"></div>
                </div>

            </div>
          </div>
        </div>

        <!-- CONTENIDO DE LOS GRÁFICOS -->
        <!-- Indicador 1: Productividad Global -->
        <div id="indicador-global" class="charts-group" style="display: block;">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card chart-card shadow-sm animate">
                        <div class="card-body w-100">
                            <div id="colmenasPorApiarioChart" style="height: 300px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card chart-card shadow-sm animate">
                        <div class="card-body w-100">
                            <div id="tipoActividadApiariosChart" style="height: 300px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card chart-card shadow-sm animate">
                        <div class="card-body w-100">
                            <div id="visitasPorApiarioChart" style="height: 300px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card chart-card shadow-sm animate">
                        <div class="card-body w-100">
                            <div id="tratamientosPorApiarioChart" style="height: 300px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- INDICADOR 2: PRODUCTIVIDAD POR APIARIO -->
        <div id="indicador-por-apiario" class="charts-group" style="display:none;">
          <div class="row">

            <div class="col-md-6 mb-4">
                <div class="card chart-card shadow-sm animate">
                    <div class="card-body w-100">
                        <div id="colmenasPorEstadoChart" style="height:300px;width:100%;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card chart-card shadow-sm animate">
                    <div class="card-body w-100">
                        <div id="indiceMortalidadChart" style="height:300px;width:100%;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card chart-card shadow-sm animate">
                    <div class="card-body w-100">
                        <div id="tasaMortalidadChart" style="height:300px;width:100%;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card chart-card shadow-sm animate">
                    <div class="card-body w-100">
                        <div id="tasaEnfermasChart" style="height:300px;width:100%;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card chart-card shadow-sm animate">
                    <div class="card-body w-100">
                        <div id="tipoVisitasPorApiarioChart" style="height:300px;width:100%;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card chart-card shadow-sm animate">
                    <div class="card-body w-100">
                        <div id="tipoTratamientoPorApiarioChart" style="height:300px;width:100%;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card chart-card shadow-sm animate">
                    <div class="card-body w-100">
                        <div id="tratamientosVarroaChart" style="height:300px;width:100%;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card chart-card shadow-sm animate">
                    <div class="card-body w-100">
                        <div id="resguardoSanitarioChart" style="height:300px;width:100%;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card chart-card shadow-sm animate">
                    <div class="card-body w-100">
                        <div id="nutricionPorApiarioChart" style="height:300px;width:100%;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card chart-card shadow-sm animate">
                    <div class="card-body w-100">
                        <div id="tipoAlimentoPorApiarioChart" style="height:300px;width:100%;"></div>
                    </div>
                </div>
            </div>
          </div>
        </div>

        <!-- Indicador 3 : Plan de Trabajo Anual -->
        <div id="indicador-plan-anual" class="charts-group" style="display:none;">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card chart-card shadow-sm animate">
                        <div class="card-body w-100">
                            <div id="estadoTareasChart" style="height:300px;width:100%;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card chart-card shadow-sm animate">
                        <div class="card-body w-100">
                            <div id="cumplimientoPlanChart" style="height:300px;width:100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicador 4 : Movimiento de Colmenas -->
        <div id="indicador-movimiento-colmenas" class="charts-group" style="display:none;">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card chart-card shadow-sm animate">
                        <div class="card-body w-100">
                            <div id="movimientosMotivoChart" style="height:300px;width:100%;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card chart-card shadow-sm animate">
                        <div class="card-body w-100">
                            <div id="colmenasTransportadasChart" style="height:300px;width:100%;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card chart-card shadow-sm animate">
                        <div class="card-body w-100">
                            <div id="eficienciaMovimientoChart" style="height:300px;width:100%;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card chart-card shadow-sm animate">
                        <div class="card-body w-100">
                            <div id="polinizacionChart" style="height:300px;width:100%;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mb-4">
                    <div class="card chart-card shadow-sm animate">
                        <div class="card-body w-100">
                            <div id="duracionPolinizacionChart" style="height:300px;width:100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicador 5 : Gestión por Apiario -->
        <div id="indicador-gestion-apiario" class="charts-group" style="display:none;">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card chart-card shadow-sm animate">
                        <div class="card-body w-100">
                            <div id="inspeccionesPorEstacionChart" style="height: 300px; width: 100%;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card chart-card shadow-sm animate">
                        <div class="card-body w-100">
                            <div id="visitasGeneralesPorEstacionChart" style="height: 300px; width: 100%;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card chart-card shadow-sm animate">
                        <div class="card-body w-100">
                            <div id="medicamentosPorEstacionChart" style="height: 300px; width: 100%;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card chart-card shadow-sm animate">
                        <div class="card-body w-100">
                            <div id="movimientosPorEstacionChart" style="height: 300px; width: 100%;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card chart-card shadow-sm animate">
                        <div class="card-body w-100">
                            <div id="alimentacionPorEstacionChart" style="height: 300px; width: 100%;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card chart-card shadow-sm animate">
                        <div class="card-body w-100">
                            <div id="productividadColmenaChart" style="height: 300px; width: 100%;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card chart-card shadow-sm animate">
                        <div class="card-body w-100">
                            <div id="riesgosClimaticosChart" style="height: 300px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    window.dataApiarios = @json($dataApiarios);
    window.dataVisitas = @json($dataVisitas);
    window.dataVisitasInspecciones = @json($dataVisitasInspecciones);
    window.dataEstadoTareas = @json($subtareasEstadoTareas);
    window.dataSubtareas = @json($tasks);
    window.dataTareasGenerales = @json($tareasGenerales);
    window.dataMovimientosColmenas = @json($movimientosColmenas);
    window.dataPresenciaVarroa = @json($presenciaVarroa);
    window.dataEstadoNutricional = @json($estadoNutricional);
        
    document.addEventListener('DOMContentLoaded', () => {
    
        const botones = document.querySelectorAll('.indicador-boton');
        const grupos = document.querySelectorAll('.charts-group');
        window.indicadorActivo = null;
    
        // Ocultar todos los graficos al inicio
        grupos.forEach(c => c.style.display = 'none');
    
        // Mostrar el primer indicador (global)
        const primerBoton = botones[0];
        primerBoton.classList.add('active');
        const primerIndicador = primerBoton.dataset.indicador;
        document.getElementById(`indicador-${primerIndicador}`).style.display = 'block';
        window.indicadorActivo = primerIndicador;
    
        // Mostrar boton modal inicial
        document.querySelector(`.boton-modal-indicador[data-indicador="${primerIndicador}"]`)
            ?.classList.remove('d-none');
    
        // Inicializar graficos del primer indicador
        inicializarGraficosGlobal();
        window.graficosGlobalCreados = true;
    
        // LISTENER DE NAVEGACION ENTRE INDICADORES
        botones.forEach(btn => {
            btn.addEventListener('click', () => {
                const indicador = btn.dataset.indicador;
                window.indicadorActivo = indicador;
            
                // Mostrar/ocultar boton PDF Global
                const btnPDFGlobal = document.getElementById("btnDescargarPDFGlobal");
                if (indicador === "global") {
                    btnPDFGlobal.classList.remove("d-none");
                } else {
                    btnPDFGlobal.classList.add("d-none");
                }
            
                // Oculta todos 
                botones.forEach(b => b.classList.remove('active'));
                grupos.forEach(c => c.style.display = 'none');
            
                // Mostrar indicador seleccionado
                btn.classList.add('active');
                document.getElementById(`indicador-${indicador}`).style.display = 'block';
            
                // Forzar resize
                setTimeout(() => window.resizeAllCharts(), 100);
            
                // Boton del modal del indicador
                document.querySelectorAll('.boton-modal-indicador')
                    .forEach(b => b.classList.add('d-none'));
                document.querySelector(`.boton-modal-indicador[data-indicador="${indicador}"]`)
                    ?.classList.remove('d-none');
            
                // Inicializar graficos del indicador si no existen
                switch (indicador) {
                    case 'global':
                        window.destruirGraficosIndicador("global");
                        inicializarGraficosGlobal();
                        window.graficosGlobalCreados = true;
                        break;
                    
                    case 'por-apiario':
                        window.destruirGraficosIndicador("por-apiario");
                        inicializarGraficosPorApiario();
                        window.graficosPorApiarioCreados = true;
                        setTimeout(() => window.resizeAllCharts(), 400);
                        break;
                    
                    case 'plan-anual':
                        window.destruirGraficosIndicador("plan-anual");
                        inicializarGraficosPlanAnual();
                        window.graficosPlanAnualCreados = true;
                        break;
                    
                    case 'movimiento-colmenas':
                        window.destruirGraficosIndicador("movimiento-colmenas");
                        inicializarGraficosMovimiento();
                        window.graficosMovimientoCreados = true;
                        break;
                    
                    case 'gestion-apiario':
                        window.destruirGraficosIndicador("gestion-apiario");
                        inicializarGraficosGestion();
                        window.graficosGestionApiarioCreados = true;
                        setTimeout(() => window.resizeAllCharts(), 400);
                        break;
                }
            });
        });
    
        // Carga de graficos dentro de modal
        document.querySelectorAll('.boton-modal-indicador').forEach(btn => {
            btn.addEventListener("click", function () {
                const indicador = this.dataset.indicador;
                window.configurarModalPorIndicador(indicador);
            });
        });
    
        /* --------------------------------------------------------------
        PRE-CARGA SILENCIOSA DE TODOS LOS GRAFICOS PARA PDF CON TODOS LOS GRAFICOS
        -------------------------------------------------------------- */
        setTimeout(() => {
        
            if (!window.graficosPorApiarioCreados) {
                inicializarGraficosPorApiario();
                window.graficosPorApiarioCreados = true;
            }
        
            if (!window.graficosPlanAnualCreados) {
                inicializarGraficosPlanAnual();
                window.graficosPlanAnualCreados = true;
            }
        
            if (!window.graficosMovimientoCreados) {
                inicializarGraficosMovimiento();
                window.graficosMovimientoCreados = true;
            }
        
            if (!window.graficosGestionApiarioCreados) {
                inicializarGraficosGestion();
                window.graficosGestionApiarioCreados = true;
            }
        
            // Espera y luego reajusta tamaños
            setTimeout(() => window.resizeAllCharts(), 600);
        
        }, 800); // Evita lag al cargar la página
    
    });
    </script>
@endsection

@section('optional-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
    
    <link href="{{ asset('./css/components/home-user/graficos.css') }}" rel="stylesheet">
    <script src="{{ asset('js/components/home-user/graficos/graficos-utils.js') }}"></script>
    <script src="{{ asset('js/components/home-user/graficos/graficos-global.js') }}"></script>
    <script src="{{ asset('js/components/home-user/graficos/graficos-por-apiario.js') }}"></script>
    <script src="{{ asset('js/components/home-user/graficos/graficos-plan-anual.js') }}"></script>
    <script src="{{ asset('js/components/home-user/graficos/graficos-movimiento.js') }}"></script>
    <script src="{{ asset('js/components/home-user/graficos/graficos-gestion.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection