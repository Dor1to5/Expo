<!-- Sección Hero/Banner principal -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    Bienvenido a <?= NOMBRE_APLICACION ?>
                </h1>
                <p class="lead mb-4">
                    Descubre las mejores exposiciones de arte y cultura. 
                    Sumérgete en un mundo de creatividad y conocimiento.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/exposiciones" class="btn btn-light btn-lg">
                        <i class="fas fa-images me-2"></i>Ver Exposiciones
                    </a>
                    <a href="/blog" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-blog me-2"></i>Leer Blog
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="<?= URL_PUBLICA ?>imagenes/hero-art.jpg" 
                     alt="Arte y Cultura" 
                     class="img-fluid rounded shadow-lg"
                     style="max-height: 400px; object-fit: cover;">
            </div>
        </div>
    </div>
</section>

<!-- Estadísticas rápidas -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 col-6 mb-3">
                <div class="d-flex flex-column align-items-center">
                    <i class="fas fa-images text-primary fs-1 mb-2"></i>
                    <h3 class="fw-bold text-primary"><?= number_format($estadisticas['total_exposiciones']) ?></h3>
                    <p class="text-muted mb-0">Exposiciones</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="d-flex flex-column align-items-center">
                    <i class="fas fa-calendar-check text-success fs-1 mb-2"></i>
                    <h3 class="fw-bold text-success"><?= number_format($estadisticas['exposiciones_actuales']) ?></h3>
                    <p class="text-muted mb-0">En Curso</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="d-flex flex-column align-items-center">
                    <i class="fas fa-blog text-info fs-1 mb-2"></i>
                    <h3 class="fw-bold text-info"><?= number_format($estadisticas['total_articulos']) ?></h3>
                    <p class="text-muted mb-0">Artículos</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="d-flex flex-column align-items-center">
                    <i class="fas fa-users text-warning fs-1 mb-2"></i>
                    <h3 class="fw-bold text-warning">1,250+</h3>
                    <p class="text-muted mb-0">Visitantes</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Exposiciones destacadas -->
<?php if (!empty($exposiciones_destacadas)): ?>
<section class="py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark">
                            <i class="fas fa-star text-warning me-2"></i>
                            Exposiciones Destacadas
                        </h2>
                        <p class="text-muted">Las mejores exposiciones seleccionadas para ti</p>
                    </div>
                    <a href="/exposiciones" class="btn btn-outline-primary">
                        Ver Todas <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="row">
            <?php foreach ($exposiciones_destacadas as $exposicion): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 hover-shadow">
                        <?php if (!empty($exposicion['imagen_principal'])): ?>
                            <img src="<?= htmlspecialchars($exposicion['imagen_principal']) ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($exposicion['titulo']) ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                <i class="fas fa-image text-muted fa-3x"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold">
                                <?= htmlspecialchars($exposicion['titulo']) ?>
                            </h5>
                            
                            <p class="card-text text-muted flex-grow-1">
                                <?= htmlspecialchars(substr($exposicion['descripcion_corta'] ?? $exposicion['descripcion'], 0, 120)) ?>
                                <?= strlen($exposicion['descripcion_corta'] ?? $exposicion['descripcion']) > 120 ? '...' : '' ?>
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center text-small text-muted mb-3">
                                <span>
                                    <i class="fas fa-calendar me-1"></i>
                                    <?= date('d/m/Y', strtotime($exposicion['fecha_inicio'])) ?>
                                </span>
                                <span>
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <?= htmlspecialchars(substr($exposicion['ubicacion'], 0, 20)) ?>
                                </span>
                            </div>
                            
                            <a href="/exposicion/<?= $exposicion['id'] ?>" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>Ver Detalles
                            </a>
                        </div>
                        
                        <div class="card-footer bg-transparent border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary">
                                    <?= ucfirst(str_replace('_', ' ', $exposicion['categoria'])) ?>
                                </span>
                                <?php if ($exposicion['precio_entrada'] > 0): ?>
                                    <span class="text-success fw-bold">
                                        <?= number_format($exposicion['precio_entrada'], 2) ?>€
                                    </span>
                                <?php else: ?>
                                    <span class="text-success fw-bold">Gratis</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Exposiciones actuales -->
