<!-- Cabecera de la página -->
<div class="bg-primary text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="fw-bold mb-2">
                    <i class="fas fa-images me-3"></i>
                    Exposiciones
                </h1>
                <p class="lead mb-0 opacity-75">
                    Descubre nuestra selección de exposiciones de arte y cultura
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="d-flex justify-content-lg-end align-items-center mt-3 mt-lg-0">
                    <span class="badge bg-light text-primary fs-6 me-3">
                        <?= number_format($total_exposiciones) ?> exposiciones
                    </span>
                    <?php if ($esta_autenticado): ?>
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#filtrosModal">
                            <i class="fas fa-filter me-2"></i>Filtros
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Barra de filtros y búsqueda -->
<div class="bg-light py-3 border-bottom">
    <div class="container">
        <form method="GET" action="/exposiciones" class="row g-3 align-items-center">
            <!-- Búsqueda -->
            <div class="col-lg-4 col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" 
                           name="buscar" 
                           class="form-control" 
                           placeholder="Buscar exposiciones..."
                           value="<?= htmlspecialchars($filtros['buscar'] ?? '') ?>">
                </div>
            </div>
            
            <!-- Categoría -->
            <div class="col-lg-2 col-md-6">
                <select name="categoria" class="form-select">
                    <option value="">Todas las categorías</option>
                    <option value="arte_contemporaneo" 
                            <?= ($filtros['categoria'] ?? '') === 'arte_contemporaneo' ? 'selected' : '' ?>>
                        Arte Contemporáneo
                    </option>
                    <option value="arte_clasico" 
                            <?= ($filtros['categoria'] ?? '') === 'arte_clasico' ? 'selected' : '' ?>>
                        Arte Clásico
                    </option>
                    <option value="fotografia" 
                            <?= ($filtros['categoria'] ?? '') === 'fotografia' ? 'selected' : '' ?>>
                        Fotografía
                    </option>
                    <option value="escultura" 
                            <?= ($filtros['categoria'] ?? '') === 'escultura' ? 'selected' : '' ?>>
                        Escultura
                    </option>
                    <option value="historia" 
                            <?= ($filtros['categoria'] ?? '') === 'historia' ? 'selected' : '' ?>>
                        Historia
                    </option>
                    <option value="ciencias" 
                            <?= ($filtros['categoria'] ?? '') === 'ciencias' ? 'selected' : '' ?>>
                        Ciencias
                    </option>
                </select>
            </div>
            
            <!-- Estado -->
            <div class="col-lg-2 col-md-4">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="activa" 
                            <?= ($filtros['estado'] ?? '') === 'activa' ? 'selected' : '' ?>>
                        Activas
                    </option>
                    <option value="proxima" 
                            <?= ($filtros['estado'] ?? '') === 'proxima' ? 'selected' : '' ?>>
                        Próximamente
                    </option>
                    <option value="finalizada" 
                            <?= ($filtros['estado'] ?? '') === 'finalizada' ? 'selected' : '' ?>>
                        Finalizadas
                    </option>
                </select>
            </div>
            
            <!-- Ordenar -->
            <div class="col-lg-2 col-md-4">
                <select name="ordenar" class="form-select">
                    <option value="fecha_inicio_desc" 
                            <?= ($filtros['ordenar'] ?? '') === 'fecha_inicio_desc' ? 'selected' : '' ?>>
                        Más Recientes
                    </option>
                    <option value="fecha_inicio_asc" 
                            <?= ($filtros['ordenar'] ?? '') === 'fecha_inicio_asc' ? 'selected' : '' ?>>
                        Más Antiguas
                    </option>
                    <option value="titulo_asc" 
                            <?= ($filtros['ordenar'] ?? '') === 'titulo_asc' ? 'selected' : '' ?>>
                        A-Z
                    </option>
                    <option value="precio_asc" 
                            <?= ($filtros['ordenar'] ?? '') === 'precio_asc' ? 'selected' : '' ?>>
                        Precio: Menor
                    </option>
                    <option value="precio_desc" 
                            <?= ($filtros['ordenar'] ?? '') === 'precio_desc' ? 'selected' : '' ?>>
                        Precio: Mayor
                    </option>
                </select>
            </div>
            
            <!-- Botones -->
            <div class="col-lg-2 col-md-4">
                <div class="d-flex gap-1">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search"></i>
                    </button>
                    <a href="/exposiciones" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de exposiciones -->
