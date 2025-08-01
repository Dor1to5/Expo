<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo_pagina ?: NOMBRE_APLICACION) ?></title>
    <meta name="description" content="<?= htmlspecialchars(DESCRIPCION_APLICACION) ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS personalizado -->
    <link href="<?= URL_PUBLICA ?>css/estilos.css" rel="stylesheet">
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-palette me-2"></i>
                <?= NOMBRE_APLICACION ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/"><i class="fas fa-home me-1"></i>Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/exposiciones"><i class="fas fa-images me-1"></i>Exposiciones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/blog"><i class="fas fa-blog me-1"></i>Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/acerca-de"><i class="fas fa-info-circle me-1"></i>Acerca de</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contacto"><i class="fas fa-envelope me-1"></i>Contacto</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if ($esta_autenticado): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>
                                <?= htmlspecialchars($usuario_actual['nombre_completo'] ?? 'Usuario') ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/perfil"><i class="fas fa-user-edit me-2"></i>Mi Perfil</a></li>
                                <?php if ($es_admin): ?>
                                    <li><a class="dropdown-item" href="/admin"><i class="fas fa-cogs me-2"></i>Administración</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login"><i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/registro"><i class="fas fa-user-plus me-1"></i>Registrarse</a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Buscador -->
                    <li class="nav-item">
                        <form class="d-flex ms-2" action="/busqueda" method="GET">
                            <input class="form-control form-control-sm me-1" type="search" name="q" placeholder="Buscar..." aria-label="Buscar">
                            <button class="btn btn-outline-light btn-sm" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Mensajes flash -->
    <?php if (!empty($mensajes_flash)): ?>
        <div class="container mt-3">
            <?php foreach ($mensajes_flash as $mensaje): ?>
                <div class="alert alert-<?= $mensaje['tipo'] === 'error' ? 'danger' : $mensaje['tipo'] ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($mensaje['mensaje']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Contenido principal -->
    <main class="container-fluid py-4">
        <?= $contenido ?>
    </main>

    <!-- Pie de página -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-palette me-2"></i><?= NOMBRE_APLICACION ?></h5>
                    <p class="text-muted"><?= DESCRIPCION_APLICACION ?></p>
                </div>
                <div class="col-md-4">
                    <h6>Enlaces Rápidos</h6>
                    <ul class="list-unstyled">
                        <li><a href="/exposiciones" class="text-muted text-decoration-none">Exposiciones</a></li>
                        <li><a href="/blog" class="text-muted text-decoration-none">Blog</a></li>
                        <li><a href="/acerca-de" class="text-muted text-decoration-none">Acerca de</a></li>
                        <li><a href="/contacto" class="text-muted text-decoration-none">Contacto</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6>Información Legal</h6>
                    <ul class="list-unstyled">
                        <li><a href="/privacidad" class="text-muted text-decoration-none">Política de Privacidad</a></li>
                        <li><a href="/terminos" class="text-muted text-decoration-none">Términos y Condiciones</a></li>
                    </ul>
                    <div class="mt-3">
                        <a href="#" class="text-muted me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-muted me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-muted me-3"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">&copy; <?= date('Y') ?> <?= NOMBRE_APLICACION ?>. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-muted">Versión <?= VERSION_APLICACION ?></p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts personalizados -->
    <script src="<?= URL_PUBLICA ?>js/app.js"></script>

    <!-- Script para manejar tokens CSRF -->
    <script>
        // Configurar token CSRF para todas las peticiones AJAX
        window.csrfToken = '<?= $csrf_token ?>';
        
        // Función helper para peticiones AJAX con CSRF
        window.ajaxConCSRF = function(options) {
            if (options.type === 'POST' && options.data) {
                if (typeof options.data === 'string') {
                    options.data += '&csrf_token=' + encodeURIComponent(window.csrfToken);
                } else if (typeof options.data === 'object') {
                    options.data.csrf_token = window.csrfToken;
                }
            }
            return fetch(options.url, {
                method: options.type || 'GET',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: options.data
            });
        };
    </script>
</body>
</html>
