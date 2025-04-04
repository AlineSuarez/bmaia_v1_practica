<head>
  <link href="{{ asset('./css/components/home.css') }}" rel="stylesheet">
  <script src="{{ asset('./js/components/home.js') }}"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<button id="back-to-top" onclick="scrollToTop()"><span class="fa fa-arrow-up"></span></button>

<section id="inicio" class="hero-section">
  <div class="image-container-hero">
    <!-- Imagen de fondo -->
    <img src="{{ asset('img/home/home_panel.jpg') }}" alt="Fondo IA" class="background-image">

    <!-- Overlay gradient for depth -->
    <div class="premium-overlay"></div>

    <div class="content-wrapper">
      <div class="hero-text">
        <h1>Bienvenido a <span class="brand-name">Ma<span class="highlight">iA</span></span></h1>
        <p class="tagline">Innovación inteligente para un futuro brillante</p>
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
            MaiA es un ecosistema de apicultura inteligente que integra condiciones ambientales,
            el estado de desarrollo de las colmenas y el conocimiento de los apicultores, proporcionando
            soporte técnico para la toma de decisiones eficientes.
          </p>
          <p>
            Cualquier apicultor con smartphone puede interactuar con MaiA, llevar registros
            visuales, realizar consultas técnicas, completar registros por voz, facilitando
            las labores del apiario.
          </p>
          <!-- Enhanced feature list -->
          <div class="feature-list">
            <div class="feature-item">
              <div class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
              </div>
              <div class="feature-text">Protección de colmenas</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                  <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                </svg>
              </div>
              <div class="feature-text">Monitoreo en tiempo real</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                  <path
                    d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                  </path>
                </svg>
              </div>
              <div class="feature-text">Análisis de datos</div>
            </div>
          </div>
        </div>
        <div class="maia-video-container">
          <div class="video-showcase">
            <div class="video-hexagon">
              <video class="feature-video" autoplay loop muted playsinline
                poster="https://files.cdn-files-a.com/uploads/10098964/800_6761a84cd11e2-thumbnail.jpg">
                <source type="video/mp4" src="https://cdn-media.f-static.net/uploads/10098964/normal_6761a84cd11e2.mp4">
              </video>
              <div class="hexagon-border"></div>
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
            Simplifica la gestión de tu apiario con una herramienta intuitiva que permite registrar datos por voz, en
            tiempo real y sin necesidad de conexión a internet.
          </p>
          <!-- Enhanced benefits list -->
          <div class="benefits-container">
            <h3 class="benefits-title">Beneficios clave:</h3>
            <ul class="benefits-list">
              <li>
                <span class="benefit-check">✓</span>
                Registro de datos por voz para mayor comodidad
              </li>
              <li>
                <span class="benefit-check">✓</span>
                Funciona sin conexión a internet en el campo
              </li>
              <li>
                <span class="benefit-check">✓</span>
                Interfaz intuitiva diseñada para apicultores
              </li>
              <li>
                <span class="benefit-check">✓</span>
                Sincronización automática cuando hay conexión
              </li>
            </ul>
          </div>
        </div>
        <div class="maia-video-container">
          <div class="video-showcase">
            <div class="video-hexagon video-hexagon-alt">
              <video class="feature-video" autoplay loop muted playsinline
                poster="https://files.cdn-files-a.com/uploads/10098964/800_6761a23826d13-thumbnail.jpg">
                <source type="video/mp4" src="https://cdn-media.f-static.net/uploads/10098964/normal_6761a23826d13.mp4">
              </video>
              <div class="hexagon-border hexagon-border-alt"></div>
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
            Mejora la sostenibilidad ambiental, disminuyendo la mortandad de abejas por varroa y nosema, y pérdida por
            enjambres.
          </p>
          <!-- Enhanced sustainability metrics -->
          <div class="metrics-container">
            <div class="metric-item">
              <div class="metric-value">-30%</div>
              <div class="metric-label">Reducción en mortandad de abejas</div>
            </div>
            <div class="metric-item">
              <div class="metric-value">+25%</div>
              <div class="metric-label">Aumento en producción de miel</div>
            </div>
            <div class="metric-item">
              <div class="metric-value">-40%</div>
              <div class="metric-label">Reducción en pérdida por enjambres</div>
            </div>
          </div>
          <p class="sustainability-note">
            Nuestro sistema contribuye a la preservación de las abejas, esenciales para la polinización y biodiversidad
            global.
          </p>
        </div>
        <div class="maia-video-container">
          <div class="video-showcase">
            <!-- Fixed hexagon video container -->
            <div class="video-hexagon video-hexagon-sustainable">
              <video class="feature-video" autoplay loop muted playsinline
                poster="https://files.cdn-files-a.com/uploads/10098964/800_6761de379c2f5-thumbnail.jpg">
                <source type="video/mp4" src="https://cdn-media.f-static.net/uploads/10098964/normal_6761de379c2f5.mp4">
              </video>
              <div class="hexagon-border hexagon-border-sustainable"></div>
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
      <h1 class="section-title">Eficiencia y <span class="highlight">Productividad</span></h1>
      <div class="maia-content-row">
        <div class="maia-text">
          <p>
            MaiA optimiza el trabajo apícola, reduciendo el tiempo dedicado a tareas administrativas y permitiendo
            enfocarse en lo que realmente importa: el cuidado de las abejas y la producción de miel de calidad.
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
                    <span>Con MaiA: 65%</span>
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
                    <span>Con MaiA: 40%</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="efficiency-stat-row">
              <div class="efficiency-bar-container">
                <div class="efficiency-label">Detección de problemas</div>
                <div class="efficiency-bar-wrapper">
                  <div class="efficiency-bar efficiency-bar-before" style="width: 60%;">
                    <span>Antes: 60%</span>
                  </div>
                </div>
                <div class="efficiency-bar-wrapper">
                  <div class="efficiency-bar efficiency-bar-after" style="width: 95%;">
                    <span>Con MaiA: 95%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Key productivity features -->
          <div class="productivity-features">
            <div class="productivity-feature">
              <div class="productivity-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
              </div>
              <div class="productivity-content">
                <h4>Ahorro de tiempo</h4>
                <p>Reduce hasta un 35% el tiempo dedicado a tareas administrativas y de registro</p>
              </div>
            </div>

            <div class="productivity-feature">
              <div class="productivity-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                  <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
              </div>
              <div class="productivity-content">
                <h4>Precisión mejorada</h4>
                <p>Aumenta la precisión en la detección temprana de problemas en las colmenas</p>
              </div>
            </div>

            <div class="productivity-feature">
              <div class="productivity-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 20v-6M6 20V10M18 20V4"></path>
                </svg>
              </div>
              <div class="productivity-content">
                <h4>Escalabilidad</h4>
                <p>Gestiona desde unas pocas hasta cientos de colmenas con la misma facilidad</p>
              </div>
            </div>
          </div>
        </div>

        <div class="maia-video-container">
          <div class="efficiency-showcase">
            <div class="efficiency-image-container">
              <div class="efficiency-image-hexagon">
                <img src="https://files.cdn-files-a.com/uploads/10098964/800_6761a84cd11e2-thumbnail.jpg"
                  alt="Eficiencia apícola con MaiA" class="efficiency-image">
                <div class="hexagon-border hexagon-border-efficiency"></div>
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
        <h2 class="cta-title">Comienza a transformar tu apiario hoy</h2>
        <p class="cta-description">
          Únete a la comunidad de apicultores que están mejorando su productividad y sostenibilidad con MaiA.
        </p>
        <div class="cta-buttons">
          <a href="#contacto" class="cta-button primary">Solicitar demostración</a>
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
            Todas las herramientas necesarias para aumentar la rentabilidad del negocio apícola, ¡al alcance de todos!
          </p>
        </div>
      </div>
    </div>

    <!-- Texto introductorio adicional -->
    <div class="intro-text">
      <p>En MaiA, hemos desarrollado un conjunto completo de herramientas digitales diseñadas específicamente para
        apicultores profesionales y aficionados. Nuestras soluciones te ayudarán a optimizar cada aspecto de tu
        operación apícola, desde la planificación de inspecciones hasta el análisis predictivo de producción.</p>
      <p>Cada herramienta ha sido creada en colaboración con expertos apicultores y científicos especializados,
        garantizando soluciones prácticas y efectivas para los desafíos reales del sector.</p>
    </div>

    <ul class="herramientas-list">
      <li class="herramienta-item">
        <div class="herramienta-inner">
          <div class="herramienta-icon-wrapper">
            <div class="herramienta-icon-container">
              <i class="fa-solid fa-calendar-check"></i>
            </div>
          </div>
          <div class="herramienta-content">
            <h3>Planificación</h3>
            <p>Planifica y organiza tus inspecciones de manera eficiente con nuestro calendario inteligente que te
              alerta sobre las actividades críticas según la temporada y condiciones climáticas.</p>
            <div class="herramienta-stats">
              <div class="stat">
                <span class="stat-value">30%</span>
                <span class="stat-label">Ahorro de tiempo</span>
              </div>
            </div>
          </div>
          <div class="hexagon-decoration"></div>
        </div>
      </li>
      <li class="herramienta-item">
        <div class="herramienta-inner">
          <div class="herramienta-icon-wrapper">
            <div class="herramienta-icon-container">
              <i class="fa-solid fa-hive"></i>
            </div>
          </div>
          <div class="herramienta-content">
            <h3>Desarrollo</h3>
            <p>Chequea el desarrollo de las colmenas con seguimiento detallado. Registra la fortaleza de la colonia,
              presencia de reina, comportamiento y patrones de crecimiento con gráficos visuales.</p>
            <div class="herramienta-stats">
              <div class="stat">
                <span class="stat-value">25%</span>
                <span class="stat-label">Mejor desarrollo</span>
              </div>
            </div>
          </div>
          <div class="hexagon-decoration"></div>
        </div>
      </li>
      <li class="herramienta-item">
        <div class="herramienta-inner">
          <div class="herramienta-icon-wrapper">
            <div class="herramienta-icon-container">
              <i class="fa-solid fa-virus"></i>
            </div>
          </div>
          <div class="herramienta-content">
            <h3>Control sanitario</h3>
            <p>Vigila y controla varroa y nosema con alertas inteligentes. Nuestro sistema de detección temprana te
              ayuda a identificar problemas sanitarios antes de que se conviertan en infestaciones graves.</p>
            <div class="herramienta-stats">
              <div class="stat">
                <span class="stat-value">40%</span>
                <span class="stat-label">Reducción de pérdidas</span>
              </div>
            </div>
          </div>
          <div class="hexagon-decoration"></div>
        </div>
      </li>
      <li class="herramienta-item">
        <div class="herramienta-inner">
          <div class="herramienta-icon-wrapper">
            <div class="herramienta-icon-container">
              <i class="fa-solid fa-cloud-sun"></i>
            </div>
          </div>
          <div class="herramienta-content">
            <h3>Monitoreo</h3>
            <p>Monitorea las condiciones del clima y vegetación en tiempo real. Integración con estaciones
              meteorológicas y datos satelitales para predecir flujos de néctar y condiciones óptimas de pecoreo.</p>
            <div class="herramienta-stats">
              <div class="stat">
                <span class="stat-value">20%</span>
                <span class="stat-label">Mayor precisión</span>
              </div>
            </div>
          </div>
          <div class="hexagon-decoration"></div>
        </div>
      </li>
      <li class="herramienta-item">
        <div class="herramienta-inner">
          <div class="herramienta-icon-wrapper">
            <div class="herramienta-icon-container">
              <i class="fa-solid fa-chart-line"></i>
            </div>
          </div>
          <div class="herramienta-content">
            <h3>Predicción</h3>
            <p>Aplica modelos de predicción y simulación para optimizar la producción. Nuestros algoritmos analizan
              datos históricos y actuales para proyectar rendimientos y sugerir intervenciones estratégicas.</p>
            <div class="herramienta-stats">
              <div class="stat">
                <span class="stat-value">35%</span>
                <span class="stat-label">Aumento productivo</span>
              </div>
            </div>
          </div>
          <div class="hexagon-decoration"></div>
        </div>
      </li>
      <li class="herramienta-item">
        <div class="herramienta-inner">
          <div class="herramienta-icon-wrapper">
            <div class="herramienta-icon-container">
              <i class="fa-solid fa-calculator"></i>
            </div>
          </div>
          <div class="herramienta-content">
            <h3>Calculadoras</h3>
            <p>Usa calculadoras para nutrición y tratamientos sanitarios precisos. Determina dosis exactas de
              medicamentos, cantidades de alimentación suplementaria y programación de tratamientos según el tamaño de
              la colonia.</p>
            <div class="herramienta-stats">
              <div class="stat">
                <span class="stat-value">50%</span>
                <span class="stat-label">Menos errores</span>
              </div>
            </div>
          </div>
          <div class="hexagon-decoration"></div>
        </div>
      </li>
      <li class="herramienta-item">
        <div class="herramienta-inner">
          <div class="herramienta-icon-wrapper">
            <div class="herramienta-icon-container">
              <i class="fa-solid fa-file-download"></i>
            </div>
          </div>
          <div class="herramienta-content">
            <h3>Registros</h3>
            <p>Completa y descarga automáticamente registros FRADA y RAMEX. Cumple con los requisitos regulatorios sin
              esfuerzo, generando documentación oficial a partir de los datos que ya registras en tu operación diaria.
            </p>
            <div class="herramienta-stats">
              <div class="stat">
                <span class="stat-value">80%</span>
                <span class="stat-label">Ahorro administrativo</span>
              </div>
            </div>
          </div>
          <div class="hexagon-decoration"></div>
        </div>
      </li>
    </ul>

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
        <h2 class="section-title">Descarga la <span class="highlight">App</span></h2>
        <p class="section-description">Accede a nuestra aplicación móvil para empezar a transformar tu apicultura.
          Disponible para Android e iOS.</p>

        <div class="app-features">
          <div class="feature">
            <div class="feature-icon">
              <i class="fa-solid fa-mobile-screen-button"></i>
            </div>
            <div class="feature-text">
              <h3>Interfaz intuitiva</h3>
              <p>Diseñada para apicultores, fácil de usar en el campo</p>
            </div>
          </div>

          <div class="feature">
            <div class="feature-icon">
              <i class="fa-solid fa-wifi"></i>
            </div>
            <div class="feature-text">
              <h3>Modo offline</h3>
              <p>Trabaja sin conexión y sincroniza cuando vuelvas a tener señal</p>
            </div>
          </div>

          <div class="feature">
            <div class="feature-icon">
              <i class="fa-solid fa-bell"></i>
            </div>
            <div class="feature-text">
              <h3>Notificaciones</h3>
              <p>Alertas personalizadas sobre eventos importantes</p>
            </div>
          </div>
        </div>

        <div class="download-buttons">
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

        <div class="app-rating">
          <div class="stars">
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star-half-stroke"></i>
          </div>
          <p>4.8 de 5 estrellas basado en más de 200 reseñas</p>
        </div>
      </div>

      <div class="app-mockup">
        <div class="phone-frame">
          <div class="phone-screen">
            <img src="https://files.cdn-files-a.com/uploads/10098964/app-mockup.jpg" alt="MaiA App"
              class="app-screenshot" />
          </div>
          <div class="phone-notch"></div>
          <div class="phone-button"></div>
          <div class="phone-reflection"></div>
        </div>
        <div class="floating-screens">
          <div class="floating-screen screen-1">
            <img src="https://files.cdn-files-a.com/uploads/10098964/app-screen1.jpg" alt="Pantalla de estadísticas" />
          </div>
          <div class="floating-screen screen-2">
            <img src="https://files.cdn-files-a.com/uploads/10098964/app-screen2.jpg" alt="Pantalla de colmenas" />
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
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Cómo funciona</h2>
      <p class="section-description">MaiA recopila datos en tiempo real desde tus colmenas y los analiza para ofrecerte
        información clave que te ayudará a tomar mejores decisiones.</p>
    </div>

    <div class="process-flow">
      <div class="process-step">
        <div class="step-number">1</div>
        <div class="step-icon">
          <i class="fa-solid fa-microchip"></i>
        </div>
        <div class="step-content">
          <h3>Recopilación de datos</h3>
          <p>Los sensores instalados en tus colmenas recopilan información sobre temperatura, humedad, peso, actividad
            de las abejas y condiciones ambientales.</p>
          <div class="step-illustration">
            <div class="hive-icon">
              <i class="fa-solid fa-hive"></i>
            </div>
            <div class="data-stream">
              <span class="data-point"></span>
              <span class="data-point"></span>
              <span class="data-point"></span>
            </div>
          </div>
        </div>
      </div>

      <div class="process-connector"></div>

      <div class="process-step">
        <div class="step-number">2</div>
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
        <div class="step-number">3</div>
        <div class="step-icon">
          <i class="fa-solid fa-brain"></i>
        </div>
        <div class="step-content">
          <h3>Análisis inteligente</h3>
          <p>Nuestros algoritmos de inteligencia artificial analizan los datos, identifican patrones y generan insights
            basados en investigaciones apícolas avanzadas.</p>
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
        <div class="step-number">4</div>
        <div class="step-icon">
          <i class="fa-solid fa-mobile-screen-button"></i>
        </div>
        <div class="step-content">
          <h3>Información accionable</h3>
          <p>Recibe alertas, recomendaciones y visualizaciones claras en tu dispositivo móvil o computadora,
            permitiéndote tomar decisiones informadas en el momento adecuado.</p>
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
    </div>

    <div class="benefits-grid">
      <div class="benefit-card">
        <div class="benefit-icon">
          <i class="fa-solid fa-chart-line"></i>
        </div>
        <h3>Aumento de productividad</h3>
        <p>Incrementa la producción de miel hasta un 30% gracias a intervenciones oportunas basadas en datos reales.</p>
      </div>

      <div class="benefit-card">
        <div class="benefit-icon">
          <i class="fa-solid fa-clock"></i>
        </div>
        <h3>Ahorro de tiempo</h3>
        <p>Reduce el tiempo dedicado a inspecciones innecesarias y enfócate en las colmenas que realmente necesitan
          atención.</p>
      </div>

      <div class="benefit-card">
        <div class="benefit-icon">
          <i class="fa-solid fa-shield-virus"></i>
        </div>
        <h3>Prevención de enfermedades</h3>
        <p>Detecta tempranamente signos de problemas sanitarios antes de que afecten a toda la colonia.</p>
      </div>

      <div class="benefit-card">
        <div class="benefit-icon">
          <i class="fa-solid fa-leaf"></i>
        </div>
        <h3>Apicultura sostenible</h3>
        <p>Optimiza recursos y reduce el impacto ambiental mientras mejoras la salud de tus colonias.</p>
      </div>
    </div>
  </div>
