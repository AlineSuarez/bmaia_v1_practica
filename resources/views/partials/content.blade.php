<head>
  <link href="{{ asset('./css/components/home.css') }}" rel="stylesheet">
  <script src="{{ asset('./js/components/home.js') }}"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<button id="back-to-top" onclick="scrollToTop()"><span class="fa fa-arrow-up"></span></button>

<!-- Modal de sitio en desarrollo -->
<div id="development-modal" class="development-modal">
  <div class="development-modal-content">
    <div class="development-modal-header">
      <h2>Muy pronto</h2>
    </div>
    <div class="development-modal-body">
      <div class="hexagon-pattern"></div>

      <div class="modal-particle modal-particle-1"></div>
      <div class="modal-particle modal-particle-2"></div>
      <div class="modal-particle modal-particle-3"></div>
      <div class="modal-particle modal-particle-4"></div>

      <div class="development-icon">
        <img src="{{ asset('img/abeja.png') }}" width="160px" height="130px" alt="Fondo IA">
      </div>
      <p>¡Bienvenido a Ma<span class="highlight">iA</span>!</p>
      <p>Presentamos MaiA: el nuevo Agente de Inteligencia Artificial al servicio de la Apicultura Chilena
        <i style="font-weight:bold;">(versión
          Beta)</i>
      </p>
      <p>En Bee Fractal estamos desarrollando una IA que te ayudará a gestionar tus colmenas de forma más inteligente.
      </p>
      <p style="font-weight:bold;">¡Mantente informado y sé el primero en probarlo!</p>

    </div>
    <div class="development-modal-footer">
      <button id="close-modal-btn">Entendido</button>
    </div>
  </div>
</div>

<section id="inicio" class="hero-section">
  <div class="image-container-hero">
    <!-- Imagen de fondo -->
    <img src="{{ asset('img/home/home_panel.jpg') }}" alt="Fondo IA" class="background-image">

    <!-- Overlay gradient for depth -->
    <div class="premium-overlay"></div>

    <div class="content-wrapper">
      <div class="hero-text">
        <h1>Bienvenido a Ma<span class="highlight">iA</span></span></h1>
        <p class="tagline">Inteligencia Artificial al servicio de la Apicultura</p>
      </div>
    </div>

    <!-- Decorative elements -->
    <div class="floating-particles"></div>
  </div>
</section>

