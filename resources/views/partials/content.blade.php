<head>
  <link href="{{ asset('./css/components/home.css') }}" rel="stylesheet">
  <script src="{{ asset('./js/components/home.js') }}"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<button id="back-to-top"><span class="fa fa-arrow-up"></span></button>

<!-- Modal de bienvenida al ecosistema -->
<div id="development-modal" class="development-modal" style="display:none;">
  <div class="development-modal-content">
    <div class="development-modal-header">
      <h2>¡Bienvenido al ecosistema B-MaiA!</h2>
    </div>
    <div class="development-modal-body">
      <div class="hexagon-pattern"></div>

      <div class="modal-particle modal-particle-1"></div>
      <div class="modal-particle modal-particle-2"></div>
      <div class="modal-particle modal-particle-3"></div>
      <div class="modal-particle modal-particle-4"></div>

      <div class="development-icon">
        <img src="{{ asset('img/abeja.png') }}" width="160px" height="130px" alt="Fondo apícola" class="development-icon-image">
      </div>
      <p>¡Descubre B-Ma<span class="highlight">iA</span>!</p>
      <p>El nuevo ecosistema apícola al servicio de la Apicultura Chilena.</p>
      <p>En Bee Fractal hemos desarrollado una plataforma que te ayudará a gestionar tus colmenas de forma más
        eficiente, sencilla y práctica.</p>
      <p style="font-weight:bold;">¡Mantente informado y aprovecha todas las herramientas que tenemos para ti!</p>
      <div style="margin-top: 20px;">
        <input type="checkbox" id="dont-show-again">
        <label for="dont-show-again" style="font-size: 0.95rem;">No volver a mostrar</label>
      </div>
    </div>
    <div class="development-modal-footer">
      <button id="close-modal-btn">Entendido</button>
    </div>
  </div>
</div>

