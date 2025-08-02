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
    <link href="<?= URL_PUBLICA ?>css/publico.css" rel="stylesheet">
    
    <!-- Open Graph meta tags para redes sociales -->
    <meta property="og:title" content="<?= htmlspecialchars($titulo_pagina ?: NOMBRE_APLICACION) ?>">
    <meta property="og:description" content="<?= htmlspecialchars(DESCRIPCION_APLICACION) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= URL_BASE . ltrim($url_actual ?? '', '/') ?>">
</head>
<body class="bg-light">
    <!-- Barra de navegación principal -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="fas fa-palette me-2"></i>
                <?= NOMBRE_APLICACION ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPublico">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarPublico">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with($url_actual, '/') && $url_actual === '/' ? 'active' : '' ?>" href="/">
                            <i class="fas fa-home me-1"></i>Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with($url_actual, '/exposiciones') ? 'active' : '' ?>" href="/exposiciones">
                            <i class="fas fa-images me-1"></i>Exposiciones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with($url_actual, '/blog') ? 'active' : '' ?>" href="/blog">
                            <i class="fas fa-blog me-1"></i>Blog
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with($url_actual, '/acerca-de') ? 'active' : '' ?>" href="/acerca-de">
                            <i class="fas fa-info-circle me-1"></i>Acerca de
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with($url_actual, '/contacto') ? 'active' : '' ?>" href="/contacto">
                            <i class="fas fa-envelope me-1"></i>Contacto
                        </a>
                    </li>
                </ul>
                
                <!-- Buscador en el navbar -->
                <form class="d-flex me-3" action="/busqueda" method="GET" role="search">
                    <div class="input-group">
                        <input class="form-control" type="search" name="q" placeholder="Buscar..." aria-label="Buscar" 
                               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        <button class="btn btn-outline-light" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <!-- Enlaces de usuario -->
                <ul class="navbar-nav">
                    <?php if ($esta_autenticado): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i>
                                <?= htmlspecialchars($usuario_actual['nombre_completo'] ?? 'Usuario') ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header">Mi Cuenta</h6></li>
                                <li><a class="dropdown-item" href="/perfil">
                                    <i class="fas fa-user-edit me-2"></i>Mi Perfil
                                </a></li>
                                <li><a class="dropdown-item" href="/cambiar-contrasena">
                                    <i class="fas fa-key me-2"></i>Cambiar Contraseña
                                </a></li>
                                <?php if ($es_admin): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="index.php?ruta=admin">
                                        <i class="fas fa-cogs me-2"></i>Panel de Administración
                                    </a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="/logout">
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login">
                                <i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-light btn-sm ms-2" href="/registro">
                                <i class="fas fa-user-plus me-1"></i>Registrarse
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb (si es necesario) -->
    <?php if (!empty($breadcrumbs)): ?>
        <nav aria-label="breadcrumb" class="bg-white border-bottom">
            <div class="container">
                <ol class="breadcrumb py-2 mb-0">
                    <?php foreach ($breadcrumbs as $breadcrumb): ?>
                        <?php if (isset($breadcrumb['url'])): ?>
                            <li class="breadcrumb-item">
                                <a href="<?= htmlspecialchars($breadcrumb['url']) ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($breadcrumb['titulo']) ?>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?= htmlspecialchars($breadcrumb['titulo']) ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </div>
        </nav>
    <?php endif; ?>

    <!-- Mensajes flash -->
    <?php if (!empty($mensajes_flash)): ?>
        <div class="container mt-3">
            <?php foreach ($mensajes_flash as $mensaje): ?>
                <div class="alert alert-<?= $mensaje['tipo'] === 'error' ? 'danger' : $mensaje['tipo'] ?> alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <?php
                        $icono = match($mensaje['tipo']) {
                            'success' => 'fas fa-check-circle',
                            'error', 'danger' => 'fas fa-exclamation-triangle',
                            'warning' => 'fas fa-exclamation-circle',
                            'info' => 'fas fa-info-circle',
                            default => 'fas fa-bell'
                        };
                        ?>
                        <i class="<?= $icono ?> me-2"></i>
                        <div><?= htmlspecialchars($mensaje['mensaje']) ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Contenido principal -->
    <main>
        <?= $contenido ?>
    </main>

    <!-- Pie de página -->
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-palette me-2"></i>
                        <?= NOMBRE_APLICACION ?>
                    </h5>
                    <p class="text-muted mb-3"><?= DESCRIPCION_APLICACION ?></p>
                    <div class="d-flex">
                        <a href="#" class="text-muted me-3 fs-5" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-muted me-3 fs-5" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-muted me-3 fs-5" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-muted fs-5" title="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Navegación</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/" class="text-muted text-decoration-none">Inicio</a></li>
                        <li class="mb-2"><a href="/exposiciones" class="text-muted text-decoration-none">Exposiciones</a></li>
                        <li class="mb-2"><a href="/blog" class="text-muted text-decoration-none">Blog</a></li>
                        <li class="mb-2"><a href="/acerca-de" class="text-muted text-decoration-none">Acerca de</a></li>
                        <li class="mb-2"><a href="/contacto" class="text-muted text-decoration-none">Contacto</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Información</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/archivo" class="text-muted text-decoration-none">Archivo</a></li>
                        <li class="mb-2"><a href="/privacidad" class="text-muted text-decoration-none">Política de Privacidad</a></li>
                        <li class="mb-2"><a href="/terminos" class="text-muted text-decoration-none">Términos y Condiciones</a></li>
                        <?php if (!$esta_autenticado): ?>
                            <li class="mb-2"><a href="/login" class="text-muted text-decoration-none">Iniciar Sesión</a></li>
                            <li class="mb-2"><a href="/registro" class="text-muted text-decoration-none">Registrarse</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Contacto</h6>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Calle Ejemplo 123, Madrid, España
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-phone me-2"></i>
                            +34 91 123 45 67
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            info@sistemagestion.com
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock me-2"></i>
                            Lun - Vie: 9:00 - 18:00
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4 border-secondary">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">
                        &copy; <?= date('Y') ?> <?= NOMBRE_APLICACION ?>. Todos los derechos reservados.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-muted">
                        Versión <?= VERSION_APLICACION ?> | 
                        Desarrollado con <i class="fas fa-heart text-danger"></i> en España
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Botón volver arriba -->
    <button type="button" class="btn btn-primary position-fixed bottom-0 end-0 m-3 rounded-circle" 
            id="botonVolverArriba" style="display: none; z-index: 1000;" title="Volver arriba">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Scripts -->
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts personalizados -->
    <script src="<?= URL_PUBLICA ?>js/publico.js"></script>

    <!-- Configuración global de JavaScript -->
    <script>
        // Configurar variables globales
        window.appConfig = {
            baseUrl: '<?= URL_BASE ?>',
            csrfToken: '<?= $csrf_token ?>',
            isAuthenticated: <?= $esta_autenticado ? 'true' : 'false' ?>,
            isAdmin: <?= $es_admin ? 'true' : 'false' ?>
        };

        // Función helper para peticiones AJAX con CSRF
        window.ajaxConCSRF = function(url, options = {}) {
            const defaultOptions = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            };
            
            // Combinar opciones
            const finalOptions = { ...defaultOptions, ...options };
            
            // Agregar token CSRF si es POST
            if (finalOptions.method === 'POST') {
                if (finalOptions.body instanceof FormData) {
                    finalOptions.body.append('csrf_token', window.appConfig.csrfToken);
                } else if (typeof finalOptions.body === 'string') {
                    finalOptions.body += '&csrf_token=' + encodeURIComponent(window.appConfig.csrfToken);
                } else {
                    finalOptions.body = 'csrf_token=' + encodeURIComponent(window.appConfig.csrfToken);
                }
            }
            
            return fetch(url, finalOptions);
        };

        // Inicializar funcionalidades cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            // Botón volver arriba
            const botonVolverArriba = document.getElementById('botonVolverArriba');
            
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    botonVolverArriba.style.display = 'block';
                } else {
                    botonVolverArriba.style.display = 'none';
                }
            });
            
            botonVolverArriba.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            
            // Auto-ocultar alertas después de 5 segundos
            const alertas = document.querySelectorAll('.alert:not(.alert-permanent)');
            alertas.forEach(function(alerta) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alerta);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
</body>
</html>

