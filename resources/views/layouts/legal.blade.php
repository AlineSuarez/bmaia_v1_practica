<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Documentos Legales') | Bee Fractal</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/logo_aveja.png') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/components/legal/legal.css') }}">

    @yield('styles')
</head>

<body>
    <!-- Header -->
    <header class="legal-header">
        <div class="header-container">
            <a href="{{ url('/') }}" class="logo">
                <img src="{{ asset('img/abeja.png') }}" alt="Bee Fractal Logo"
                    onerror="this.src='data:image/svg+xml;charset=utf-8,%3Csvg xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22 width%3D%2240%22 height%3D%2240%22 viewBox%3D%220 0 40 40%22%3E%3Cpath fill%3D%22%23bc9611%22 d%3D%22M20 0L5 10v20l15 10 15-10V10L20 0zm0 8l10 6v12l-10 6-10-6V14l10-6z%22%2F%3E%3C%2Fsvg%3E'">
                <span class="logo-text">Bee Fractal</span>
            </a>
            <a href="{{ url('/') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Volver al inicio
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="legal-container">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="legal-footer">
        <div class="footer-container">
            <div class="footer-links">
                <a href="{{ route('privacidad') }}">Política de Privacidad</a>
                <a href="{{ route('terminos') }}">Términos de Uso</a>
                <a href="{{ route('cookies') }}">Política de Cookies</a>
            </div>
            <div class="copyright">
                &copy; {{ date('Y') }} Bee Fractal SpA. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const contentDiv = document.querySelector('.legal-container');
            const headings = contentDiv.querySelectorAll('h2');

            if (headings.length >= 3) {
                // Crear el contenedor principal de la TOC con clase adicional para acordeón
                const tocDiv = document.createElement('div');
                tocDiv.className = 'toc toc-accordion';

                // Crear el encabezado que será clickeable
                const tocHeader = document.createElement('div');
                tocHeader.className = 'toc-header';

                // Título con ícono
                const tocTitle = document.createElement('div');
                tocTitle.className = 'toc-title';
                tocTitle.innerHTML = '<i class="fas fa-list"></i>Contenidos';

                // Botón de expandir/contraer
                const toggleBtn = document.createElement('button');
                toggleBtn.className = 'toc-toggle';
                toggleBtn.innerHTML = '<i class="fas fa-chevron-down"></i>';
                toggleBtn.setAttribute('aria-expanded', 'false');

                // Contenedor del contenido del acordeón
                const tocContent = document.createElement('div');
                tocContent.className = 'toc-content';
                tocContent.style.display = 'none';
                tocContent.style.maxHeight = '0';
                tocContent.style.overflow = 'hidden';
                tocContent.style.transition = 'max-height 0.3s ease-out';

                // Lista de contenidos
                const tocList = document.createElement('ul');
                tocList.className = 'toc-list';

                // Agregar cada encabezado a la lista
                headings.forEach((heading, index) => {
                    if (!heading.id) {
                        heading.id = 'section-' + (index + 1);
                    }

                    const listItem = document.createElement('li');
                    const link = document.createElement('a');
                    link.href = '#' + heading.id;
                    link.textContent = heading.textContent;

                    // Cerrar el acordeón cuando se hace clic en un enlace
                    link.addEventListener('click', function () {
                        toggleToc(false);
                    });

                    listItem.appendChild(link);
                    tocList.appendChild(listItem);
                });

                // Función para alternar la visibilidad
                function toggleToc(forceState) {
                    const isExpanded = forceState !== undefined ? forceState :
                        tocContent.style.display === 'none';

                    if (isExpanded) {
                        tocContent.style.display = 'block';
                        // Establecer altura máxima para animación
                        setTimeout(() => {
                            tocContent.style.maxHeight = tocContent.scrollHeight + 'px';
                        }, 10);
                        toggleBtn.innerHTML = '<i class="fas fa-chevron-down"></i>';
                        toggleBtn.setAttribute('aria-expanded', 'true');
                    } else {
                        tocContent.style.maxHeight = '0';
                        setTimeout(() => {
                            tocContent.style.display = 'none';
                        }, 300);
                        toggleBtn.innerHTML = '<i class="fas fa-chevron-down"></i>';
                        toggleBtn.setAttribute('aria-expanded', 'false');
                    }
                }

                toggleBtn.addEventListener('click', function () {
                    toggleToc();
                });

                tocHeader.appendChild(tocTitle);
                tocHeader.appendChild(toggleBtn);
                tocContent.appendChild(tocList);
                tocDiv.appendChild(tocHeader);
                tocDiv.appendChild(tocContent);

                const firstH1 = contentDiv.querySelector('h1');
                if (firstH1) {
                    firstH1.parentNode.insertBefore(tocDiv, firstH1.nextSibling);
                } else {
                    contentDiv.insertBefore(tocDiv, contentDiv.firstChild);
                }
            }
        });
    </script>
    @yield('scripts')
</body>

</html>