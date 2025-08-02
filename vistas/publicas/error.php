<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error del Sistema - <?= NOMBRE_APLICACION ?? 'Sistema de Gestión' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 text-center">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <i class="fas fa-exclamation-circle text-danger" style="font-size: 4rem;"></i>
                        <h1 class="display-4 fw-bold text-danger mt-3">Error</h1>
                        <h2 class="h4 text-dark mb-3">Error del Sistema</h2>
                        <p class="text-muted mb-4">
                            Ha ocurrido un error inesperado. Por favor, inténtalo de nuevo más tarde.
                        </p>
                        <div class="d-flex flex-column flex-md-row gap-2 justify-content-center">
                            <a href="/" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i>Ir al Inicio
                            </a>
                            <button onclick="location.reload()" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-2"></i>Reintentar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