<section id="maia-ecosystem" class="maia-section">
  <div class="maia-container">
    <!-- Section 1: Sobre MaiA -->
    <div class="maia-content-section">
      <h1 class="section-title">Sobre Ma<span class="highlight">iA</span></h1>
      <div class="maia-content-row">
        <div class="maia-text">
          <p>
            Imagina tener un asesor experto disponible las 24 horas para ayudarte con tus colmenas.
          </p>
          <p>
            MaiA es un Agente de Inteligencia Artificial diseñado pensando en los apicultores chilenos. MaiA entiende
            los desafíos
            de los productores y está listo para responder tus consultas técnicas, ayudarte a llevar registros por voz
            sin importar
            dónde estés, y simplificar la gestión de tu apiario de forma rápida y sencilla.
          </p>
          <p>
            Con MaiA, olvídate de las dudas y el papeleo. Pregúntale sobre los primeros signos de una enfermedad y obtén
            información
            al instante. Dedica más tiempo a tus abejas y deja que MaiA te facilite el día a día en tu negocio.
          </p>
        </div>
        <div class="maia-video-container">
          <div class="video-showcase">
            <div class="device smartphone">
              <div class="smartphone-frame">
                <div class="smartphone-screen">
                  <video class="feature-video" autoplay loop muted playsinline
                    poster="https://files.cdn-files-a.com/uploads/10098964/800_6761a84cd11e2-thumbnail.jpg">
                    <source type="video/mp4"
                      src="https://cdn-media.f-static.net/uploads/10098964/normal_6761a84cd11e2.mp4">
                  </video>
                </div>
              </div>
            </div>
            <div class="glow-effect"></div>
            <!-- Enhanced decorative elements -->
            <div class="floating-hexagon floating-hexagon-lg" style="top: -40px; right: 20px;"></div>
            <div class="floating-hexagon floating-hexagon-sm" style="bottom: 30px; left: -20px;"></div>
            <div class="floating-hexagon floating-hexagon-md" style="top: 50%; right: -30px;"></div>
            <div class="floating-particle" style="top: 20%; left: 10%;"></div>
            <div class="floating-particle" style="bottom: 30%; right: 15%;"></div>
          </div>
        </div>
      </div>
      <!-- Animated line -->
      <div class="animated-line"></div>
    </div>

    <!-- Section 2: + Simple -->
    <div class="maia-content-section">
      <h1 class="section-title"><span class="highlight">+</span> Simple</h1>
      <div class="maia-content-row">
        <div class="maia-text">
          <p>
            Registra datos por voz, en tiempo real y sin necesidad de internet. Nuestra plataforma todo en uno, 100%
            digital está
            siempre contigo, accesible desde cualquier dispositivo.
          </p>
          <!-- Enhanced benefits list -->
          <div class="benefits-container">
            <h3 class="benefits-title">Nuestro distintivo:</h3>
            <ul class="benefits-list">
              <li>
                <span class="benefit-check">✓</span>
                Registro de datos por voz
              </li>
              <li>
                <span class="benefit-check">✓</span>
                Funciona en lugares sin conexión a internet
              </li>
              <li>
                <span class="benefit-check">✓</span>
                Interfaz intuitiva diseñada para apicultores
              </li>
              <li>
                <span class="benefit-check">✓</span>
                Sincronización automática
              </li>
              <li>
                <span class="benefit-check">✓</span>
                Fácil de usar
              </li>
              <li>
                <span class="benefit-check">✓</span>
                Conexión por Bluetooth con otros dispositivos
              </li>
              <li>
                <span class="benefit-check">✓</span>
                Aplicación multiplataforma
              </li>
            </ul>
          </div>
          <p style="margin-top: 20px;">
            ¿Quieres simplificar la gestión de tu apiario y aumentar tu producción?
          </p>
          <p style="margin-top: 20px;">
            Prueba MaiA gratis por 16 días y vive la experiencia!
          </p>
          <div class="cta-buttons">
            <a onclick="openModal('register-modal')" class="cta-button primary" style="cursor: pointer;">Comienza tu
              prueba gratis</a>
          </div>
        </div>
        <div class="maia-video-container">
          <div class="video-showcase">
            <!-- Monitor completo con soporte y base -->
            <div class="device macbook">
              <div class="macbook-lid">
                <div class="macbook-screen">
                  <video class="feature-video" autoplay loop muted playsinline
                    poster="https://files.cdn-files-a.com/uploads/10098964/800_6761a23826d13-thumbnail.jpg">
                    <source type="video/mp4"
                      src="https://cdn-media.f-static.net/uploads/10098964/normal_6761a23826d13.mp4">
                  </video>
                </div>
              </div>
              <div class="macbook-stand"></div>
              <div class="macbook-base"></div>
            </div>
            <div class="glow-effect glow-effect-alt"></div>
            <!-- Enhanced decorative elements -->
            <div class="floating-hexagon floating-hexagon-sm" style="top: -30px; left: 20px;"></div>
            <div class="floating-hexagon floating-hexagon-lg" style="bottom: 20px; right: -15px;"></div>
            <div class="floating-hexagon floating-hexagon-md" style="top: 40%; left: -25px;"></div>
            <div class="floating-particle" style="top: 25%; right: 10%;"></div>
            <div class="floating-particle" style="bottom: 40%; left: 5%;"></div>
          </div>
        </div>
      </div>
      <!-- Animated line -->
      <div class="animated-line"></div>
    </div>

    <!-- Section 3: + Sostenible -->
    <div class="maia-content-section">
      <h1 class="section-title"><span class="highlight">+</span> Sostenible</h1>
      <div class="maia-content-row">
        <div class="maia-text">
          <p>
            MaiA te ayuda a mejorar la sostenibilidad ambiental, disminuyendo la mortandad de abejas por varroa y
            nosema, y la
            pérdida por enjambres. Además te ayuda a reducir el número de viajes, el gasto de combustible y las
            emisiones de CO2.
          </p>
          <!-- Enhanced sustainability metrics -->
          <div class="metrics-container">
            <div class="metric-item">
              <div class="metric-value">-10%</div>
              <div class="metric-label">Reducción de viajes, uso de combustible y emisiones de CO2</div>
            </div>
            <div class="metric-item">
              <div class="metric-value">-30%</div>
              <div class="metric-label">Reducción en mortandad de abejas</div>
            </div>
            <div class="metric-item">
              <div class="metric-value">-40%</div>
              <div class="metric-label">Previene la formación y pérdida de enjambres</div>
            </div>
          </div>
          <p class="sustainability-note">
            Nuestro sistema contribuye activamente a la preservación de las abejas, esenciales para la polinización y la
            biodiversidad. ¿Quieres mejorar la salud de tus colmenas?
          </p>
          <div class="cta-buttons">
            <a href="#descarga" class="cta-button primary">¡Descarga la App y descubre la diferencia!</a>
          </div>
        </div>
        <div class="maia-video-container">
          <div class="video-showcase">
            <!-- iPad mejorado con cámara centrada y sin botón home -->
            <div class="device ipad">
              <div class="ipad-frame">
                <div class="ipad-screen">
                  <video class="feature-video" autoplay loop muted playsinline
                    poster="https://files.cdn-files-a.com/uploads/10098964/800_6761de379c2f5-thumbnail.jpg">
                    <source type="video/mp4"
                      src="https://cdn-media.f-static.net/uploads/10098964/normal_6761de379c2f5.mp4">
                  </video>
                </div>
              </div>
            </div>
            <div class="glow-effect glow-effect-sustainable"></div>
            <!-- Enhanced decorative elements -->
            <div class="floating-hexagon floating-hexagon-lg" style="top: -35px; right: 30px;"></div>
            <div class="floating-hexagon floating-hexagon-md" style="bottom: 25px; left: -10px;"></div>
            <div class="floating-hexagon floating-hexagon-sm" style="top: 30%; right: -20px;"></div>
            <div class="floating-particle" style="top: 15%; left: 20%;"></div>
            <div class="floating-particle" style="bottom: 20%; right: 25%;"></div>
          </div>
        </div>
      </div>
      <!-- Animated line -->
      <div class="animated-line"></div>
    </div>

    <!-- NEW SECTION: Eficiencia y Productividad -->
    <div class="maia-content-section">
      <h1 class="section-title"><span class="highlight">+</span> Práctico</h1>
      <div class="maia-content-row">
        <div class="maia-text">
          <p>
            Gana tiempo y enfócate en tus abejas. MaiA te ayuda a optimizar tu trabajo, reduciendo tareas
            administrativas para que
            dediques tu energía a lo que realmente importa: el cuidado de tus colmenas y la producción de miel.
          </p>
          <p>
            Con MaiA registra tus observaciones en menos de 10 segundos!
          </p>

          <!-- Efficiency stats -->
          <div class="efficiency-stats">
            <div class="efficiency-stat-row">
              <div class="efficiency-bar-container">
                <div class="efficiency-label">Tiempo en campo</div>
                <div class="efficiency-bar-wrapper">
                  <div class="efficiency-bar efficiency-bar-before" style="width: 100%;">
                    <span>Antes: 100%</span>
                  </div>
                </div>
                <div class="efficiency-bar-wrapper">
                  <div class="efficiency-bar efficiency-bar-after" style="width: 65%;">
                    <span>Con MaiA: 65% + 35% de tiempo libre</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="efficiency-stat-row">
              <div class="efficiency-bar-container">
                <div class="efficiency-label">Tiempo en documentación</div>
                <div class="efficiency-bar-wrapper">
                  <div class="efficiency-bar efficiency-bar-before" style="width: 100%;">
                    <span>Antes: 100%</span>
                  </div>
                </div>
                <div class="efficiency-bar-wrapper">
                  <div class="efficiency-bar efficiency-bar-after" style="width: 40%;">
                    <span>Con MaiA: 40% + 60% de tiempo libre</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="efficiency-stat-row">
              <div class="efficiency-bar-container">
                <div class="efficiency-label">Tiempo al detectar problemas</div>
                <div class="efficiency-bar-wrapper">
                  <div class="efficiency-bar efficiency-bar-before" style="width: 60%;">
                    <span>Antes: 60%</span>
                  </div>
                </div>
                <div class="efficiency-bar-wrapper">
                  <div class="efficiency-bar efficiency-bar-after" style="width: 95%;">
                    <span>Con MaiA: 95% + 5% de tiempo libre</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="maia-video-container">
          <div class="efficiency-showcase">
            <div class="device smartphone smartphone-dark">
              <div class="smartphone-frame">
                <div class="smartphone-screen">
                  <img src="/img/eficiencia.jpg" alt="Eficiencia apícola con MaiA" class="efficiency-image">
                </div>
              </div>
            </div>
            <div class="glow-effect glow-effect-efficiency"></div>
            <!-- Enhanced decorative elements -->
            <div class="floating-hexagon floating-hexagon-lg" style="top: -25px; left: 20px;"></div>
            <div class="floating-hexagon floating-hexagon-sm" style="bottom: 15px; right: -10px;"></div>
            <div class="floating-hexagon floating-hexagon-md" style="top: 60%; left: -15px;"></div>
            <div class="floating-particle" style="top: 30%; right: 20%;"></div>
            <div class="floating-particle" style="bottom: 25%; left: 15%;"></div>
          </div>
        </div>
      </div>
      <!-- Animated line -->
      <div class="animated-line"></div>
    </div>

    <!-- Call to Action -->
    <div class="maia-content-section">
      <div class="cta-container">
        <h2 class="cta-title">Súmate a la transformación digital</h2>
        <p class="cta-description">
          Únete a nuestra comunidad de apicultores y mejora la gestión de tus colmenas. Ahorra tiempo y dinero,
          reduciendo costos
          operacionales y aumentando la rentabilidad de tu negocio.
        </p>
        <p class="cta-description">
          Prueba MaiA gratis por 16 días y transforma la gestión de tus colmenas!
        </p>
        <div class="cta-buttons">
          <a href="#descarga" class="cta-button primary">¡Descarga la app ahora!</a>
        </div>

        <!-- Enhanced honeycomb background for CTA -->
        <div class="cta-honeycomb-bg">
          <div class="honeycomb-cell"></div>
          <div class="honeycomb-cell"></div>
          <div class="honeycomb-cell"></div>
          <div class="honeycomb-cell"></div>
          <div class="honeycomb-cell"></div>
          <div class="honeycomb-cell"></div>
          <div class="honeycomb-cell"></div>
          <div class="honeycomb-cell"></div>
          <div class="honeycomb-cell"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Enhanced background elements -->
  <div class="bg-honeycomb bg-honeycomb-1"></div>
  <div class="bg-honeycomb bg-honeycomb-2"></div>
  <div class="bg-honeycomb bg-honeycomb-3"></div>