</section>

<!-- Sección de Testimonios -->
<section id="testimonios" class="testimonios-section">
  <div class="honeycomb-bg"></div>
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
      <!-- Testimonio 1 -->
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

      <!-- Testimonio 2 -->
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
          <i class="fa-solid fa-users"></i>
        </div>
        <div class="stat-number">500+</div>
        <div class="stat-label">Apicultores satisfechos</div>
      </div>
      <div class="stat-item">
        <div class="stat-icon">
          <i class="fa-solid fa-hive"></i>
        </div>
        <div class="stat-number">10,000+</div>
        <div class="stat-label">Colmenas monitoreadas</div>
      </div>
      <div class="stat-item">
        <div class="stat-icon">
          <i class="fa-solid fa-chart-line"></i>
        </div>
        <div class="stat-number">30%</div>
        <div class="stat-label">Aumento promedio en producción</div>
      </div>
    </div>
  </div>
</section>

<!-- Sección de Contacto -->
<section id="contacto" class="contacto-section">
  <div class="contacto-bg-decoration">
    <div class="honeycomb-pattern left"></div>
    <div class="honeycomb-pattern right"></div>
  </div>

  <div class="container">
    <div class="contacto-content">
      <div class="contacto-header">
        <h2 class="section-title">Contacto</h2>
        <p class="section-description">¿Tienes preguntas? ¡Estamos aquí para ayudarte! Nuestro equipo de expertos
          apícolas está listo para brindarte soporte y asesoría personalizada.</p>
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
        <div class="footer-logo-icon">
          <i class="fas fa-seedling"></i>
        </div>
        <div class="footer-logo-text">MaiA</div>
      </div>
      <p class="footer-description">
        Soluciones innovadoras para la apicultura moderna. Monitorizamos y optimizamos la producción de miel con
        tecnología de vanguardia para garantizar la salud de las abejas y la calidad del producto final, contribuyendo a
        un ecosistema más sostenible.
      </p>
      <div class="footer-social">
        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
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
            <div class="product-name">MaiA Sensor Pro</div>
            <div class="product-desc">Monitoreo avanzado de temperatura y humedad</div>
          </div>
        </li>
        <li class="product-item">
          <div class="product-icon">
            <i class="fas fa-tablet-alt"></i>
          </div>
          <div class="product-info">
            <div class="product-name">MaiA Control</div>
            <div class="product-desc">Panel de control para apicultores</div>
          </div>
        </li>
        <li class="product-item">
          <div class="product-icon">
            <i class="fas fa-cloud"></i>
          </div>
          <div class="product-info">
            <div class="product-name">MaiA Cloud</div>
            <div class="product-desc">Almacenamiento y análisis de datos</div>
          </div>
        </li>
      </ul>
    </div>

    <!-- Horario de atención -->
    <div class="footer-column">
      <h3>Horario de Atención</h3>
      <ul class="hours-list">
        <li class="hours-item">
          <span class="day">Lunes</span>
          <span class="time">9:00 - 18:00</span>
        </li>
        <li class="hours-item">
          <span class="day">Martes</span>
          <span class="time">9:00 - 18:00</span>
        </li>
        <li class="hours-item">
          <span class="day">Miércoles</span>
          <span class="time">9:00 - 18:00</span>
        </li>
        <li class="hours-item">
          <span class="day">Jueves</span>
          <span class="time">9:00 - 18:00</span>
        </li>
        <li class="hours-item">
          <span class="day">Viernes</span>
          <span class="time">9:00 - 17:00</span>
        </li>
        <li class="hours-item">
          <span class="day">Sábado</span>
          <span class="time">10:00 - 14:00</span>
        </li>
        <li class="hours-item">
          <span class="day">Domingo</span>
          <span class="time">Cerrado</span>
        </li>
      </ul>
    </div>

    <!-- Información de contacto -->
    <div class="footer-column">
      <h3>Contacto</h3>
      <ul class="footer-contact-info">
        <li>
          <i class="fas fa-map-marker-alt"></i>
          <span>Calle Apicultura 123, Ciudad Miel, 28001</span>
        </li>
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
      </ul>
    </div>
  </div>

  <!-- Parte inferior del footer -->
  <div class="footer-bottom">
    <!-- Efecto de onda decorativa -->
    <div class="footer-wave"></div>

    <div class="footer-bottom-content">
      <p class="copyright">&copy; {{ date(format: 'Y') }} MaiA. Todos los derechos reservados.</p>
      <div class="footer-bottom-links">
        <a href="#">Política de Privacidad</a>
        <a href="#">Términos de Servicio</a>
        <a href="#">Política de Cookies</a>
      </div>
    </div>
  </div>
</footer>