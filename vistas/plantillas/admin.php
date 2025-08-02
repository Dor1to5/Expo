<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo_pagina ?? 'Panel de Administración' ?> - <?= NOMBRE_APLICACION ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Admin CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 2px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .navbar-admin {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .border-left-primary { border-left: 4px solid #4e73df !important; }
        .border-left-success { border-left: 4px solid #1cc88a !important; }
        .border-left-info { border-left: 4px solid #36b9cc !important; }
        .border-left-warning { border-left: 4px solid #f6c23e !important; }
        .text-primary { color: #4e73df !important; }
        .text-success { color: #1cc88a !important; }
        .text-info { color: #36b9cc !important; }
        .text-warning { color: #f6c23e !important; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white fw-bold"><?= NOMBRE_APLICACION ?></h4>
                        <small class="text-white-50">Panel de Administración</small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?ruta=admin">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?ruta=admin/usuarios">
                                <i class="fas fa-users me-2"></i>
                                Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?ruta=admin/exposiciones">
                                <i class="fas fa-images me-2"></i>
                                Exposiciones
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?ruta=admin/articulos">
                                <i class="fas fa-newspaper me-2"></i>
                                Artículos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?ruta=admin/roles">
                                <i class="fas fa-user-shield me-2"></i>
                                Roles
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?ruta=admin/configuracion">
                                <i class="fas fa-cogs me-2"></i>
                                Configuración
                            </a>
                        </li>
                        
                        <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
                        
                        <li class="nav-item">
                            <a class="nav-link" href="index.php" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i>
                                Ver Sitio Público
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?ruta=logout">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Top navbar -->
                <nav class="navbar navbar-expand-lg navbar-admin mb-4">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        
                        <div class="navbar-nav ms-auto">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle me-2"></i>
                                    <?= $_SESSION['usuario_nombre'] ?? 'Administrador' ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="index.php?ruta=admin/perfil"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                                    <li><a class="dropdown-item" href="index.php?ruta=admin/configuracion"><i class="fas fa-cogs me-2"></i>Configuración</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page content -->
                <div class="content">
                    <?php if (isset($mensaje_exito)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= htmlspecialchars($mensaje_exito) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($mensaje_error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= htmlspecialchars($mensaje_error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php echo $contenido; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Admin JS -->
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Manejar estado activo del menú
        document.addEventListener('DOMContentLoaded', function() {
            const currentUrl = window.location.search;
            const menuLinks = document.querySelectorAll('.sidebar .nav-link');
            
            menuLinks.forEach(function(link) {
                // Remover clase active de todos los enlaces
                link.classList.remove('active');
                
                // Obtener la ruta del enlace
                const linkUrl = new URL(link.href);
                const linkRoute = linkUrl.searchParams.get('ruta');
                const currentRoute = new URLSearchParams(currentUrl).get('ruta');
                
                // Marcar como activo si coincide la ruta
                if (linkRoute === currentRoute || (currentRoute === null && linkRoute === null)) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>