</section>

<!-- Estructura HTML actualizada con nuevas partículas -->
<section id="herramientas" class="herramientas-section">
  <!-- Nuevas partículas decorativas -->
  <div class="particles-container">
    <div class="particle particle-1"></div>
    <div class="particle particle-2"></div>
    <div class="particle particle-3"></div>
    <div class="particle particle-4"></div>
    <div class="particle particle-5"></div>
    <div class="particle particle-6"></div>
    <div class="particle particle-7"></div>
    <div class="particle particle-8"></div>
    <div class="particle particle-9"></div>
    <div class="particle particle-10"></div>
    <div class="particle particle-11"></div>
    <div class="particle particle-12"></div>
  </div>

  <div class="herramientas-container">
    <div class="img-content-hero">
      <!-- Imagen de fondo -->
      <img src="https://files.cdn-files-a.com/uploads/10098964/2000_676186026e771.jpg" alt="Herramientas" />
      <div class="overlay">
        <div class="hero-content">
          <!-- Título simplificado -->
          <h2 class="section-title">Herramientas</h2>
          <p class="hero-description">
            Todas las herramientas necesarias para aumentar la rentabilidad del negocio apícola, ¡al alcance de tu mano!
          </p>
        </div>
      </div>
    </div>

    <!-- Texto introductorio adicional -->
    <div class="intro-text">
      <h2 style="font-weight: bold; margin-bottom: 30px;">MaiA la forma más inteligente de gestionar tus colmenas</h2>
      <p>Plataforma todo en uno, 100% digital, que integra herramientas avanzadas de IA para gestionar tus colmenas de
        manera más
        fácil y rápida, adaptándose a las particularidades de la apicultura chilena.</p>
      <div class="cta-buttons">
        <a href="#contacto" class="cta-button primary">Comienza tu prueba gratis</a>
      </div>
    </div>

    <div class="herramientas-grid-container">
      <ul class="herramientas-grid">
        <!-- Sistema Experto -->
        <li class="herramienta-card">
          <div class="herramienta-header">
            <div class="herramienta-icon">
              <i class="fa-solid fa-brain"></i>
            </div>
            <h3>Sistema Experto</h3>
          </div>
          <div class="herramienta-body">
            <p>Realiza consultas técnicas a nuestro Agente de IA y obtén respuestas precisas, basadas en la información
              de
              tu apiario y nuestra base de conocimiento. Tendrás un asesor experto las 24 horas del día, 365 días del
              año.
            </p>
            <div class="herramienta-stat">
              <span class="stat-value">99%</span>
              <span class="stat-label">Precisión de respuestas</span>
            </div>
          </div>
        </li>

        <!-- Cuaderno de Campo -->
        <li class="herramienta-card">
          <div class="herramienta-header">
            <div class="herramienta-icon">
              <i class="fa-solid fa-file-download"></i>
            </div>
            <h3>Cuaderno de Campo</h3>
          </div>
          <div class="herramienta-body">
            <p>Completa y descarga automáticamente el Cuaderno de Campo del SAG. Cumple con los requisitos regulatorios
              sin
              esfuerzo, generando documentación oficial a partir de los datos que registras, mediante comando de voz.
              Solo
              tienes que imprimir y firmar.</p>
            <div class="herramienta-stat">
              <span class="stat-value">100%</span>
              <span class="stat-label">Más Práctico</span>
            </div>
          </div>
        </li>

        <!-- Agenda de Tareas -->
        <li class="herramienta-card">
          <div class="herramienta-header">
            <div class="herramienta-icon">
              <i class="fa-solid fa-calendar-check"></i>
            </div>
            <h3>Agenda de Tareas</h3>
          </div>
          <div class="herramienta-body">
            <p>Planifica y organiza tus inspecciones y tareas de manera eficiente. Podrás recibir notificaciones de
              tareas
              pendientes y alertas de eventos críticos.</p>
            <div class="herramienta-stat">
              <span class="stat-value">50%</span>
              <span class="stat-label">Ahorro de tiempo</span>
            </div>
          </div>
        </li>

        <!-- Trazabilidad de Colmenas -->
        <li class="herramienta-card">
          <div class="herramienta-header">
            <div class="herramienta-icon">
              <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <h3>Trazabilidad de colmenas</h3>
          </div>
          <div class="herramienta-body">
            <p>Garantiza la calidad y autenticidad de tu miel. Podrás crear todos los apiarios que necesites durante la
              temporada, manteniendo el registro y trazabilidad individual de cada colmena.</p>
            <div class="herramienta-stat">
              <span class="stat-value">100%</span>
              <span class="stat-label">Trazabilidad de colmenas</span>
            </div>
          </div>
        </li>

        <!-- Control Sanitario -->
        <li class="herramienta-card">
          <div class="herramienta-header">
            <div class="herramienta-icon">
              <i class="fa-solid fa-virus"></i>
            </div>
            <h3>Control Sanitario</h3>
          </div>
          <div class="herramienta-body">
            <p>Vigila y controla la presencia de varroa y nosema. Nuestro sistema de detección temprana te ayuda a
              identificar problemas sanitarios antes de que se conviertan en un problema fuera de control.</p>
            <div class="herramienta-stat">
              <span class="stat-value">30%</span>
              <span class="stat-label">Reducción de mortandad</span>
            </div>
          </div>
        </li>

        <!-- Clima y Vegetación -->
        <li class="herramienta-card">
          <div class="herramienta-header">
            <div class="herramienta-icon">
              <i class="fa-solid fa-cloud-sun"></i>
            </div>
            <h3>Clima y Vegetación</h3>
          </div>
          <div class="herramienta-body">
            <p>Monitorea las condiciones del clima y vegetación. Descubre las zonas ideales para ubicar tus colmenas,
              considerando la flora local, los calendarios de floración y los riesgos ambientales. Maximiza el potencial
              de
              cada apiario.</p>
            <div class="herramienta-stat">
              <span class="stat-value">85%</span>
              <span class="stat-label">Precisión de monitoreo</span>
            </div>
          </div>
        </li>

        <!-- Simulador de Rendimiento -->
        <li class="herramienta-card">
          <div class="herramienta-header">
            <div class="herramienta-icon">
              <i class="fa-solid fa-chart-line"></i>
            </div>
            <h3>Simulador de Rendimiento</h3>
          </div>
          <div class="herramienta-body">
            <p>Usa modelos de predicción de floraciones y simulación de rendimiento. Nuestros algoritmos analizan datos
              históricos y actuales, que te ayudarán a predecir flujos de néctar, proyectar rendimientos y ajustar tus
              prácticas de manejo para obtener el máximo potencial.</p>
            <div class="herramienta-stat">
              <span class="stat-value">85%</span>
              <span class="stat-label">Precisión en predicción</span>
            </div>
          </div>
        </li>

        <!-- Calculadoras -->
        <li class="herramienta-card">
          <div class="herramienta-header">
            <div class="herramienta-icon">
              <i class="fa-solid fa-calculator"></i>
            </div>
            <h3>Calculadoras</h3>
          </div>
          <div class="herramienta-body">
            <p>Usa calculadoras de nutrición y tratamientos sanitarios. Determina dosis exactas de medicamentos y
              alimentación suplementaria, según el tamaño y estado de desarrollo de tus colmenas.</p>
            <div class="herramienta-stat">
              <span class="stat-value">100%</span>
              <span class="stat-label">Más exacto</span>
            </div>
          </div>
        </li>
      </ul>
    </div>
    <!-- Nota informativa final -->
    <div class="info-footer">
      <div class="info-icon">
        <i class="fa-solid fa-lightbulb"></i>
      </div>
      <p>Todas nuestras herramientas están disponibles en la plataforma MaiA y se sincronizan automáticamente entre
        dispositivos. Los datos se actualizan en tiempo real, permitiéndote tomar decisiones informadas desde cualquier
        lugar y en cualquier momento.</p>
    </div>
  </div>