<section id="inicio" class="hero-section">
  <div class="image-container-hero">
    <!-- Imagen de fondo -->
    <img src="{{ asset('img/home/home_panel.jpg') }}" alt="Fondo Apícola" class="background-image">

    <div class="premium-overlay"></div>

    <div class="content-wrapper">
      <div class="hero-text">
        <h1>Bienvenido a B-Ma<span class="highlight">iA</span></span></h1>
        <p class="tagline">Ecosistema de Apicultura Inteligente</p>
      </div>
    </div>

    <!-- Decorative elements -->
    <div class="floating-particles"></div>
  </div>
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
      <h2 style="font-weight: bold; margin-bottom: 30px;">B-MaiA la forma más eficiente de gestionar tus colmenas</h2>
      <p>Plataforma todo en uno, 100% digital, que integra herramientas avanzadas para gestionar tus colmenas de
        manera más
        fácil y rápida, adaptándose a las particularidades de la apicultura chilena.</p>
    </div>

    <!-- Sección de B-MAIA Estándar -->

    <div class="herramientas-grid-container">
      <!-- Sección Superior: Imagen, Título y Botón -->
      <div class="herramientas-header-section">
        <div class="header-left">
          <div class="platform-image-container">
            <img src="{{ asset('img/logo-2.png') }}" alt="Plataforma de Gestión Apícola" class="platform-image">
          </div>
          <div class="title-button-row">
            <h2 class="platform-title">Plataforma de Gestión Apícola</h2>
            <div class="cta-buttons">
              <a href="{{ route('register') }}" class="cta-button primary">Comienza tu prueba gratis</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Grid de Herramientas -->
      <div class="herramientas-grid-wrapper">
        <ul class="herramientas-grid">

          <!-- Trazabilidad de Colmenas -->
          <li class="herramienta-card">
            <div class="herramienta-header">
              <div class="herramienta-icon">
                <i class="fa-solid fa-boxes-stacked"></i>
              </div>
              <h3>Trazabilidad de colmenas</h3>
            </div>
            <div class="herramienta-body">
              <p>Garantiza la calidad y autenticidad de tu miel. Podrás registrar todos los apiarios que necesites
                durante la temporada,
                manteniendo la trazabilidad individual de cada colmena.
              </p>
              <div class="herramienta-stat">
                <span class="stat-value">100%</span>
                <span class="stat-label">Trazabilidad de colmenas</span>
              </div>
            </div>
          </li>
          <!-- Cuaderno de Campo -->
          <li class="herramienta-card">
            <div class="herramienta-header">
              <div class="herramienta-icon">
                <i class="fa-solid fa-file-alt"></i>
              </div>
              <h3>Cuaderno de Campo</h3>
            </div>
            <div class="herramienta-body">
              <p>Completa y descarga el cuaderno de campo y registro de movimiento de colmenas fiscalizado por el SAG.
                Cumple con los
                requisitos regulatorios, generando documentación oficial a partir de los datos que registras. Evita
                multas, solo tienes
                que imprimir y firmar.
              </p>
              <div class="herramienta-stat">
                <span class="stat-value">100%</span>
                <span class="stat-label">Más Práctico</span>
              </div>
            </div>
          </li>

          <!-- Plan de trabajo Anual -->
          <li class="herramienta-card">
            <div class="herramienta-header">
              <div class="herramienta-icon">
                <i class="fa-solid fa-calendar-check"></i>
              </div>
              <h3>Plan de trabajo Anual</h3>
            </div>
            <div class="herramienta-body">
              <p>Planifica y organiza tus inspecciones y tareas de manera eficiente, asegurando un monitoreo constante
                de tu progreso.
                Recibe notificaciones sobre tareas pendientes y alertas inmediatas ante eventos críticos, lo que te
                permitirá tomar
                decisiones rápidas y mejorar la gestión de tu plan de trabajo.
              </p>
              <div class="herramienta-stat">
                <span class="stat-value">50%</span>
                <span class="stat-label">Ahorro de tiempo</span>
              </div>
            </div>
          </li>

          <!-- Control Sanitario -->
          <li class="herramienta-card">
            <div class="herramienta-header">
              <div class="herramienta-icon">
                <i class="fa-solid fa-chart-line"></i>
              </div>
              <h3>Indicadores</h3>
            </div>
            <div class="herramienta-body">
              <p>Visualiza en tiempo real los indicadores de tu producción, desde el rendimiento de miel hasta la
                cantidad de tratamientos aplicados en tus apiarios y colmenas. Podrás optimizar tus decisiones y
                maximizar la eficiencia de tus manejos productivos.
              </p>
              <div class="herramienta-stat">
                <span class="stat-value">30%</span>
                <span class="stat-label">Reducción de mortandad</span>
              </div>
            </div>
          </li>

          <!-- Clima y Geolocalización -->
          <li class="herramienta-card">
            <div class="herramienta-header">
              <div class="herramienta-icon">
                <i class="fa-solid fa-cloud-sun"></i>
              </div>
              <h3>Clima y Geolocalización</h3>
            </div>
            <div class="herramienta-body">
              <p>Monitorea las condiciones climáticas y geográficas de tus lugares de producción. Podrás planificar y
                organizar la
                ubicación de tus colmenas con mayor precisión.</p>
              <div class="herramienta-stat">
                <span class="stat-value">85%</span>
                <span class="stat-label">Precisión de monitoreo</span>
              </div>
            </div>
          </li>

          <!-- Imagen lateral junto a la última tarjeta -->
          <li class="herramienta-image-showcase">
            <div class="showcase-image-container">
              <img src="{{ asset('img/apicultura.jpg') }}" alt="Acompañamiento B-MaiA" class="showcase-image">
              <div class="image-overlay">
                <div class="overlay-content">
                  <h4>Te acompañamos en tu gestión</h4>
                  <p>
                    En B-MaiA, te apoyamos con herramientas inteligentes y modelos de simulación para que tomes siempre
                    las mejores
                    decisiones para tus colmenas. ¡Cuenta con nosotros en cada etapa de tu producción apícola!
                  </p>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <div class="bmaia-plan">
      <div class="decoration-hexagon"></div>
      <div class="decoration-hexagon"></div>
      <div class="decoration-hexagon"></div>
      <div class="shine-effect"></div>

      <h2>¿Qué es B-MAIA Estándar?</h2>
      <p>B-MAIA Estándar es nuestra solución básica, diseñada para apicultores que buscan una gestión eficiente de sus
        colmenas sin complicaciones. Incluye herramientas esenciales para el monitoreo y la gestión de apiarios.</p>
    </div>

    <!-- Sección de B-MAIA Plus -->

    <div class="herramientas-grid-container">
      <!-- Sección Superior: Imagen, Título y Botón -->
      <div class="herramientas-header-section">
        <div class="header-left">
          <div class="platform-image-container">
            <img src="{{ asset('img/logo-3.png') }}" alt="Atlas de Flora Melífera y Polinización"
              class="platform-image">
          </div>
          <div class="title-button-row">
            <h2 class="platform-title">Atlas de Flora Melífera y Polinización</h2>
            <div class="cta-buttons">
              <a class="cta-button primary disabled">Próximamente</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Grid de Herramientas: 5 tarjetas + imagen -->
      <div class="herramientas-grid-wrapper">
        <ul class="herramientas-grid">
          <!-- Mapa Apibotánico -->
          <li class="herramienta-card">
            <div class="herramienta-header">
              <div class="herramienta-icon">
                <i class="fa-solid fa-map"></i>
              </div>
              <h3>Mapa Apibotánico</h3>
            </div>
            <div class="herramienta-body">
              <p>Explora el mapa apibotánico, una herramienta visual que te permite identificar las mejores zonas para
                ubicar tus
                colmenas. Basado en datos geoespaciales y factores ambientales, este mapa te ayudará a seleccionar áreas
                óptimas para
                maximizar tu producción.
              </p>
            </div>
          </li>

          <!-- Predicción de Rendimiento -->
          <li class="herramienta-card">
            <div class="herramienta-header">
              <div class="herramienta-icon">
                <i class="fa-solid fa-chart-line"></i>
              </div>
              <h3>Predicción de Rendimiento</h3>
            </div>
            <div class="herramienta-body">
              <p>Consulta el modelo de predicción de rendimiento de miel, que integra datos históricos, ambientales y
                productivos para
                estimar el potencial melífero por zona. Visualiza de forma clara y georreferenciada las áreas de mayor
                productividad, y
                maximiza el retorno de cada temporada.</p>
            </div>
          </li>

          <!-- Mapa Frutícola -->
          <li class="herramienta-card">
            <div class="herramienta-header">
              <div class="herramienta-icon">
                <i class="fa-solid fa-apple-alt"></i>
              </div>
              <h3>Mapa Frutícola</h3>
            </div>
            <div class="herramienta-body">
              <p>Accede al mapa del catastro frutícola que muestra la distribución georreferenciada de cultivos que
                requieren servicios
                de polinización. Esta herramienta te permite planificar las rutas de polinización y optimizar la
                coordinación con
                agricultores.</p>
            </div>
          </li>

          <!-- Flora Melífera -->
          <li class="herramienta-card">
            <div class="herramienta-header">
              <div class="herramienta-icon">
                <i class="fa-solid fa-leaf"></i>
              </div>
              <h3>Flora Melífera</h3>
            </div>
            <div class="herramienta-body">
              <p>Consulta la base de datos de flora melífera, con información detallada sobre las especies que
                contribuyen a la
                producción apícola. Cada ficha incluye su valor de néctar o polen, características botánicas y un
                calendario fenológico
                que indica los periodos de floración.
              </p>
            </div>
          </li>

          <!-- Predicción de Floraciones -->
          <li class="herramienta-card">
            <div class="herramienta-header">
              <div class="herramienta-icon">
                <i class="fa-solid fa-chart-simple"></i>
              </div>
              <h3>Predicción de Floraciones</h3>
            </div>
            <div class="herramienta-body">
              <p>Utiliza el modelo de predicción de floraciones para Tevo, Quillay, Avellano, Corontillo, Litre, Peumo y
                Tralhuén. El
                algoritmo integra datos ambientales históricos y en condiciones actuales. Podrás anticipar los flujos de
                néctar, y
                planificar con precisión las ventanas productivas de cada zona, maximizando así el potencial de tu
                producción.</p>
            </div>
          </li>

          <!-- Imagen lateral junto a la última tarjeta -->
          <li class="herramienta-image-showcase">
            <div class="showcase-image-container">
              <img src="{{ asset('img/apicultura-2.jpg') }}" alt="Acompañamiento B-MaiA Plus" class="showcase-image">
              <div class="image-overlay">
                <div class="overlay-content">
                  <h4>Te acompañamos en tu crecimiento</h4>
                  <p>
                    Con B-MaiA Plus, no solo accedes a herramientas avanzadas, sino también a nuestro acompañamiento
                    experto en
                    cada etapa de tu producción. ¡Estamos contigo en cada decisión importante!
                  </p>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <div class="bmaia-plan">
      <div class="decoration-hexagon"></div>
      <div class="decoration-hexagon"></div>
      <div class="decoration-hexagon"></div>
      <div class="shine-effect"></div>

      <h2>¿Qué es B-MAIA Plus?</h2>
      <p>
        B-MAIA Plus es nuestra solución avanzada para apicultores que buscan ir más allá en la gestión de sus colmenas.
        No solo accedes a herramientas inteligentes y modelos predictivos, sino también a nuestro acompañamiento experto
        en
        cada etapa de tu producción.
        Recibe recomendaciones personalizadas, soporte dedicado y maximiza el potencial de tu apiario con tecnología de
        vanguardia.
        ¡Estamos contigo en cada decisión importante!
      </p>
    </div>

    <!-- Sección de B-MAIA PRO -->

    <div class="herramientas-grid-container">
      <!-- Sección Superior: Imagen, Título y Botón -->
      <div class="herramientas-header-section">
        <div class="header-left">
          <div class="platform-image-container">
            <img src="{{ asset('img/logo-4.png') }}" alt="Agente de Inteligencia Artificial" class="platform-image">
          </div>
          <div class="title-button-row">
            <h2 class="platform-title">Agente de Inteligencia Artificial</h2>
            <div class="cta-buttons">
              <a class="cta-button primary">En fase de desarrollo</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Grid de Herramientas: 5 tarjetas + imagen -->
      <div class="herramientas-grid-wrapper">
        <ul class="herramientas-grid">
          <!-- Registros por Voz -->
          <li class="herramienta-card">
            <div class="herramienta-header">
              <div class="herramienta-icon">
                <i class="fa-solid fa-microphone"></i>
              </div>
              <h3>Registros por Voz</h3>
            </div>
            <div class="herramienta-body">
              <p>A través de nuestra aplicación móvil registra conversaciones, agenda tareas, agrega información de los
                manejos aplicados
                y registra observaciones de tus visitas de inspección, todo mediante comandos de voz. Utiliza la función
                de inspección
                guiada para principiantes y profesionales
              </p>
            </div>
          </li>

          <!-- Sistema Experto -->
          <li class="herramienta-card">
            <div class="herramienta-header">
              <div class="herramienta-icon">
                <i class="fa-solid fa-brain"></i>
              </div>
              <h3>Sistema Experto</h3>
            </div>
            <div class="herramienta-body">
              <p>Realiza consultas técnicas a nuestro Sistema Experto y Agente de IA, y obtén respuestas precisas,
                basadas en la
                información de tu apiario y nuestra base de conocimiento. Tendrás un asesor experto las 24 horas del
                día, 365 días del
                año.</p>
            </div>
          </li>

          <!-- Monitoreo por Imágenes -->
          <li class="herramienta-card">
            <div class="herramienta-header">
              <div class="herramienta-icon">
                <i class="fa-solid fa-camera"></i>
              </div>
              <h3>Monitoreo por Imágenes</h3>
            </div>
            <div class="herramienta-body">
              <p>Monitorea tus colmenas de forma inteligente con nuestra app equipada con tecnología de reconocimiento
                de imágenes.
                Captura fotos en terreno y recibe un análisis automático sobre el estado sanitario y posibles anomalías.
                La app
                transforma tus registros visuales en datos útiles para apoyar tus decisiones.</p>
            </div>
          </li>

          <!-- Imagen lateral junto a la última tarjeta -->
          <li class="herramienta-image-showcase">
            <div class="showcase-image-container">
              <img src="{{ asset('img/apicultura-3.png') }}" alt="Acompañamiento B-MaiA PRO" class="showcase-image">
              <div class="image-overlay">
                <div class="overlay-content">
                  <h4>Te acompañamos a nivel profesional</h4>
                  <p>
                    Con B-MaiA PRO, cuentas con el respaldo de inteligencia artificial y asesoría experta para llevar tu
                    apicultura al siguiente nivel.
                  </p>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <div class="bmaia-plan">
      <div class="decoration-hexagon"></div>
      <div class="decoration-hexagon"></div>
      <div class="decoration-hexagon"></div>
      <div class="shine-effect"></div>

      <h2>¿Qué es B-MAIA PRO?</h2>
      <p>
        B-MAIA PRO es nuestra solución más avanzada, pensada para apicultores profesionales que buscan llevar su
        producción
        al siguiente nivel.
        Integra inteligencia artificial, análisis avanzados y asesoría experta para optimizar cada aspecto de tu gestión
        apícola.
        Recibe soporte personalizado, recomendaciones técnicas y acceso a herramientas innovadoras que te permitirán
        enfrentar cualquier desafío profesional con confianza y eficiencia.
      </p>
    </div>

    <!-- Nota informativa final -->
    <div class="info-footer">
      <div class="info-icon">
        <i class="fa-solid fa-lightbulb"></i>
      </div>
      <p>Todas nuestras herramientas están disponibles en la plataforma B-MaiA y se sincronizan automáticamente entre
        dispositivos. Los datos se actualizan en tiempo real, permitiéndote tomar decisiones informadas desde cualquier
        lugar y en cualquier momento.</p>
    </div>
  </div>
