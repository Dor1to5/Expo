<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-success text-white text-center py-4">
                    <i class="fas fa-user-plus fa-2x mb-3"></i>
                    <h3 class="fw-bold mb-0">Crear Cuenta</h3>
                    <p class="mb-0 opacity-75">Únete a nuestra comunidad</p>
                </div>
                
                <div class="card-body p-5">
                    <!-- Mensajes de error/éxito -->
                    <?php if (isset($mensaje_error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?= htmlspecialchars($mensaje_error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($errores_validacion)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Por favor, corrige los siguientes errores:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($errores_validacion as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Formulario de registro -->
                    <form action="/registro" method="POST" id="registroForm" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <!-- Nombre completo -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label fw-semibold">
                                    <i class="fas fa-user me-2 text-success"></i>
                                    Nombre *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>"
                                       placeholder="Tu nombre"
                                       required
                                       autocomplete="given-name">
                                <div class="invalid-feedback">
                                    El nombre es obligatorio.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="apellidos" class="form-label fw-semibold">
                                    <i class="fas fa-user me-2 text-success"></i>
                                    Apellidos *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="apellidos" 
                                       name="apellidos" 
                                       value="<?= htmlspecialchars($datos['apellidos'] ?? '') ?>"
                                       placeholder="Tus apellidos"
                                       required
                                       autocomplete="family-name">
                                <div class="invalid-feedback">
                                    Los apellidos son obligatorios.
                                </div>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="fas fa-envelope me-2 text-success"></i>
                                Correo Electrónico *
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?= htmlspecialchars($datos['email'] ?? '') ?>"
                                   placeholder="tu@email.com"
                                   required
                                   autocomplete="email">
                            <div class="invalid-feedback">
                                Por favor, introduce un email válido.
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Utilizaremos tu email para enviarte información importante.
                            </div>
                        </div>
                        
                        <!-- Contraseña -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">
                                <i class="fas fa-lock me-2 text-success"></i>
                                Contraseña *
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Mínimo 8 caracteres"
                                       required
                                       minlength="8"
                                       autocomplete="new-password">
                                <button type="button" 
                                        class="btn btn-outline-secondary" 
                                        id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">
                                La contraseña debe tener al menos 8 caracteres.
                            </div>
                            
                            <!-- Indicador de fuerza de contraseña -->
                            <div class="mt-2">
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar" 
                                         id="passwordStrength" 
                                         role="progressbar" 
                                         style="width: 0%"></div>
                                </div>
                                <small class="text-muted" id="passwordHint">
                                    Usa mayúsculas, minúsculas, números y símbolos.
                                </small>
                            </div>
                        </div>
                        
                        <!-- Confirmar contraseña -->
                        <div class="mb-4">
                            <label for="password_confirmacion" class="form-label fw-semibold">
                                <i class="fas fa-lock me-2 text-success"></i>
                                Confirmar Contraseña *
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmacion" 
                                       name="password_confirmacion" 
                                       placeholder="Repite tu contraseña"
                                       required
                                       autocomplete="new-password">
                                <button type="button" 
                                        class="btn btn-outline-secondary" 
                                        id="togglePasswordConfirm">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">
                                Las contraseñas no coinciden.
                            </div>
                        </div>
                        
                        <!-- Teléfono (opcional) -->
                        <div class="mb-4">
                            <label for="telefono" class="form-label fw-semibold">
                                <i class="fas fa-phone me-2 text-success"></i>
                                Teléfono <span class="text-muted">(opcional)</span>
                            </label>
                            <input type="tel" 
                                   class="form-control" 
                                   id="telefono" 
                                   name="telefono" 
                                   value="<?= htmlspecialchars($datos['telefono'] ?? '') ?>"
                                   placeholder="+34 123 456 789"
                                   autocomplete="tel">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Para notificaciones importantes sobre eventos.
                            </div>
                        </div>
                        
                        <!-- Términos y condiciones -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="terminos" 
                                       name="terminos" 
                                       required>
                                <label class="form-check-label" for="terminos">
                                    Acepto los 
                                    <a href="/terminos" target="_blank" class="text-primary">
                                        términos y condiciones
                                    </a> 
                                    y la 
                                    <a href="/privacidad" target="_blank" class="text-primary">
                                        política de privacidad
                                    </a> *
                                </label>
                                <div class="invalid-feedback">
                                    Debes aceptar los términos y condiciones.
                                </div>
                            </div>
                        </div>
                        
                        <!-- Newsletter (opcional) -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="newsletter" 
                                       name="newsletter"
                                       <?= isset($datos['newsletter']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="newsletter">
                                    <i class="fas fa-envelope me-1"></i>
                                    Quiero recibir noticias sobre exposiciones y eventos
                                </label>
                            </div>
                        </div>
                        
                        <!-- Botón enviar -->
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-user-plus me-2"></i>
                                Crear Mi Cuenta
                            </button>
                        </div>
                        
                        <!-- Enlaces adicionales -->
                        <div class="text-center">
                            <hr class="my-4">
                            <p class="text-muted">
                                ¿Ya tienes una cuenta?
                                <a href="/login" class="text-success text-decoration-none fw-semibold">
                                    Inicia sesión aquí
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Beneficios de registrarse -->
            <div class="card mt-4 border-0 bg-light">
                <div class="card-body">
                    <h5 class="fw-bold text-center mb-3">
                        <i class="fas fa-gift text-success me-2"></i>
                        Beneficios de Registrarte
                    </h5>
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <i class="fas fa-heart text-danger fa-2x mb-2"></i>
                            <h6 class="fw-semibold">Favoritos</h6>
                            <p class="small text-muted mb-0">Guarda tus exposiciones preferidas</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <i class="fas fa-bell text-warning fa-2x mb-2"></i>
                            <h6 class="fw-semibold">Notificaciones</h6>
                            <p class="small text-muted mb-0">Entérate de nuevos eventos</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <i class="fas fa-comments text-info fa-2x mb-2"></i>
                            <h6 class="fw-semibold">Comentarios</h6>
                            <p class="small text-muted mb-0">Participa en la comunidad</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript personalizado -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle para mostrar/ocultar contraseñas
    function setupPasswordToggle(toggleId, passwordId) {
        const toggle = document.getElementById(toggleId);
        const password = document.getElementById(passwordId);
        
        if (toggle && password) {
            toggle.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                const icon = this.querySelector('i');
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        }
    }
    
    setupPasswordToggle('togglePassword', 'password');
    setupPasswordToggle('togglePasswordConfirm', 'password_confirmacion');
    
    // Validación de contraseña en tiempo real
    const passwordField = document.getElementById('password');
    const passwordStrength = document.getElementById('passwordStrength');
    const passwordHint = document.getElementById('passwordHint');
    
    if (passwordField && passwordStrength) {
        passwordField.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let hints = [];
            
            // Criterios de validación
            if (password.length >= 8) strength += 25;
            else hints.push('mínimo 8 caracteres');
            
            if (/[a-z]/.test(password)) strength += 25;
            else hints.push('minúsculas');
            
            if (/[A-Z]/.test(password)) strength += 25;
            else hints.push('mayúsculas');
            
            if (/[0-9]/.test(password)) strength += 25;
            else hints.push('números');
            
            if (/[^A-Za-z0-9]/.test(password)) strength += 10;
            else hints.push('símbolos');
            
            // Actualizar barra de progreso
            passwordStrength.style.width = Math.min(strength, 100) + '%';
            
            // Colores según la fuerza
            passwordStrength.className = 'progress-bar';
            if (strength < 50) {
                passwordStrength.classList.add('bg-danger');
                passwordHint.textContent = 'Contraseña débil. Añade: ' + hints.join(', ');
                passwordHint.className = 'text-danger small';
            } else if (strength < 75) {
                passwordStrength.classList.add('bg-warning');
                passwordHint.textContent = 'Contraseña moderada. Mejora: ' + hints.join(', ');
                passwordHint.className = 'text-warning small';
            } else {
                passwordStrength.classList.add('bg-success');
                passwordHint.textContent = 'Contraseña fuerte';
                passwordHint.className = 'text-success small';
            }
        });
    }
    
    // Validación de confirmación de contraseña
    const passwordConfirm = document.getElementById('password_confirmacion');
    if (passwordField && passwordConfirm) {
        function validatePasswordMatch() {
            if (passwordConfirm.value && passwordField.value !== passwordConfirm.value) {
                passwordConfirm.setCustomValidity('Las contraseñas no coinciden');
            } else {
                passwordConfirm.setCustomValidity('');
            }
        }
        
        passwordField.addEventListener('input', validatePasswordMatch);
        passwordConfirm.addEventListener('input', validatePasswordMatch);
    }
    
    // Validación del formulario
    const registroForm = document.getElementById('registroForm');
    if (registroForm) {
        registroForm.addEventListener('submit', function(event) {
            if (!registroForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            registroForm.classList.add('was-validated');
        });
    }
    
    // Auto-focus en el primer campo
    const nombreField = document.getElementById('nombre');
    if (nombreField && !nombreField.value) {
        nombreField.focus();
    }
});
</script>

<!-- CSS personalizado -->
<style>
.card {
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
}

.form-control:focus {
    border-color: var(--bs-success);
    box-shadow: 0 0 0 0.25rem rgba(var(--bs-success-rgb), 0.25);
}

.btn-success {
    background: linear-gradient(135deg, var(--bs-success) 0%, #059669 100%);
    border: none;
}

.btn-success:hover {
    background: linear-gradient(135deg, #059669 0%, var(--bs-success) 100%);
    transform: translateY(-1px);
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    transition: all 0.3s ease;
}

.input-group .btn-outline-secondary:hover {
    background-color: var(--bs-light);
}
</style>