</section>

<!-- Sección de Descarga de la App -->
<section id="descarga" class="descarga-section">
  <!-- Partículas decorativas -->
  <div class="particles-container">
    <div class="particle particle-1"></div>
    <div class="particle particle-2"></div>
    <div class="particle particle-3"></div>
    <div class="particle particle-4"></div>
    <div class="particle particle-5"></div>
    <div class="particle particle-6"></div>
  </div>

  <div class="container">
    <div class="descarga-content">
      <div class="descarga-text">
        <h2 class="section-title">Descarga la App</h2>
        <p class="section-description" style="text-align: center;">Prueba MaiA gratis por 16 días, la forma más
          inteligente de gestionar tus
          colmenas</p>

        <div class="app-features">
          <div class="feature">
            <div class="feature-icon">
              <i class="fa-solid fa-mobile-screen-button"></i>
            </div>
            <div class="feature-text">
              <h3>Interfaz intuitiva</h3>
              <p>Diseñada para apicultores, fácil de usar por comandos de voz</p>
            </div>
          </div>

          <div class="feature">
            <div class="feature-icon">
              <i class="fa-solid fa-wifi"></i>
            </div>
            <div class="feature-text">
              <h3>Modo offline</h3>
              <p>Funciona en lugares sin la necesidad conexión a internet</p>
            </div>
          </div>

          <div class="feature">
            <div class="feature-icon">
              <i class="fa-solid fa-bell"></i>
            </div>
            <div class="feature-text">
              <h3>Notificaciones</h3>
              <p>Recibe notificaciones de tareas pendientes y alertas de eventos críticos</p>
            </div>
          </div>

          <div class="feature">
            <div class="feature-icon">
              <i class="fa-solid fa-infinity"></i>
            </div>
            <div class="feature-text">
              <h3>Apiarios ilimitados</h3>
              <p>Crea y gestiona tantos apiarios como quieras, sin perder los registros y trazabilidad de tus colmenas
                individuales</p>
            </div>
          </div>

          <div class="feature">
            <div class="feature-icon">
              <i class="fa-solid fa-qrcode"></i>
            </div>
            <div class="feature-text">
              <h3>Código QR</h3>
              <p>Identifica tus colmenas con códigos QR y visualiza toda su información de forma rápida y organizada</p>
            </div>
          </div>

          <div class="feature">
            <div class="feature-icon">
              <i class="fa-solid fa-cloud"></i>
            </div>
            <div class="feature-text">
              <h3>Datos seguros</h3>
              <p>Accede a la información de tus colmenas desde cualquier lugar con la seguridad de que tus datos están
                siempre
                actualizados y protegidos en la nube</p>
            </div>
          </div>
        </div>

        <div class="download-buttons" style="justify-content: center;">
          <a href="#" class="download-btn android-btn">
            <div class="btn-icon">
              <i class="fa-brands fa-android"></i>
            </div>
            <div class="btn-text">
              <span class="btn-small-text">Disponible en</span>
              <span class="btn-large-text">Google Play</span>
            </div>
          </a>

          <a href="#" class="download-btn ios-btn">
            <div class="btn-icon">
              <i class="fa-brands fa-apple"></i>
            </div>
            <div class="btn-text">
              <span class="btn-small-text">Descarga en</span>
              <span class="btn-large-text">App Store</span>
            </div>
          </a>
        </div>

        <div class="app-rating" style="justify-content: center; margin-bottom: 10px;">
          <div class="stars">
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star-half-stroke"></i>
          </div>
        </div>
        <p style="text-align:center;">4.8 de 5 estrellas basado en más de 200 reseñas</p>
      </div>

      <div class="app-mockup">
        <div class="phone-frame">
          <div class="phone-screen">
            <img src="{{ asset('img/home/MaiA - App final-01.png') }}" alt="MaiA App" class="app-screenshot" />
          </div>
          <div class="phone-notch"></div>
          <div class="phone-reflection"></div>
        </div>
        <div class="floating-screens">
          <div class="floating-screen screen-1">
            <img src="{{ asset('img/home/MaiA - App final-02.png') }}" alt="Pantalla de estadísticas" />
          </div>
          <div class="floating-screen screen-2">
            <img src="{{ asset('img/home/MaiA - App final-10.png') }}" alt="Pantalla de colmenas" />
          </div>
        </div>
        <div class="mockup-shadow"></div>
        <div class="mockup-decoration"></div>
      </div>
    </div>
  </div>
</section>

