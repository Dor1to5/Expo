<div class="container-fluid">
    <div class="row">
        <!-- Contenido Principal -->
        <div class="col-lg-8">
            <!-- Header del Blog -->
            <div class="blog-header mb-5">
                <h1 class="display-4 font-weight-bold text-primary">
                    <i class="fas fa-newspaper me-3"></i>Blog
                </h1>
                <p class="lead text-muted">
                    Descubre las últimas noticias, artículos y novedades sobre arte, cultura y exposiciones
                </p>
            </div>
            
            <!-- Búsqueda -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="/blog/buscar" class="row g-3">
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="q" 
                                   placeholder="Buscar artículos..." 
                                   value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Filtros por Categoría -->
            <div class="mb-4">
                <div class="d-flex flex-wrap gap-2">
                    <a href="/blog" class="btn btn-outline-primary btn-sm <?= !isset($_GET['categoria']) ? 'active' : '' ?>">
                        <i class="fas fa-th-large me-1"></i>Todas
                    </a>
                    <a href="/blog/categoria/noticias" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-newspaper me-1"></i>Noticias
                    </a>
                    <a href="/blog/categoria/exposiciones" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-building me-1"></i>Exposiciones
                    </a>
                    <a href="/blog/categoria/arte" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-palette me-1"></i>Arte
                    </a>
                    <a href="/blog/categoria/cultura" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-theater-masks me-1"></i>Cultura
                    </a>
                    <a href="/blog/categoria/historia" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-landmark me-1"></i>Historia
                    </a>
                    <a href="/blog/categoria/eventos" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-calendar-alt me-1"></i>Eventos
                    </a>
                </div>
            </div>
            
            <!-- Lista de Artículos -->
            <?php if (empty($articulos)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-newspaper fa-4x text-muted mb-4"></i>
                    <h3 class="text-muted">No hay artículos disponibles</h3>
                    <p class="text-muted">Vuelve pronto para ver nuevos contenidos</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($articulos as $index => $articulo): ?>
                        <div class="col-md-6 mb-4">
                            <article class="card h-100 shadow-sm hover-shadow">
                                <?php if (!empty($articulo['imagen_destacada'])): ?>
                                    <img src="<?= htmlspecialchars($articulo['imagen_destacada']) ?>" 
                                         class="card-img-top" 
                                         alt="<?= htmlspecialchars($articulo['titulo']) ?>"
                                         style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-body d-flex flex-column">
                                    <!-- Categoría -->
                                    <div class="mb-2">
                                        <span class="badge bg-primary">
                                            <?= ucfirst(str_replace('_', ' ', $articulo['categoria'])) ?>
                                        </span>
                                        <?php if ($articulo['destacado']): ?>
                                            <span class="badge bg-warning">
                                                <i class="fas fa-star"></i> Destacado
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Título -->
                                    <h5 class="card-title">
                                        <a href="/blog/<?= htmlspecialchars($articulo['slug']) ?>" 
                                           class="text-decoration-none text-dark hover-primary">
                                            <?= htmlspecialchars($articulo['titulo']) ?>
                                        </a>
                                    </h5>
                                    
                                    <!-- Resumen -->
                                    <?php if (!empty($articulo['resumen'])): ?>
                                        <p class="card-text text-muted">
                                            <?= htmlspecialchars($articulo['resumen']) ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <!-- Meta información -->
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center text-muted small">
                                            <div>
                                                <i class="fas fa-user me-1"></i>
                                                <?php if ($articulo['autor_invitado']): ?>
                                                    <?= htmlspecialchars($articulo['autor_invitado']) ?>
                                                <?php else: ?>
                                                    <?= htmlspecialchars($articulo['nombre'] ?? 'Usuario') ?> 
                                                    <?= htmlspecialchars($articulo['apellidos'] ?? '') ?>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <i class="fas fa-clock me-1"></i>
                                                <?= $articulo['tiempo_lectura'] ?? 1 ?> min
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center text-muted small mt-1">
                                            <div>
                                                <i class="fas fa-calendar me-1"></i>
                                                <?= date('d/m/Y', strtotime($articulo['fecha_publicacion'] ?? $articulo['fecha_creacion'])) ?>
                                            </div>
                                            <div>
                                                <i class="fas fa-eye me-1"></i>
                                                <?= number_format($articulo['contador_visitas'] ?? 0) ?> visitas
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                        
                        <!-- Artículo destacado cada 4 artículos -->
                        <?php if (($index + 1) % 4 === 0 && $index < count($articulos) - 1): ?>
                            <div class="col-12 mb-4">
                                <div class="alert alert-info d-flex align-items-center">
                                    <i class="fas fa-info-circle fa-2x me-3"></i>
                                    <div>
                                        <h5 class="alert-heading">¿Te gustan nuestros artículos?</h5>
                                        <p class="mb-1">Suscríbete a nuestro boletín para recibir las últimas noticias sobre arte y cultura.</p>
                                        <small class="text-muted">Enviamos máximo un email por semana, sin spam.</small>
                                    </div>
                                    <button class="btn btn-info ms-auto">Suscribirse</button>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <!-- Paginación -->
                <?php if ($totalPaginas > 1): ?>
                    <nav aria-label="Paginación de artículos" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($paginaActual > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="/blog?pagina=<?= $paginaActual - 1 ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php
                            $inicio = max(1, $paginaActual - 2);
                            $fin = min($totalPaginas, $paginaActual + 2);
                            ?>
                            
                            <?php if ($inicio > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="/blog?pagina=1">1</a>
                                </li>
                                <?php if ($inicio > 2): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $inicio; $i <= $fin; $i++): ?>
                                <li class="page-item <?= $i === $paginaActual ? 'active' : '' ?>">
                                    <a class="page-link" href="/blog?pagina=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($fin < $totalPaginas): ?>
                                <?php if ($fin < $totalPaginas - 1): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="/blog?pagina=<?= $totalPaginas ?>"><?= $totalPaginas ?></a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if ($paginaActual < $totalPaginas): ?>
                                <li class="page-item">
                                    <a class="page-link" href="/blog?pagina=<?= $paginaActual + 1 ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Artículos Destacados -->
            <?php if (!empty($articulosDestacados)): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-star me-2"></i>Artículos Destacados
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php foreach ($articulosDestacados as $destacado): ?>
                            <div class="d-flex p-3 border-bottom">
                                <?php if (!empty($destacado['imagen_destacada'])): ?>
                                    <img src="<?= htmlspecialchars($destacado['imagen_destacada']) ?>" 
                                         class="rounded me-3" 
                                         style="width: 60px; height: 60px; object-fit: cover;"
                                         alt="<?= htmlspecialchars($destacado['titulo']) ?>">
                                <?php else: ?>
                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="/blog/<?= htmlspecialchars($destacado['slug']) ?>" 
                                           class="text-decoration-none text-dark hover-primary">
                                            <?= htmlspecialchars($destacado['titulo']) ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?= date('d/m/Y', strtotime($destacado['fecha_publicacion'] ?? $destacado['fecha_creacion'])) ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Categorías -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-folder me-2"></i>Categorías
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="/blog" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-th-large me-2"></i>Todas</span>
                            <span class="badge bg-primary rounded-pill"><?= $totalArticulos ?></span>
                        </a>
                        <a href="/blog/categoria/noticias" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-newspaper me-2"></i>Noticias</span>
                        </a>
                        <a href="/blog/categoria/exposiciones" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-building me-2"></i>Exposiciones</span>
                        </a>
                        <a href="/blog/categoria/arte" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-palette me-2"></i>Arte</span>
                        </a>
                        <a href="/blog/categoria/cultura" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-theater-masks me-2"></i>Cultura</span>
                        </a>
                        <a href="/blog/categoria/historia" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-landmark me-2"></i>Historia</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Newsletter -->
            <div class="card shadow-sm mb-4 bg-light">
                <div class="card-body text-center">
                    <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">¡Suscríbete!</h5>
                    <p class="card-text">Recibe las últimas noticias sobre arte y cultura directamente en tu email.</p>
                    <form class="mb-3">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Tu email">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                    <small class="text-muted">Sin spam, máximo un email por semana</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.hover-primary:hover {
    color: var(--bs-primary) !important;
}

.blog-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem 2rem;
    border-radius: 1rem;
    margin-bottom: 2rem;
}

.blog-header h1 {
    color: white !important;
}

.card-img-top {
    transition: transform 0.3s ease;
}

.card:hover .card-img-top {
    transform: scale(1.05);
}
</style>