</section>

<!-- Sección de Cómo Funciona -->
<section id="como-funciona" class="como-funciona-section">
  <h2 class="section-title">¿Quieres saber más sobre B-MaiA?</h2>
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
        <p style="margin-bottom: 15px; font-size: 1rem; color: #555;">Nuestro sistema analiza tus datos, junto con
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
          Con B-MaiA, optimiza tu tiempo, protege tus abejas y produce miel de manera más inteligente.
          Todo sincronizado de forma segura en la nube y accesible desde cualquier dispositivo.
        </div>

        <div class="contenedor-img">
          <img src="{{ asset('img/home/procesar.png') }}" alt="Fondo Apícola" class="img-flotante">
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
              <h3>Registro por voz <span class="proximamente-pro">(próximamente en B-MAIA PRO)</span></h3>
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
              <h3>Análisis avanzado <span class="proximamente-pro">(próximamente en B-MAIA PRO)</span></h3>
              <p>B-MaiA integra y analiza los datos registrados, correlacionando el estado de desarrollo de las
                colmenas,
                las
                condiciones
                ambientales del apiario, y el registro de los manejos aplicados. El sistema identifica
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
              <h3>Información accionable <span class="proximamente-pro">(próximamente en B-MAIA PRO)</span></h3>
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
            <div class="step-number">7</div>
            <div class="step-icon">
              <i class="fa-solid fa-chart-line"></i>
            </div>
            <div class="step-content">
              <h3>Sistema de Mejora Continua</h3>
              <p>B-MaiA genera un historial detallado de tus actividades y manejos aplicados en cada apiario, por
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
        <h2 class="section-title" style="margin-top: 20px; font-size: 30px !important;">Beneficios de B-MaiA</h2>
        <div class="collapse-icon">
          <i class="fa-solid fa-chevron-down"></i>
        </div>
      </div>

      <!-- Contenido visible incluso cuando está colapsado -->
      <div class="benefits-preview"
        style="padding: 0 20px 20px; text-align: center; margin-bottom: 20px; border-bottom: 1px dashed rgba(255, 143, 0, 0.2);">
        <p style="margin-bottom: 15px; font-size: 1rem; color: #555;">Descubre cómo B-MaiA mejora la productividad,
          salud
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