<!-- Sección de Cómo Funciona -->
<section id="como-funciona" class="como-funciona-section">
  <h2 class="section-title">¿Quieres saber más sobre MaiA?</h2>
  <div class="container">
    <div class="process-container collapsible-container" style="margin-bottom: 40px;">
      <div class="collapsible-header" style="position: relative; z-index: 2;">
        <h2 class="section-title" style="margin-top: 20px; font-size: 30px !important;">¿Cómo funciona?</h2>
        <div class="collapse-icon">
          <i class="fa-solid fa-chevron-down"></i>
        </div>
      </div>

      <!-- Contenido visible incluso cuando está colapsado -->
      <div class="process-preview"
        style="padding: 0 20px 20px; text-align: center; margin-bottom: 20px; border-bottom: 1px dashed rgba(255, 143, 0, 0.2);">
        <p style="margin-bottom: 15px; font-size: 1rem; color: #555;">Nuestra IA analiza tus datos, junto con
          información
          climática y de floración local, para entregarte alertas tempranas y recomendaciones personalizadas. <span
            style="font-style: italic; color: #ff8f00;">Haz clic para ver el proceso completo.</span></p>

        <div class="process-preview-icons" style="display: flex; justify-content: center; gap: 25px; flex-wrap: wrap;">
          <div class="preview-icon" style="display: flex; flex-direction: column; align-items: center;">
            <i class="fa-solid fa-calendar-days" style="font-size: 24px; color: #ff8f00; margin-bottom: 8px;"></i>
            <span style="font-size: 0.8rem; color: #666;">Planifica</span>
          </div>
          <div class="preview-icon" style="display: flex; flex-direction: column; align-items: center;">
            <i class="fa-solid fa-microphone-lines" style="font-size: 24px; color: #ff8f00; margin-bottom: 8px;"></i>
            <span style="font-size: 0.8rem; color: #666;">Registra</span>
          </div>
          <div class="preview-icon" style="display: flex; flex-direction: column; align-items: center;">
            <i class="fa-solid fa-brain" style="font-size: 24px; color: #ff8f00; margin-bottom: 8px;"></i>
            <span style="font-size: 0.8rem; color: #666;">Analiza</span>
          </div>
          <div class="preview-icon" style="display: flex; flex-direction: column; align-items: center;">
            <i class="fa-solid fa-mobile-screen-button"
              style="font-size: 24px; color: #ff8f00; margin-bottom: 8px;"></i>
            <span style="font-size: 0.8rem; color: #666;">Informa</span>
          </div>
          <div class="preview-icon" style="display: flex; flex-direction: column; align-items: center;">
            <i class="fa-solid fa-chart-line" style="font-size: 24px; color: #ff8f00; margin-bottom: 8px;"></i>
            <span style="font-size: 0.8rem; color: #666;">Mejora continua</span>
          </div>
        </div>
      </div>

      <div class="collapsible-content">
        <div class="section-description"
          style="margin-bottom: 30px; text-align: center; max-width: 800px; margin-left: auto; margin-right: auto;">
          Con MaiA, optimiza tu tiempo, protege tus abejas y produce miel de manera más inteligente.
          Todo sincronizado de forma segura en la nube y accesible desde cualquier dispositivo.
        </div>

        <div class="contenedor-img">
          <img src="{{ asset('img/home/procesar.png') }}" alt="Fondo IA" class="img-flotante">
        </div>

        <div class="process-flow">
          <div class="process-step">
            <div class="step-number">1</div>
            <div class="step-icon">
              <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div class="step-content">
              <h3>Planifica tus Tareas</h3>
              <p>Planifica y organiza tus inspecciones de manera eficiente, optimizando el uso del tiempo y los recursos
                en
                cada etapa de producción.</p>
              <div class="step-illustration">
                <div class="hive-icon">
                  <i class="fa-solid fa-calendar-check"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="process-connector"></div>

          <div class="process-step">
            <div class="step-number">2</div>
            <div class="step-icon">
              <i class="fa-solid fa-microphone-lines"></i>
            </div>
            <div class="step-content">
              <h3>Registro por voz</h3>
              <p>A través de la App registra conversaciones, agenda tareas, agrega información de tus manejos y registra
                observaciones de
                tus visitas de inspección, todo mediante comandos de voz.</p>
              <div class="step-illustration">
                <div class="voice-animation">
                  <div class="sound-waves">
                    <div class="sound-wave wave-1"></div>
                    <div class="sound-wave wave-2"></div>
                    <div class="sound-wave wave-3"></div>
                    <div class="sound-wave wave-4"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="process-connector"></div>

          <div class="process-step">
            <div class="step-number">3</div>
            <div class="step-icon">
              <i class="fa-solid fa-cloud-arrow-up"></i>
            </div>
            <div class="step-content">
              <h3>Transmisión segura</h3>
              <p>Los datos se transmiten de forma segura a nuestros servidores a través de conexiones encriptadas,
                respetando tu privacidad y la seguridad de tu información.</p>
              <div class="step-illustration">
                <div class="transmission-animation">
                  <div class="transmission-dot"></div>
                  <div class="transmission-dot"></div>
                  <div class="transmission-dot"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="process-connector"></div>

          <div class="process-step">
            <div class="step-number">4</div>
            <div class="step-icon">
              <i class="fa-solid fa-brain"></i>
            </div>
            <div class="step-content">
              <h3>Análisis inteligente</h3>
              <p>MaiA integra y analiza los datos registrados, correlacionando el estado de desarrollo de las colmenas,
                las
                condiciones
                ambientales del apiario, y el registro de los manejos aplicados. Nuestro Agente de IA identifica
                patrones
                y
                genera
                recomendaciones técnicas personalizadas.
              </p>
              <div class="step-illustration">
                <div class="analysis-animation">
                  <div class="analysis-graph">
                    <div class="graph-bar"></div>
                    <div class="graph-bar"></div>
                    <div class="graph-bar"></div>
                    <div class="graph-bar"></div>
                    <div class="graph-bar"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="process-connector"></div>

          <div class="process-step">
            <div class="step-number">5</div>
            <div class="step-icon">
              <i class="fa-solid fa-mobile-screen-button"></i>
            </div>
            <div class="step-content">
              <h3>Información accionable</h3>
              <p>Recibe notificaciones de tareas pendientes, alertas de eventos críticos y recomendaciones
                personalizadas
                en
                tu
                dispositivo móvil, permitiéndote tomar decisiones oportunamente.</p>
              <div class="step-illustration">
                <div class="notification-animation">
                  <div class="notification-bell">
                    <i class="fa-solid fa-bell"></i>
                  </div>
                  <div class="notification-popup">
                    <div class="notification-dot"></div>
                    <div class="notification-line"></div>
                    <div class="notification-line"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="process-connector"></div>

          <div class="process-step">
            <div class="step-number">6</div>
            <div class="step-icon">
              <i class="fa-solid fa-robot"></i>
            </div>
            <div class="step-content">
              <h3>Consulta al Sistema Experto</h3>
              <p>Realiza consultas técnicas a nuestro Agente de IA, y obtén respuestas precisas, basadas en la
                información
                de tu propio
                apiario. Tendrás un asesor experto las 24 horas del día, durante 365 días del año.</p>
              <div class="step-illustration">
                <div class="ai-assistant-animation">
                  <div class="chat-container-horizontal">
                    <div class="chat-sequence">
                      <div class="chat-item user-query">
                        <i class="fa-solid fa-user-circle"></i>
                        <div class="query-bubble">¿Varroa?</div>
                      </div>
                      <div class="chat-arrow">
                        <i class="fa-solid fa-chevron-right"></i>
                      </div>
                      <div class="chat-item ai-thinking">
                        <i class="fa-solid fa-brain"></i>
                        <div class="thinking-dots">
                          <span class="dot"></span>
                          <span class="dot"></span>
                          <span class="dot"></span>
                        </div>
                      </div>
                      <div class="chat-arrow">
                        <i class="fa-solid fa-chevron-right"></i>
                      </div>
                      <div class="chat-item ai-response">
                        <i class="fa-solid fa-robot"></i>
                        <div class="response-bubble">
                          <i class="fa-solid fa-check-circle"></i>
                          <span>Tratamiento recomendado</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="process-connector"></div>

          <div class="process-step">
            <div class="step-number">7</div>
            <div class="step-icon">
              <i class="fa-solid fa-chart-simple"></i>
            </div>
            <div class="step-content">
              <h3>Visualización de Indicadores</h3>
              <p>Accede a un panel de control con indicadores fáciles de entender.</p>
              <div class="step-illustration">
                <div class="dashboard-animation">
                  <div class="dashboard-container">
                    <div class="dashboard-header">
                      <div class="dashboard-title"></div>
                      <div class="dashboard-menu"></div>
                    </div>
                    <div class="dashboard-widgets">
                      <div class="widget widget-chart">
                        <div class="widget-bar widget-bar-1"></div>
                        <div class="widget-bar widget-bar-2"></div>
                        <div class="widget-bar widget-bar-3"></div>
                        <div class="widget-bar widget-bar-4"></div>
                      </div>
                      <div class="widget widget-stats">
                        <div class="stat-circle"></div>
                        <div class="stat-label"></div>
                      </div>
                      <div class="widget widget-list">
                        <div class="list-item"></div>
                        <div class="list-item"></div>
                        <div class="list-item"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="process-connector"></div>

          <div class="process-step">
            <div class="step-number">8</div>
            <div class="step-icon">
              <i class="fa-solid fa-chart-line"></i>
            </div>
            <div class="step-content">
              <h3>Sistema de Mejora Continua</h3>
              <p>MaiA genera un historial detallado de tus actividades y manejos aplicados en cada apiario, por
                temporada.
                A
                medida que estos datos se acumulan, surgen patrones que permiten analizar y optimizar tus prácticas,
                creando
                un
                sistema
                de mejora continua que potenciará cada vez más la gestión de tu negocio.</p>
              <div class="step-illustration">
                <div class="improvement-animation">
                  <div class="timeline-container">
                    <div class="timeline-icon">
                      <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <div class="timeline-arrow">
                      <i class="fa-solid fa-arrow-right"></i>
                    </div>
                    <div class="timeline-icon">
                      <i class="fa-solid fa-magnifying-glass-chart"></i>
                    </div>
                    <div class="timeline-arrow">
                      <i class="fa-solid fa-arrow-right"></i>
                    </div>
                    <div class="timeline-icon">
                      <i class="fa-solid fa-lightbulb"></i>
                    </div>
                    <div class="timeline-arrow">
                      <i class="fa-solid fa-arrow-right"></i>
                    </div>
                    <div class="timeline-icon">
                      <i class="fa-solid fa-trophy"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="benefits-container collapsible-container" style="margin-bottom: 70px;">
      <div class="collapsible-header" style="position: relative; z-index: 2;">
        <h2 class="section-title" style="margin-top: 20px; font-size: 30px !important;">Beneficios de MaiA</h2>
        <div class="collapse-icon">
          <i class="fa-solid fa-chevron-down"></i>
        </div>
      </div>

      <!-- Contenido visible incluso cuando está colapsado -->
      <div class="benefits-preview"
        style="padding: 0 20px 20px; text-align: center; margin-bottom: 20px; border-bottom: 1px dashed rgba(255, 143, 0, 0.2);">
        <p style="margin-bottom: 15px; font-size: 1rem; color: #555;">Descubre cómo MaiA mejora la productividad, salud
          de las
          colmenas y gestión de datos para tu apiario. <span style="font-style: italic; color: #ff8f00;">Haz clic para
            ver
            todos los beneficios.</span></p>

        <div class="benefits-preview-icons" style="display: flex; justify-content: center; gap: 25px; flex-wrap: wrap;">
          <div class="preview-icon" style="display: flex; flex-direction: column; align-items: center;">
            <i class="fa-solid fa-chart-line" style="font-size: 24px; color: #ff8f00; margin-bottom: 8px;"></i>
            <span style="font-size: 0.8rem; color: #666;">Productividad</span>
          </div>
          <div class="preview-icon" style="display: flex; flex-direction: column; align-items: center;">
            <i class="fa-solid fa-shield-virus" style="font-size: 24px; color: #ff8f00; margin-bottom: 8px;"></i>
            <span style="font-size: 0.8rem; color: #666;">Salud</span>
          </div>
          <div class="preview-icon" style="display: flex; flex-direction: column; align-items: center;">
            <i class="fa-solid fa-robot" style="font-size: 24px; color: #ff8f00; margin-bottom: 8px;"></i>
            <span style="font-size: 0.8rem; color: #666;">IA Experto</span>
          </div>
          <div class="preview-icon" style="display: flex; flex-direction: column; align-items: center;">
            <i class="fa-solid fa-mobile-alt" style="font-size: 24px; color: #ff8f00; margin-bottom: 8px;"></i>
            <span style="font-size: 0.8rem; color: #666;">Accesibilidad</span>
          </div>
          <div class="preview-icon" style="display: flex; flex-direction: column; align-items: center;">
            <i class="fa-solid fa-clock" style="font-size: 24px; color: #ff8f00; margin-bottom: 8px;"></i>
            <span style="font-size: 0.8rem; color: #666;">Ahorro de tiempo</span>
          </div>
        </div>
      </div>

      <div class="collapsible-content">
        <div class="benefits-category">
          <h3 class="category-title">Productividad y Eficiencia</h3>
          <div class="benefits-row">
            <div class="benefit-item"
              data-tooltip="Incrementa la producción de miel hasta un 30% gracias a intervenciones oportunas basadas en datos reales.">
              <div class="benefit-icon">
                <i class="fa-solid fa-chart-line"></i>
              </div>
              <span class="benefit-label">Aumento de productividad</span>
            </div>

            <div class="benefit-item"
              data-tooltip="Reduce el tiempo dedicado a inspecciones innecesarias y enfócate en las colmenas que realmente necesitan atención.">
              <div class="benefit-icon">
                <i class="fa-solid fa-clock"></i>
              </div>
              <span class="benefit-label">Ahorro de tiempo</span>
            </div>

            <div class="benefit-item"
              data-tooltip="Aumenta la precisión en la detección temprana de problemas en las colmenas.">
              <div class="benefit-icon">
                <i class="fas fa-check-circle"></i>
              </div>
              <span class="benefit-label">Precisión mejorada</span>
            </div>

            <div class="benefit-item"
              data-tooltip="Gestiona desde unas pocas hasta cientos de colmenas con la misma facilidad.">
              <div class="benefit-icon">
                <i class="fas fa-layer-group"></i>
              </div>
              <span class="benefit-label">Escalabilidad</span>
            </div>
          </div>
        </div>

        <!-- Categoría: Salud de las Colmenas -->
        <div class="benefits-category">
          <h3 class="category-title">Salud de las Colmenas</h3>
          <div class="benefits-row">
            <div class="benefit-item"
              data-tooltip="Detecta tempranamente signos de problemas sanitarios antes de que afecten a toda la colonia.">
              <div class="benefit-icon">
                <i class="fa-solid fa-shield-virus"></i>
              </div>
              <span class="benefit-label">Prevención de enfermedades</span>
            </div>

            <div class="benefit-item"
              data-tooltip="Optimiza recursos y reduce el impacto ambiental mientras mejoras la salud de tus colonias.">
              <div class="benefit-icon">
                <i class="fa-solid fa-leaf"></i>
              </div>
              <span class="benefit-label">Apicultura sostenible</span>
            </div>
          </div>
        </div>

        <!-- Categoría: Gestión de Datos e Información -->
        <div class="benefits-category">
          <h3 class="category-title">Gestión de Datos e Información</h3>
          <div class="benefits-row">
            <div class="benefit-item"
              data-tooltip="Realiza consultas técnicas a nuestro Agente de IA, y obtén respuestas precisas y contextualizadas basadas en la información de tu propio apiario.">
              <div class="benefit-icon">
                <i class="fas fa-robot"></i>
              </div>
              <span class="benefit-label">Sistema Experto</span>
            </div>

            <div class="benefit-item"
              data-tooltip="Visualiza la información de tus apiarios de un vistazo y toma decisiones basadas en datos concretos.">
              <div class="benefit-icon">
                <i class="fas fa-chart-pie"></i>
              </div>
              <span class="benefit-label">Visualización de Indicadores</span>
            </div>

            <div class="benefit-item"
              data-tooltip="Accede a tu información desde cualquier dispositivo, en cualquier momento y lugar, con sincronización automática.">
              <div class="benefit-icon">
                <i class="fas fa-mobile-alt"></i>
              </div>
              <span class="benefit-label">Acceso Multiplataforma</span>
            </div>

            <div class="benefit-item"
              data-tooltip="Mantén un registro completo de todas las actividades y tratamientos aplicados a tus colmenas para un seguimiento preciso.">
              <div class="benefit-icon">
                <i class="fas fa-history"></i>
              </div>
              <span class="benefit-label">Historial Detallado</span>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>