<div class="container py-5">
    <?php if (!empty($exposiciones)): ?>
        <!-- Información de resultados -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <p class="text-muted mb-0">
                Mostrando <?= count($exposiciones) ?> de <?= number_format($total_exposiciones) ?> exposiciones
                <?php if (!empty($filtros['buscar'])): ?>
                    para "<strong><?= htmlspecialchars($filtros['buscar']) ?></strong>"
                <?php endif; ?>
            </p>
            
            <!-- Vista de grid/lista -->
            <div class="btn-group" role="group">
                <input type="radio" class="btn-check" name="vista" id="vistaGrid" checked>
                <label class="btn btn-outline-secondary" for="vistaGrid">
                    <i class="fas fa-th"></i>
                </label>
                <input type="radio" class="btn-check" name="vista" id="vistaLista">
                <label class="btn btn-outline-secondary" for="vistaLista">
                    <i class="fas fa-list"></i>
                </label>
            </div>
        </div>
        
        <!-- Grid de exposiciones -->
        <div id="exposicionesGrid" class="row">
            <?php foreach ($exposiciones as $exposicion): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 hover-card">
                        <!-- Imagen de la exposición -->
                        <div class="position-relative">
                            <?php if (!empty($exposicion['imagen_principal'])): ?>
                                <img src="<?= htmlspecialchars($exposicion['imagen_principal']) ?>" 
                                     class="card-img-top" 
                                     alt="<?= htmlspecialchars($exposicion['titulo']) ?>"
                                     style="height: 220px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 220px;">
                                    <i class="fas fa-image text-muted fa-3x"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Badge de estado -->
                            <div class="position-absolute top-0 start-0 m-2">
                                <?php 
                                $estado_clase = 'bg-secondary';
                                $estado_texto = 'Pendiente';
                                $estado_icono = 'clock';
                                
                                $hoy = new DateTime();
                                $fecha_inicio = new DateTime($exposicion['fecha_inicio']);
                                $fecha_fin = new DateTime($exposicion['fecha_fin']);
                                
                                if ($hoy >= $fecha_inicio && $hoy <= $fecha_fin) {
                                    $estado_clase = 'bg-success';
                                    $estado_texto = 'Activa';
                                    $estado_icono = 'play';
                                } elseif ($hoy > $fecha_fin) {
                                    $estado_clase = 'bg-danger';
                                    $estado_texto = 'Finalizada';
                                    $estado_icono = 'stop';
                                } elseif ($hoy < $fecha_inicio) {
                                    $estado_clase = 'bg-warning';
                                    $estado_texto = 'Próximamente';
                                    $estado_icono = 'calendar';
                                }
                                ?>
                                <span class="badge <?= $estado_clase ?>">
                                    <i class="fas fa-<?= $estado_icono ?> me-1"></i>
                                    <?= $estado_texto ?>
                                </span>
                            </div>
                            
                            <!-- Badge de precio -->
                            <div class="position-absolute top-0 end-0 m-2">
                                <?php if ($exposicion['precio_entrada'] > 0): ?>
                                    <span class="badge bg-primary">
                                        <?= number_format($exposicion['precio_entrada'], 2) ?>€
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success">Gratis</span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Botón de favorito (solo para usuarios autenticados) -->
                            <?php if ($esta_autenticado): ?>
                                <div class="position-absolute bottom-0 end-0 m-2">
                                    <button class="btn btn-light btn-sm rounded-circle favorito-btn" 
                                            data-exposicion-id="<?= $exposicion['id'] ?>"
                                            title="Añadir a favoritos">
                                        <i class="fas fa-heart text-muted"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Contenido de la tarjeta -->
                        <div class="card-body d-flex flex-column">
                            <div class="mb-2">
                                <span class="badge bg-light text-dark border">
                                    <i class="fas fa-tag me-1"></i>
                                    <?= ucfirst(str_replace('_', ' ', $exposicion['categoria'])) ?>
                                </span>
                            </div>
                            
                            <h5 class="card-title fw-bold">
                                <a href="/exposicion/<?= $exposicion['id'] ?>" 
                                   class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($exposicion['titulo']) ?>
                                </a>
                            </h5>
                            
                            <p class="card-text text-muted flex-grow-1">
                                <?= htmlspecialchars(substr($exposicion['descripcion_corta'] ?? $exposicion['descripcion'], 0, 100)) ?>
                                <?= strlen($exposicion['descripcion_corta'] ?? $exposicion['descripcion']) > 100 ? '...' : '' ?>
                            </p>
                            
                            <!-- Información adicional -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between text-small text-muted mb-1">
                                    <span>
                                        <i class="fas fa-calendar me-1"></i>
                                        <?= date('d/m/Y', strtotime($exposicion['fecha_inicio'])) ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-calendar-check me-1"></i>
                                        <?= date('d/m/Y', strtotime($exposicion['fecha_fin'])) ?>
                                    </span>
                                </div>
                                <div class="text-small text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <?= htmlspecialchars($exposicion['ubicacion']) ?>
                                </div>
                            </div>
                            
                            <!-- Botones de acción -->
                            <div class="d-flex gap-2">
                                <a href="/exposicion/<?= $exposicion['id'] ?>" 
                                   class="btn btn-primary flex-fill">
                                    <i class="fas fa-eye me-1"></i>Ver Detalles
                                </a>
                                <?php if (!empty($exposicion['enlace_compra'])): ?>
                                    <a href="<?= htmlspecialchars($exposicion['enlace_compra']) ?>" 
                                       class="btn btn-outline-success"
                                       target="_blank"
                                       rel="noopener">
                                        <i class="fas fa-ticket-alt"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Vista de lista (oculta por defecto) -->
        <div id="exposicionesLista" class="d-none">
            <?php foreach ($exposiciones as $exposicion): ?>
                <div class="card mb-3 shadow-sm border-0">
                    <div class="row g-0">
                        <div class="col-md-3">
                            <?php if (!empty($exposicion['imagen_principal'])): ?>
                                <img src="<?= htmlspecialchars($exposicion['imagen_principal']) ?>" 
                                     class="img-fluid rounded-start h-100" 
                                     alt="<?= htmlspecialchars($exposicion['titulo']) ?>"
                                     style="object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light h-100 d-flex align-items-center justify-content-center rounded-start">
                                    <i class="fas fa-image text-muted fa-3x"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-light text-dark border me-2">
                                            <?= ucfirst(str_replace('_', ' ', $exposicion['categoria'])) ?>
                                        </span>
                                        <?php 
                                        $hoy = new DateTime();
                                        $fecha_inicio = new DateTime($exposicion['fecha_inicio']);
                                        $fecha_fin = new DateTime($exposicion['fecha_fin']);
                                        
                                        if ($hoy >= $fecha_inicio && $hoy <= $fecha_fin) {
                                            echo '<span class="badge bg-success">Activa</span>';
                                        } elseif ($hoy > $fecha_fin) {
                                            echo '<span class="badge bg-danger">Finalizada</span>';
                                        } else {
                                            echo '<span class="badge bg-warning">Próximamente</span>';
                                        }
                                        ?>
                                    </div>
                                    <div>
                                        <?php if ($exposicion['precio_entrada'] > 0): ?>
                                            <span class="badge bg-primary">
                                                <?= number_format($exposicion['precio_entrada'], 2) ?>€
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Gratis</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <h5 class="card-title fw-bold">
                                    <a href="/exposicion/<?= $exposicion['id'] ?>" 
                                       class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($exposicion['titulo']) ?>
                                    </a>
                                </h5>
                                
                                <p class="card-text text-muted">
                                    <?= htmlspecialchars(substr($exposicion['descripcion_corta'] ?? $exposicion['descripcion'], 0, 200)) ?>
                                    <?= strlen($exposicion['descripcion_corta'] ?? $exposicion['descripcion']) > 200 ? '...' : '' ?>
                                </p>
                                
                                <div class="row text-small text-muted mb-3">
                                    <div class="col-md-6">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?= date('d/m/Y', strtotime($exposicion['fecha_inicio'])) ?> - 
                                        <?= date('d/m/Y', strtotime($exposicion['fecha_fin'])) ?>
                                    </div>
                                    <div class="col-md-6">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?= htmlspecialchars($exposicion['ubicacion']) ?>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <a href="/exposicion/<?= $exposicion['id'] ?>" 
                                       class="btn btn-primary">
                                        <i class="fas fa-eye me-1"></i>Ver Detalles
                                    </a>
                                    <?php if (!empty($exposicion['enlace_compra'])): ?>
                                        <a href="<?= htmlspecialchars($exposicion['enlace_compra']) ?>" 
                                           class="btn btn-outline-success"
                                           target="_blank">
                                            <i class="fas fa-ticket-alt me-1"></i>Entradas
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($esta_autenticado): ?>
                                        <button class="btn btn-outline-danger favorito-btn" 
                                                data-exposicion-id="<?= $exposicion['id'] ?>">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Paginación -->
        <?php if ($total_paginas > 1): ?>
            <nav aria-label="Navegación de exposiciones" class="mt-5">
                <ul class="pagination justify-content-center">
                    <!-- Página anterior -->
                    <?php if ($pagina_actual > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= construir_url_paginacion($pagina_actual - 1, $filtros) ?>">
                                <i class="fas fa-chevron-left me-1"></i>Anterior
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Páginas -->
                    <?php 
                    $inicio = max(1, $pagina_actual - 2);
                    $fin = min($total_paginas, $pagina_actual + 2);
                    
                    if ($inicio > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= construir_url_paginacion(1, $filtros) ?>">1</a>
                        </li>
                        <?php if ($inicio > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $inicio; $i <= $fin; $i++): ?>
                        <li class="page-item <?= $i === $pagina_actual ? 'active' : '' ?>">
                            <a class="page-link" href="<?= construir_url_paginacion($i, $filtros) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($fin < $total_paginas): ?>
                        <?php if ($fin < $total_paginas - 1): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= construir_url_paginacion($total_paginas, $filtros) ?>">
                                <?= $total_paginas ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Página siguiente -->
                    <?php if ($pagina_actual < $total_paginas): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= construir_url_paginacion($pagina_actual + 1, $filtros) ?>">
                                Siguiente<i class="fas fa-chevron-right ms-1"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
        
    <?php else: ?>
        <!-- Estado vacío -->
        <div class="text-center py-5">
            <i class="fas fa-search fa-4x text-muted mb-4"></i>
            <h3 class="text-muted">No se encontraron exposiciones</h3>
            <p class="text-muted mb-4">
                <?php if (!empty($filtros['buscar']) || !empty($filtros['categoria']) || !empty($filtros['estado'])): ?>
                    Intenta ajustar los filtros de búsqueda para encontrar más resultados.
                <?php else: ?>
                    Parece que no hay exposiciones disponibles en este momento.
                <?php endif; ?>
            </p>
            <div class="d-flex justify-content-center gap-2">
                <a href="/exposiciones" class="btn btn-primary">
                    <i class="fas fa-refresh me-2"></i>Ver Todas
                </a>
                <a href="/" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-2"></i>Volver al Inicio
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- JavaScript personalizado -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cambio de vista grid/lista
    const vistaGrid = document.getElementById('vistaGrid');
    const vistaLista = document.getElementById('vistaLista');
    const exposicionesGrid = document.getElementById('exposicionesGrid');
    const exposicionesLista = document.getElementById('exposicionesLista');
    
    if (vistaGrid && vistaLista && exposicionesGrid && exposicionesLista) {
        vistaGrid.addEventListener('change', function() {
            if (this.checked) {
                exposicionesGrid.classList.remove('d-none');
                exposicionesLista.classList.add('d-none');
            }
        });
        
        vistaLista.addEventListener('change', function() {
            if (this.checked) {
                exposicionesGrid.classList.add('d-none');
                exposicionesLista.classList.remove('d-none');
            }
        });
    }
    
    // Funcionalidad de favoritos
    <?php if ($esta_autenticado): ?>
    document.querySelectorAll('.favorito-btn').forEach(button => {
        button.addEventListener('click', function() {
            const exposicionId = this.dataset.exposicionId;
            const icon = this.querySelector('i');
            
            fetch('/api/favoritos', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': window.csrfToken
                },
                body: JSON.stringify({
                    exposicion_id: exposicionId,
                    accion: icon.classList.contains('text-danger') ? 'eliminar' : 'agregar'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    icon.classList.toggle('text-danger');
                    icon.classList.toggle('text-muted');
                    
                    // Mostrar toast de confirmación
                    mostrarToast(data.mensaje, 'success');
                } else {
                    mostrarToast(data.mensaje || 'Error al procesar la solicitud', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarToast('Error de conexión', 'error');
            });
        });
    });
    <?php endif; ?>
});

// Función helper para construir URLs de paginación
<?php
function construir_url_paginacion($pagina, $filtros) {
    $params = array_merge($filtros, ['pagina' => $pagina]);
    $params = array_filter($params, function($value) {
        return $value !== '' && $value !== null;
    });
    
    return '/exposiciones' . (empty($params) ? '' : '?' . http_build_query($params));
}
?>
</script>

<!-- CSS personalizado -->
<style>
.hover-card {
    transition: all 0.3s ease;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.text-small {
    font-size: 0.875rem;
}

.favorito-btn {
    transition: all 0.3s ease;
}

.favorito-btn:hover {
    transform: scale(1.1);
}

.page-link {
    border-radius: 50px;
    margin: 0 2px;
    border: none;
}

.page-item.active .page-link {
    background: linear-gradient(135deg, var(--bs-primary) 0%, #4f46e5 100%);
}

@media (max-width: 768px) {
    .hover-card:hover {
        transform: none;
    }
}
</style>
