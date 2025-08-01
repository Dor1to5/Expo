<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <i class="fas fa-sign-in-alt fa-2x mb-3"></i>
                    <h3 class="fw-bold mb-0">Iniciar Sesión</h3>
                    <p class="mb-0 opacity-75">Accede a tu cuenta</p>
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
                    
                    <?php if (isset($mensaje_exito)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= htmlspecialchars($mensaje_exito) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Formulario de login -->
                    <form action="/login" method="POST" id="loginForm" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <!-- Campo Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold">
                                <i class="fas fa-envelope me-2 text-primary"></i>
                                Correo Electrónico
                            </label>
                            <input type="email" 
                                   class="form-control form-control-lg" 
                                   id="email" 
                                   name="email" 
                                   value="<?= htmlspecialchars($datos['email'] ?? '') ?>"
                                   placeholder="tu@email.com"
                                   required
                                   autocomplete="email">
                            <div class="invalid-feedback">
                                Por favor, introduce un email válido.
                            </div>
                        </div>
                        
                        <!-- Campo Contraseña -->
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">
                                <i class="fas fa-lock me-2 text-primary"></i>
                                Contraseña
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-lg" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Tu contraseña"
                                       required
                                       autocomplete="current-password">
                                <button type="button" 
                                        class="btn btn-outline-secondary" 
                                        id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">
                                La contraseña es obligatoria.
                            </div>
                        </div>
                        
                        <!-- Recordar sesión -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="recordar" 
                                       name="recordar"
                                       <?= isset($datos['recordar']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="recordar">
                                    Recordar mi sesión
                                </label>
                            </div>
                        </div>
                        
                        <!-- Botón enviar -->
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Iniciar Sesión
                            </button>
                        </div>
                        
                        <!-- Enlaces adicionales -->
                        <div class="text-center">
                            <p class="text-muted mb-3">
                                <a href="/recuperar-password" class="text-decoration-none">
                                    ¿Has olvidado tu contraseña?
                                </a>
                            </p>
                            
                            <hr class="my-4">
                            
                            <p class="text-muted">
                                ¿No tienes una cuenta?
                                <a href="/registro" class="text-primary text-decoration-none fw-semibold">
                                    Regístrate aquí
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Información adicional -->
            <div class="text-center mt-4">
                <div class="row">
                    <div class="col-4">
                        <div class="border-end">
                            <i class="fas fa-shield-alt text-success fa-2x mb-2"></i>
                            <p class="small text-muted mb-0">Seguro</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border-end">
                            <i class="fas fa-clock text-info fa-2x mb-2"></i>
                            <p class="small text-muted mb-0">24/7</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-users text-primary fa-2x mb-2"></i>
                        <p class="small text-muted mb-0">Comunidad</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript personalizado -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle para mostrar/ocultar contraseña
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    
    if (togglePassword && passwordField) {
        togglePassword.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
    
    // Validación del formulario
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            if (!loginForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            loginForm.classList.add('was-validated');
        });
    }
    
    // Auto-focus en el primer campo
    const emailField = document.getElementById('email');
    if (emailField && !emailField.value) {
        emailField.focus();
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
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, var(--bs-primary) 0%, #4f46e5 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #4f46e5 0%, var(--bs-primary) 100%);
    transform: translateY(-1px);
}

.input-group .btn-outline-secondary:hover {
    background-color: var(--bs-light);
}

.border-end:last-child {
    border-right: none !important;
}
</style>