<!-- Sección de Testimonios 
<section id="testimonios" class="testimonios-section">
  <div class="container"></div>
  <div class="particles-container">
    <div class="particle particle-1"></div>
    <div class="particle particle-2"></div>
    <div class="particle particle-3"></div>
    <div class="particle particle-4"></div>
    <div class="particle particle-5"></div>
    <div class="particle particle-6"></div>
  </div>

  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Testimonios</h2>
      <p class="section-description">
        Lo que nuestros clientes dicen sobre MaiA
      </p>
    </div>

    <div class="testimonios-grid">
      <div class="testimonio-card">
        <div class="testimonio-inner">
          <div class="testimonio-header">
            <div class="testimonio-image-wrapper">
              <img src="https://files.cdn-files-a.com/uploads/10098964/400_6760f4f53c71b.jpg" alt="Foto de Carlos"
                class="testimonio-image" />
              <div class="image-decoration"></div>
            </div>
            <div class="testimonio-meta">
              <h3 class="testimonio-name">Carlos Correa</h3>
              <p class="testimonio-role">Apicultor y Asesor Apícola</p>
              <div class="experience-badge">
                <span class="experience-years">15</span>
                <span class="experience-text">años</span>
              </div>
            </div>
          </div>

          <div class="rating">
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
          </div>

          <div class="testimonio-content">
            <div class="testimonio-quote-icon">
              <i class="fa-solid fa-quote-left"></i>
            </div>
            <p class="testimonio-text">
              "La plataforma MaiA facilita enormemente el monitoreo y gestión de mis colmenas, algo que antes era muy
              laborioso. Desde que la utilizo, he reducido el tiempo de inspección en un 40% y he podido detectar
              problemas antes de que se conviertan en situaciones críticas."
            </p>
          </div>
        </div>
      </div>

      <div class="testimonio-card">
        <div class="testimonio-inner">
          <div class="testimonio-header">
            <div class="testimonio-image-wrapper">
              <img src="https://files.cdn-files-a.com/uploads/10098964/400_6760f46d02078.jpg" alt="Foto de Felipe"
                class="testimonio-image" />
              <div class="image-decoration"></div>
            </div>
            <div class="testimonio-meta">
              <h3 class="testimonio-name">Felipe Albornoz</h3>
              <p class="testimonio-role">Apicultor Profesional</p>
              <div class="experience-badge">
                <span class="experience-years">10</span>
                <span class="experience-text">años</span>
              </div>
            </div>
          </div>

          <div class="rating">
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
          </div>

          <div class="testimonio-content">
            <div class="testimonio-quote-icon">
              <i class="fa-solid fa-quote-left"></i>
            </div>
            <p class="testimonio-text">
              "Gracias a MaiA, he podido mejorar significativamente la salud de mis colmenas y aumentar la producción de
              miel en un 30%. La aplicación me alerta sobre posibles problemas y me ofrece recomendaciones precisas que
              han sido fundamentales para optimizar mi operación apícola."
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="testimonios-stats">
      <div class="stat-item">
        <div class="stat-icon">
          <img src="/img/apicultor.png" width="50px" height="50px" alt="Eficiencia apícola con MaiA">
        </div>
        <div class="stat-number">+500</div>
        <div class="stat-label">Apicultores satisfechos</div>
      </div>
      <div class="stat-item">
        <div class="stat-icon">
          <i class="fa-solid fa-pen-nib"></i>
        </div>
        <div class="stat-number">+1.000</div>
        <div class="stat-label">Apiarios registrados</div>
      </div>
      <div class="stat-item">
        <div class="stat-icon">
          <i class="fa-solid fa-chart-line"></i>
        </div>
        <div class="stat-number">+150.000</div>
        <div class="stat-label">Colmenas con trazabilidad</div>
      </div>
      <div class="stat-item">
        <div class="stat-icon">
          <i class="fa-solid fa-seedling"></i>
        </div>
        <div class="stat-number">+5.000</div>
        <div class="stat-label">Hectáreas polinizadas</div>
      </div>
      <div class="stat-item">
        <div class="stat-icon">
          <i class="fa-solid fa-map"></i>
        </div>
        <div class="stat-number">7</div>
        <div class="stat-label">Regiones de Chile con presencia de MaiA</div>
      </div>
    </div>
  </div>
