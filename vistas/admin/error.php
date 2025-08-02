<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5 text-center">
                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                    <h1 class="display-4 fw-bold text-danger mt-3">Error del Sistema</h1>
                    <h2 class="h4 text-dark mb-3">Ha ocurrido un error</h2>
                    
                    <?php if (isset($mensaje_error)): ?>
                        <div class="alert alert-danger text-start mb-4">
                            <h5 class="alert-heading">Detalles del Error:</h5>
                            <p class="mb-0"><?= htmlspecialchars($mensaje_error) ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (MODO_DEBUG && isset($error_trace)): ?>
                        <div class="alert alert-warning text-start mb-4">
                            <h6 class="alert-heading">Información de Debug:</h6>
                            <pre class="mb-0" style="font-size: 0.8rem;"><?= htmlspecialchars($error_trace) ?></pre>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-flex flex-column flex-md-row gap-2 justify-content-center">
                        <a href="index.php?ruta=admin/dashboard" class="btn btn-primary">
                            <i class="fas fa-tachometer-alt me-2"></i>Ir al Dashboard
                        </a>
                        <button onclick="history.back()" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver Atrás
                        </button>
                        <button onclick="location.reload()" class="btn btn-outline-info">
                            <i class="fas fa-redo me-2"></i>Reintentar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

