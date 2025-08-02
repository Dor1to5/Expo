<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Crear Nuevo Usuario</h1>
    <a href="/admin/usuarios" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver a Lista
    </a>
</div>

<?php if (isset($_SESSION['mensajes_flash'])): ?>
    <?php foreach ($_SESSION['mensajes_flash'] as $mensaje): ?>
        <div class="alert alert-<?= $mensaje['tipo'] === 'exito' ? 'success' : ($mensaje['tipo'] === 'error' ? 'danger' : ($mensaje['tipo'] === 'info' ? 'info' : $mensaje['tipo'])) ?> alert-dismissible fade show" role="alert">
            <i class="fas fa-<?= $mensaje['tipo'] === 'exito' ? 'check-circle' : ($mensaje['tipo'] === 'error' ? 'exclamation-triangle' : ($mensaje['tipo'] === 'info' ? 'info-circle' : 'info-circle')) ?> me-2"></i>
            <?= $mensaje['mensaje'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endforeach; ?>
    <?php unset($_SESSION['mensajes_flash']); ?>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Información del Usuario</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/usuarios/crear/procesar" id="formCrearUsuario">
                    <!-- Token CSRF removido para desarrollo -->
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                                <div class="invalid-feedback">
                                    Por favor ingrese el nombre.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="apellidos" class="form-label">Apellidos <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                                <div class="invalid-feedback">
                                    Por favor ingrese los apellidos.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">
                            Por favor ingrese un email válido.
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                <div class="form-text">Mínimo 6 caracteres</div>
                                <div class="invalid-feedback">
                                    La contraseña debe tener al menos 6 caracteres.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                                <div class="invalid-feedback">
                                    Las contraseñas no coinciden.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rol_id" class="form-label">Rol <span class="text-danger">*</span></label>
                                <select class="form-select" id="rol_id" name="rol_id" required>
                                    <option value="">Seleccionar rol...</option>
                                    <?php foreach ($roles as $rol): ?>
                                        <option value="<?= $rol['id'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor seleccione un rol.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono">
                                <div class="form-text">Opcional</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" checked>
                            <label class="form-check-label" for="activo">
                                Usuario activo
                            </label>
                            <div class="form-text">Los usuarios inactivos no pueden iniciar sesión</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="email_verificado" name="email_verificado" value="1">
                            <label class="form-check-label" for="email_verificado">
                                Email verificado
                            </label>
                            <div class="form-text">Marcar si el email del usuario ya está verificado</div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/admin/usuarios" class="btn btn-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Crear Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Información de Roles</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-primary">Usuario</h6>
                    <small class="text-muted">Permisos básicos de lectura y comentarios.</small>
                </div>
                <div class="mb-3">
                    <h6 class="text-success">Editor</h6>
                    <small class="text-muted">Puede crear y editar contenido, moderar comentarios.</small>
                </div>
                <div class="mb-3">
                    <h6 class="text-danger">Administrador</h6>
                    <small class="text-muted">Acceso completo al sistema, gestión de usuarios y configuración.</small>
                </div>
            </div>
        </div>
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Consejos</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        <small>Use contraseñas seguras con al menos 8 caracteres.</small>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-shield-alt text-success me-2"></i>
                        <small>Los usuarios inactivos no pueden acceder al sistema.</small>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope text-warning me-2"></i>
                        <small>Se enviará un email de bienvenida al usuario.</small>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCrearUsuario');
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirm');
    
    // Validar que las contraseñas coincidan
    function validatePasswords() {
        if (password.value !== passwordConfirm.value) {
            passwordConfirm.setCustomValidity('Las contraseñas no coinciden');
            passwordConfirm.classList.add('is-invalid');
        } else {
            passwordConfirm.setCustomValidity('');
            passwordConfirm.classList.remove('is-invalid');
        }
    }
    
    password.addEventListener('input', validatePasswords);
    passwordConfirm.addEventListener('input', validatePasswords);
    
    // Validación del formulario
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        form.classList.add('was-validated');
    });
});
</script>