</section> -->

<!-- Sección de Contacto -->
<section id="contacto" class="contacto-section">
  <div class="contacto-bg-decoration">
    <div class="honeycomb-pattern left"></div>
    <div class="honeycomb-pattern right"></div>
  </div>
  <div class="footer-glow" style="justify-content: center; align-items: center; text-align: center;"></div>

  <div class="container">
    <div class="contacto-content">
      <div class="contacto-header">
        <h2 class="section-title">Contacto</h2>
        <p class="section-description">En Bee Fractal valoramos la comunicación directa con los apicultores. Si tienes
          preguntas, comentarios o simplemente
          quieres saber más sobre cómo nuestra IA puede ayudarte, no dudes en contactarnos. ¡Estamos aquí para apoyarte!
        </p>
      </div>

      <div class="contacto-cards-container">
        <div class="contacto-card">
          <div class="contacto-card-icon">
            <i class="fa-solid fa-clock"></i>
          </div>
          <h3>Horario de atención</h3>
          <p>Lunes a Viernes: 9:00 - 18:00</p>
          <p>Sábados: 9:00 - 13:00</p>
        </div>

        <div class="contacto-card">
          <div class="contacto-card-icon">
            <i class="fa-solid fa-location-dot"></i>
          </div>
          <h3>Ubicación</h3>
          <p>Talca, Chile</p>
          <p>Región del Maule</p>
        </div>

        <div class="contacto-card">
          <div class="contacto-card-icon">
            <i class="fa-solid fa-headset"></i>
          </div>
          <h3>Soporte técnico</h3>
          <p>Disponible 24/7 para clientes premium</p>
          <p>Respuesta garantizada en menos de 2 horas</p>
        </div>
      </div>

      <div class="contacto-buttons">
        <a href="https://wa.me/56933479555" target="_blank" class="contacto-btn whatsapp-btn">
          <div class="btn-icon">
            <i class="fa-brands fa-whatsapp"></i>
          </div>
          <div class="btn-text">
            <span class="btn-label">Contáctanos por WhatsApp</span>
            <span class="btn-description">Respuesta inmediata</span>
          </div>
          <div class="btn-arrow">
            <i class="fa-solid fa-arrow-right"></i>
          </div>
        </a>

        <a href="mailto:contacto@apicheck.cl" class="contacto-btn email-btn">
          <div class="btn-icon">
            <i class="fa-solid fa-envelope"></i>
          </div>
          <div class="btn-text">
            <span class="btn-label">contacto@apicheck.cl</span>
            <span class="btn-description">Escríbenos un correo</span>
          </div>
          <div class="btn-arrow">
            <i class="fa-solid fa-arrow-right"></i>
          </div>
        </a>
      </div>
      <p class="section-description" style="text-align: center; font-weight: bold;"><i>MaiA: Inteligencia Artificial
          para la Apicultura</i>
      </p>
      <p style="text-align: center;"> Un agente de IA desarrollado por Bee Fractal SpA al servicio del apicultor
        chileno.</p>
    </div>
  </div>

  <div class="contacto-decoration">
    <div class="hex-decoration hex-1"></div>
    <div class="hex-decoration hex-2"></div>
    <div class="hex-decoration hex-3"></div>
  </div>