<?php if (!empty($exposiciones_actuales)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark">
                            <i class="fas fa-calendar-check text-success me-2"></i>
                            Exposiciones Actuales
                        </h2>
                        <p class="text-muted">Visita estas exposiciones que están abiertas ahora</p>
                    </div>
                    <a href="/exposiciones?actuales=1" class="btn btn-outline-success">
                        Ver Todas <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="row">
            <?php foreach (array_slice($exposiciones_actuales, 0, 4) as $exposicion): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <?php if (!empty($exposicion['imagen_principal'])): ?>
                            <img src="<?= htmlspecialchars($exposicion['imagen_principal']) ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($exposicion['titulo']) ?>"
                                 style="height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 150px;">
                                <i class="fas fa-image text-muted fa-2x"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h6 class="card-title fw-bold">
                                <?= htmlspecialchars(substr($exposicion['titulo'], 0, 50)) ?>
                                <?= strlen($exposicion['titulo']) > 50 ? '...' : '' ?>
                            </h6>
                            
                            <p class="card-text text-muted small">
                                <i class="fas fa-calendar me-1"></i>
                                Hasta el <?= date('d/m/Y', strtotime($exposicion['fecha_fin'])) ?>
                            </p>
                            
                            <a href="/exposicion/<?= $exposicion['id'] ?>" 
                               class="btn btn-success btn-sm w-100">
                                <i class="fas fa-eye me-1"></i>Visitar
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Artículos del blog -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Artículos recientes -->
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold text-dark">
                            <i class="fas fa-blog text-info me-2"></i>
                            Últimas Noticias
                        </h2>
                        <p class="text-muted">Mantente al día con nuestras últimas publicaciones</p>
                    </div>
                    <a href="/blog" class="btn btn-outline-info">
                        Ver Blog <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                
                <?php if (!empty($articulos_recientes)): ?>
                    <div class="row">
                        <?php foreach ($articulos_recientes as $articulo): ?>
                            <div class="col-md-6 mb-4">
                                <article class="card border-0 shadow-sm h-100">
                                    <?php if (!empty($articulo['imagen_destacada'])): ?>
                                        <img src="<?= htmlspecialchars($articulo['imagen_destacada']) ?>" 
                                             class="card-img-top" 
                                             alt="<?= htmlspecialchars($articulo['titulo']) ?>"
                                             style="height: 150px; object-fit: cover;">
                                    <?php endif; ?>
                                    
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold">
                                            <a href="/blog/<?= htmlspecialchars($articulo['slug']) ?>" 
                                               class="text-decoration-none text-dark">
                                                <?= htmlspecialchars($articulo['titulo']) ?>
                                            </a>
                                        </h5>
                                        
                                        <p class="card-text text-muted">
                                            <?= htmlspecialchars(substr($articulo['resumen'] ?? $articulo['contenido'], 0, 100)) ?>...
                                        </p>
                                        
                                        <div class="d-flex justify-content-between align-items-center text-small text-muted">
                                            <span>
                                                <i class="fas fa-user me-1"></i>
                                                <?= htmlspecialchars($articulo['nombre_autor']) ?>
                                            </span>
                                            <span>
                                                <i class="fas fa-calendar me-1"></i>
                                                <?= date('d/m/Y', strtotime($articulo['fecha_publicacion'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-blog text-muted fa-3x mb-3"></i>
                        <p class="text-muted">No hay artículos disponibles en este momento.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar con artículos destacados -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-star me-2"></i>
                            Artículos Destacados
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($articulos_destacados)): ?>
                            <?php foreach ($articulos_destacados as $articulo): ?>
                                <div class="d-flex mb-3 pb-3 border-bottom">
                                    <?php if (!empty($articulo['imagen_destacada'])): ?>
                                        <img src="<?= htmlspecialchars($articulo['imagen_destacada']) ?>" 
                                             alt="<?= htmlspecialchars($articulo['titulo']) ?>"
                                             class="rounded me-3"
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">
                                            <a href="/blog/<?= htmlspecialchars($articulo['slug']) ?>" 
                                               class="text-decoration-none text-dark">
                                                <?= htmlspecialchars(substr($articulo['titulo'], 0, 60)) ?>
                                                <?= strlen($articulo['titulo']) > 60 ? '...' : '' ?>
                                            </a>
                                        </h6>
                                        <p class="text-muted small mb-0">
                                            <?= date('d/m/Y', strtotime($articulo['fecha_publicacion'])) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted text-center">No hay artículos destacados.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Newsletter suscripción -->
                <div class="card bg-light border-0 mt-4">
                    <div class="card-body text-center">
                        <i class="fas fa-envelope-open-text text-primary fa-2x mb-3"></i>
                        <h5 class="fw-bold">Mantente Informado</h5>
                        <p class="text-muted small">Recibe noticias sobre nuevas exposiciones y eventos.</p>
                        <form action="/newsletter" method="POST" class="mt-3">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="Tu email..." required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="fw-bold mb-3">¿Listo para Explorar?</h2>
                <p class="lead mb-4">
                    Únete a nuestra comunidad y descubre un mundo lleno de arte, cultura y conocimiento. 
                    ¡Tu próxima experiencia inolvidable te está esperando!
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <?php if (!$esta_autenticado): ?>
                        <a href="/registro" class="btn btn-light btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Únete Ahora
                        </a>
                    <?php endif; ?>
                    <a href="/exposiciones" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-compass me-2"></i>Explorar Exposiciones
                    </a>
                    <a href="/contacto" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-envelope me-2"></i>Contactar
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CSS personalizado para esta página -->
<style>
.hero-section {
    background: linear-gradient(135deg, var(--bs-primary) 0%, #4f46e5 100%);
}

.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.min-vh-50 {
    min-height: 50vh;
}

.card-img-top {
    transition: transform 0.3s ease;
}

.card:hover .card-img-top {
    transform: scale(1.05);
}

.text-small {
    font-size: 0.875rem;
}
</style>