<!-- Sección de Logros -->
<section id="logros" class="logros-section">
  <!-- Partículas decorativas de fondo -->
  <div class="logros-particles">
    <div class="particle particle-1"></div>
    <div class="particle particle-2"></div>
    <div class="particle particle-3"></div>
    <div class="particle particle-4"></div>
    <div class="particle particle-5"></div>
    <div class="particle particle-6"></div>
  </div>

  <div class="logros-container">
    <div class="logros-header">
      <div class="award-icon">
        <i class="fa-solid fa-trophy"></i>
        <div class="award-glow"></div>
      </div>

      <h2 class="section-title">¡Orgullosos de nuestros logros!</h2>

      <div class="achievement-badge-header">
        <span class="badge-year">2024</span>
        <span class="badge-program">Primera Generación</span>
      </div>

      <p class="logros-description">
        Nuestro Ecosistema de Apicultura Inteligente fue seleccionado para formar parte de la Primera Generación del
        Programa de Incubación Polo Maule Innova Agro 4.0 - 2024. Este programa, ejecutado por Fundación Innova con el
        respaldo del Gobierno Regional del Maule, nos permitió desarrollar y validar nuestra propuesta tecnológica para
        la apicultura chilena.
      </p>
    </div>

    <div class="logros-content-wrapper">
      <!-- Línea decorativa conectora -->
      <div class="connection-line"></div>

      <div class="logros-images-container">
        <div class="logro-image-wrapper">
          <div class="image-frame">
            <img src="{{ asset('img/logro-1.png') }}" alt="Gobierno Regional del Maule" class="logro-image">
            <div class="image-shine"></div>
          </div>
          <div class="logo-label">Gobierno Regional del Maule</div>
        </div>

        <div class="logro-image-wrapper">
          <div class="image-frame">
            <img src="{{ asset('img/logro-2.png') }}" alt="Fundación Innova" class="logro-image">
            <div class="image-shine"></div>
          </div>
          <div class="logo-label">Fundación Innova</div>
        </div>

        <div class="logro-image-wrapper">
          <div class="image-frame">
            <img src="{{ asset('img/logro-3.png') }}" alt="Maule Agro Futuro" class="logro-image">
            <div class="image-shine"></div>
          </div>
          <div class="logo-label">Maule Agro Futuro</div>
        </div>

        <div class="logro-image-wrapper">
          <div class="image-frame">
            <img src="{{ asset('img/logro-4.png') }}" alt="Polo Maule Innova" class="logro-image">
            <div class="image-shine"></div>
          </div>
          <div class="logo-label">Polo Maule Innova</div>
        </div>
      </div>
    </div>

    <div class="logros-footer">
      <div class="footer-decoration">
        <div class="hex-decoration hex-left"></div>
        <div class="hex-decoration hex-right"></div>
      </div>
      <p>Este reconocimiento valida nuestro compromiso con la innovación y el desarrollo tecnológico al servicio de la
        apicultura nacional.</p>

      <div class="validation-stamp">
        <i class="fa-solid fa-certificate"></i>
        <span>Validado oficialmente</span>
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
  <div class="footer-glow" style="justify-content: center; align-items: center; text-align: center;"></div>

  <div class="container">
    <div class="contacto-content">
      <div class="contacto-header">
        <h2 class="section-title">Contacto</h2>
        <p class="section-description">En Bee Fractal valoramos la comunicación directa con los apicultores. Si tienes
          preguntas, comentarios o simplemente
          quieres saber más sobre cómo nuestra plataforma puede ayudarte, no dudes en contactarnos. ¡Estamos aquí para
          apoyarte!
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
        <a href="https://wa.me/56977632303?text=Hola%2C%20me%20gustar%C3%ADa%20recibir%20informaci%C3%B3n%20detallada%20sobre%20las%20soluciones%20y%20servicios%20de%20B-MaiA.%20Agradezco%20su%20atenci%C3%B3n." target="_blank" class="contacto-btn whatsapp-btn" aria-label="Contáctanos por WhatsApp">
          <div class="btn-icon">
            <i class="fa-brands fa-whatsapp" style="font-size: 1em !important;"></i>
          </div>
          <div class="btn-text">
            <span class="btn-label">Contáctanos por WhatsApp</span>
            <span class="btn-description">Atención profesional y respuesta inmediata</span>
          </div>
          <div class="btn-arrow">
            <i class="fa-solid fa-arrow-right"></i>
          </div>
        </a>

        <a href="mailto:contacto@bmaia.cl" class="contacto-btn email-btn">
          <div class="btn-icon">
            <i class="fa-solid fa-envelope" style="font-size: 1em !important;"></i>
          </div>
          <div class="btn-text">
            <span class="btn-label">contacto@bmaia.cl</span>
            <span class="btn-description">Escríbenos un correo</span>
          </div>
          <div class="btn-arrow">
            <i class="fa-solid fa-arrow-right"></i>
          </div>
        </a>
      </div>
      <p class="section-description" style="text-align: center; font-weight: bold;">B-MaiA: Gestión apícola al
        servicio de la Apicultura.
      </p>
      <p style="text-align: center;"> Un sistema desarrollado por Bee Fractal SpA al servicio del apicultor
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
        Solarpunk, nuestro rol es integrar de manera armoniosa las soluciones tecnológicas con el
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
            <div class="product-desc">Prueba B-MaiA gratis por 16 días</div>
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
          <i class="fas fa-envelope"></i>
          <span>contacto@bmaia.cl</span>
        </li>
        <li>
          <i class="fas fa-headset"></i>
          <span>Soporte: +56 9 7763 2303</span>
        </li>
        <li>
          <i class="fas fa-map-marker-alt"></i>
          <span>54 Oriente nº 1145, Talca</span>
        </li>
      </ul>
    </div>
  </div>

  <div class="footer-bottom">

    <div class="footer-bottom-content">
      <p class="copyright">&copy; {{ date(format: 'Y') }} B-MaiA. Todos los derechos reservados.</p>
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
    const firstCollapsible = document.querySelector('.collapsible-container');
    if (firstCollapsible) {
      firstCollapsible.classList.remove('active');
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('development-modal');
    var closeModalBtn = document.getElementById('close-modal-btn');
    var dontShowAgain = document.getElementById('dont-show-again');

    // Mostrar solo si no está marcado en localStorage
    if (!localStorage.getItem('hideWelcomeModal')) {
      modal.style.display = "block";
    }

    closeModalBtn.onclick = function () {
      if (dontShowAgain.checked) {
        localStorage.setItem('hideWelcomeModal', '1');
      }
      modal.style.display = "none";
    }

    window.onclick = function (event) {
      if (event.target == modal) {
        if (dontShowAgain.checked) {
          localStorage.setItem('hideWelcomeModal', '1');
        }
        modal.style.display = "none";
      }
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('open-register-btn');
    if (btn) {
      btn.addEventListener('click', function () {
        // Reemplaza 'register-modal' por el id real de tu modal de registro
        document.getElementById('register-modal').style.display = 'block';
      });
    }
  });
</script>