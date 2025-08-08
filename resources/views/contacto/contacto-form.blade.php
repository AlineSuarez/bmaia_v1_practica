@extends('layouts.contact')

@section('title', 'B-MaiA | Contáctanos')

@section('content')
    <section class="contacto-section contacto-form-only">
        <div class="contacto-bg-decoration"></div>

        <div class="container">
            <div class="contacto-content">
                <a href="{{ url('/') }}" class="btn btn-secondary" style="margin-bottom: 18px;">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>
                <div class="contacto-header">
                    <h2 class="section-title">Contáctanos</h2>
                    <p class="section-description">Cuéntanos en qué podemos ayudarte. Responderemos a la brevedad.</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success" role="alert">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <strong>Por favor corrige los siguientes errores:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="contact-card">
                    <div class="contact-card-header">
                        <div class="contact-card-icon">
                            <i class="fa-solid fa-envelope-open-text"></i>
                        </div>
                        <div>
                            <h3>Formulario de Contacto</h3>
                            <p>Atención profesional y cercana</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('contacto.enviar') }}" novalidate class="contact-form">
                        @csrf

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="nombre">Nombre completo <span class="req">*</span></label>
                                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                                    minlength="2" maxlength="120" placeholder="Ej.: Juan Pérez">
                                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group">
                                <label for="email">Correo electrónico <span class="req">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                    maxlength="160" placeholder="tu@correo.com" inputmode="email">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="tel" name="telefono" id="telefono" value="{{ old('telefono') }}" maxlength="20"
                                    placeholder="+56 9 1234 5678" inputmode="tel">
                                @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group">
                                <label for="empresa">Empresa/Organización</label>
                                <input type="text" name="empresa" id="empresa" value="{{ old('empresa') }}" maxlength="160"
                                    placeholder="Opcional">
                                @error('empresa')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group">
                                <label for="tipo">Tipo de consulta <span class="req">*</span></label>
                                <select name="tipo" id="tipo" required>
                                    <option value="" disabled {{ old('tipo') ? '' : 'selected' }}>Selecciona una opción
                                    </option>
                                    <option value="informacion" {{ old('tipo') === 'informacion' ? 'selected' : '' }}>
                                        Información</option>
                                    <option value="soporte" {{ old('tipo') === 'soporte' ? 'selected' : '' }}>Soporte técnico
                                    </option>
                                    <option value="ventas" {{ old('tipo') === 'ventas' ? 'selected' : '' }}>Ventas</option>
                                    <option value="otros" {{ old('tipo') === 'otros' ? 'selected' : '' }}>Otros</option>
                                </select>
                                @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group">
                                <label for="asunto">Asunto <span class="req">*</span></label>
                                <input type="text" name="asunto" id="asunto" value="{{ old('asunto') }}" required
                                    minlength="3" maxlength="150" placeholder="Resumen de tu consulta">
                                @error('asunto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mensaje">Mensaje <span class="req">*</span></label>
                            <textarea name="mensaje" id="mensaje" rows="6" required minlength="10" maxlength="2000"
                                placeholder="Cuéntanos en detalle cómo podemos ayudarte...">{{ old('mensaje') }}</textarea>
                            <div id="mensaje-counter" class="char-counter">0 / 2000</div>
                            @error('mensaje')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-consent">
                            <label class="checkbox">
                                <input type="checkbox" name="acepta_politica" value="1" {{ old('acepta_politica') ? 'checked' : '' }} required>
                                <span>Acepto la <a href="{{ route('privacidad') }}" target="_blank" rel="noopener">Política
                                        de Privacidad</a></span>
                            </label>
                            @error('acepta_politica')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Honeypot -->
                        <div class="hp">
                            <label for="website">No completar este campo</label>
                            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                        </div>
                        <input type="hidden" name="form_started_at" value="{{ now()->timestamp }}">

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-paper-plane"></i> Enviar
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fa-solid fa-eraser"></i> Limpiar
                            </button>
                        </div>
                    </form>
                </div>

                <div class="social-media">
                    <h3>Síguenos</h3>
                    <div class="social-icons">
                        <a class="social-icon" href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a class="social-icon" href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a class="social-icon" href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="contacto-decoration">
            <div class="hex-decoration hex-1"></div>
            <div class="hex-decoration hex-2"></div>
            <div class="hex-decoration hex-3"></div>
        </div>
    </section>

    <script>
        (function () {
            const textarea = document.getElementById('mensaje');
            const counter = document.getElementById('mensaje-counter');
            if (!textarea || !counter) return;
            const update = () => {
                const max = parseInt(textarea.getAttribute('maxlength') || '2000', 10);
                const len = textarea.value.length;
                counter.textContent = `${len} / ${max}`;
            };
            textarea.addEventListener('input', update);
            update();
        })();
    </script>
@endsection