</section>

<footer>
  <!-- Decoraciones hexagonales -->
  <div class="footer-decoration footer-hex-1"></div>
  <div class="footer-decoration footer-hex-2"></div>
  <div class="footer-decoration footer-hex-3"></div>
  <div class="footer-decoration footer-hex-4"></div>
  <div class="footer-decoration footer-hex-5"></div>

  <!-- Efecto de luz radial -->
  <div class="footer-light"></div>

  <!-- Efecto de brillo en el borde superior -->
  <div class="footer-glow"></div>

  <!-- Contenedor principal -->
  <div class="footer-container">
    <!-- Sección del logo -->
    <div class="footer-logo-section">
      <div class="footer-logo">
        <div class="footer-logo-text">Bee Fractal SpA</div>
      </div>
      <p class="footer-description">
        Nuestra misión es contribuir a la creación de una apicultura más eficiente y resiliente. Inspirado en la
        filosofía
        Solarpunk, nuestro rol es integrar de manera armoniosa las soluciones de Inteligencia Artificial (IA) con el
        entorno
        silvestre y natural, construyendo sistemas productivos que sean más sostenibles para el futuro.
      </p>
      <div class="footer-social">
        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
        <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
      </div>
    </div>

    <!-- Productos destacados -->
    <div class="footer-column">
      <h3>Productos Destacados</h3>
      <ul class="product-list">
        <li class="product-item">
          <div class="product-icon">
            <i class="fas fa-microchip"></i>
          </div>
          <div class="product-info">
            <div class="product-name">Drone</div>
            <div class="product-desc">Prueba MaiA gratis por 16 días</div>
          </div>
        </li>
        <li class="product-item">
          <div class="product-icon">
            <i class="fas fa-tablet-alt"></i>
          </div>
          <div class="product-info">
            <div class="product-name">Worker Bee</div>
            <div class="product-desc">Acceso a todos los servicios por 12 meses</div>
          </div>
        </li>
        <li class="product-item">
          <div class="product-icon">
            <i class="fas fa-cloud"></i>
          </div>
          <div class="product-info">
            <div class="product-name">Queen</div>
            <div class="product-desc">(próximamente, con nuevas funciones)</div>
          </div>
        </li>
      </ul>
    </div>

    <!-- Información de contacto -->
    <div class="footer-column">
      <h3>Contacto</h3>
      <ul class="footer-contact-info">
        <li>
          <i class="fas fa-phone-alt"></i>
          <span>+34 123 456 789</span>
        </li>
        <li>
          <i class="fas fa-envelope"></i>
          <span>info@maia-tech.com</span>
        </li>
        <li>
          <i class="fas fa-headset"></i>
          <span>Soporte: +34 123 456 790</span>
        </li>
        <li>
          <i class="fas fa-map-marker-alt"></i>
          <span>Calle Apicultura 123, Ciudad Miel, 28001</span>
        </li>
      </ul>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="footer-wave"></div>

    <div class="footer-bottom-content">
      <p class="copyright">&copy; {{ date(format: 'Y') }} MaiA. Todos los derechos reservados.</p>
      <div class="footer-bottom-links">
        <a href="{{ route('privacidad') }}">Política de Privacidad</a>
        <a href="{{ route('terminos') }}">Términos de Uso</a>
        <a href="{{ route('cookies') }}">Política de Cookies</a>
      </div>
    </div>
  </div>
</footer>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('development-modal');
    modal.style.display = "block";

    var closeModalBtn = document.getElementById('close-modal-btn');
    closeModalBtn.onclick = function () {
      modal.style.display = "none";
    }

    window.onclick = function (event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    const benefitItems = document.querySelectorAll('.benefit-item');

    benefitItems.forEach(item => {
      item.addEventListener('click', function () {
        this.classList.toggle('tooltip-active');

        benefitItems.forEach(otherItem => {
          if (otherItem !== this) {
            otherItem.classList.remove('tooltip-active');
          }
        });
      });
    });

    document.addEventListener('click', function (e) {
      if (!e.target.closest('.benefit-item')) {
        benefitItems.forEach(item => {
          item.classList.remove('tooltip-active');
        });
      }
    });
  });

  document.addEventListener('DOMContentLoaded', function () {
    const benefitItems = document.querySelectorAll('.benefit-item');
    let activeTooltip = null;
    let tooltipTimeout = null;

    benefitItems.forEach(item => {
      item.addEventListener('click', function (e) {
        e.stopPropagation();

        if (tooltipTimeout) clearTimeout(tooltipTimeout);

        if (activeTooltip && activeTooltip !== this) {
          activeTooltip.classList.remove('tooltip-active');
        }

        this.classList.toggle('tooltip-active');

        activeTooltip = this.classList.contains('tooltip-active') ? this : null;

        if (activeTooltip && window.innerWidth <= 768) {
          document.body.classList.add('tooltip-open');

          tooltipTimeout = setTimeout(() => {
            if (activeTooltip) {
              activeTooltip.classList.remove('tooltip-active');
              activeTooltip = null;
              document.body.classList.remove('tooltip-open');
            }
          }, 4000);
        }
      });
    });

    document.addEventListener('click', function () {
      if (tooltipTimeout) clearTimeout(tooltipTimeout);

      if (activeTooltip) {
        activeTooltip.classList.remove('tooltip-active');
        activeTooltip = null;
        document.body.classList.remove('tooltip-open');
      }
    });
  });

  document.addEventListener('DOMContentLoaded', function () {
    const collapsibleHeaders = document.querySelectorAll('.collapsible-header');

    collapsibleHeaders.forEach(header => {
      header.addEventListener('click', function () {
        const container = this.closest('.collapsible-container');
        container.classList.toggle('active');

        if (container.classList.contains('active')) {
          collapsibleHeaders.forEach(otherHeader => {
            const otherContainer = otherHeader.closest('.collapsible-container');
            if (otherContainer !== container) {
              otherContainer.classList.remove('active');
            }
          });
        }
      });
    });
    document.querySelector('.collapsible-container').classList.remove('active');
  });
</script>