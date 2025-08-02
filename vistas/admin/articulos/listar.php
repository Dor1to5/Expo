<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-newspaper me-2"></i>Gestión de Artículos
    </h1>
    <a href="index.php?ruta=admin/articulos/crear" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nuevo Artículo
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

<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Artículos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalArticulos ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-newspaper fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Publicados
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= count(array_filter($articulos, fn($a) => $a['estado'] === 'publicado')) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Destacados
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= count(array_filter($articulos, fn($a) => $a['destacado'] == 1)) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-star fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Borradores
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= count(array_filter($articulos, fn($a) => $a['estado'] === 'borrador')) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-edit fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-3">
                <select class="form-select" id="filtroEstado" onchange="filtrarTabla()">
                    <option value="">Todos los estados</option>
                    <option value="borrador">Borrador</option>
                    <option value="revision">En Revisión</option>
                    <option value="programado">Programado</option>
                    <option value="publicado">Publicado</option>
                    <option value="archivado">Archivado</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="filtroCategoria" onchange="filtrarTabla()">
                    <option value="">Todas las categorías</option>
                    <option value="noticias">Noticias</option>
                    <option value="exposiciones">Exposiciones</option>
                    <option value="arte">Arte</option>
                    <option value="cultura">Cultura</option>
                    <option value="historia">Historia</option>
                    <option value="educacion">Educación</option>
                    <option value="eventos">Eventos</option>
                    <option value="entrevistas">Entrevistas</option>
                    <option value="opinion">Opinión</option>
                    <option value="tecnica">Técnica</option>
                    <option value="otros">Otros</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" id="buscarArticulo" placeholder="Buscar por título..." onkeyup="filtrarTabla()">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">
                    <i class="fas fa-times"></i> Limpiar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Artículos -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Lista de Artículos</h6>
    </div>
    <div class="card-body">
        <?php if (empty($articulos)): ?>
            <div class="text-center py-5">
                <i class="fas fa-newspaper fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">No hay artículos registrados</h5>
                <p class="text-gray-500">Comienza creando tu primer artículo</p>
                <a href="index.php?ruta=admin/articulos/crear" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Crear Primer Artículo
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered" id="tablaArticulos" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Título</th>
                            <th>Categoría</th>
                            <th>Autor</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Visitas</th>
                            <th>Lectura</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articulos as $articulo): ?>
                            <?php
                            // Determinar badge del estado
                            $estadoBadges = [
                                'borrador' => '<span class="badge bg-secondary">Borrador</span>',
                                'revision' => '<span class="badge bg-warning">En Revisión</span>',
                                'programado' => '<span class="badge bg-info">Programado</span>',
                                'publicado' => '<span class="badge bg-success">Publicado</span>',
                                'archivado' => '<span class="badge bg-dark">Archivado</span>'
                            ];
                            
                            $estadoHtml = $estadoBadges[$articulo['estado']] ?? '<span class="badge bg-secondary">' . ucfirst($articulo['estado']) . '</span>';
                            
                            if ($articulo['destacado']) {
                                $estadoHtml .= ' <span class="badge bg-primary">Destacado</span>';
                            }
                            
                            // Formatear fecha
                            $fecha = $articulo['fecha_publicacion'] ? 
                                date('d/m/Y', strtotime($articulo['fecha_publicacion'])) : 
                                date('d/m/Y', strtotime($articulo['fecha_creacion']));
                            ?>
                            <tr data-estado="<?= $articulo['estado'] ?>" data-categoria="<?= $articulo['categoria'] ?>">
                                <td class="text-center">
                                    <?php if (!empty($articulo['imagen_destacada'])): ?>
                                        <img src="<?= htmlspecialchars($articulo['imagen_destacada']) ?>" 
                                             alt="<?= htmlspecialchars($articulo['titulo']) ?>" 
                                             class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="font-weight-bold"><?= htmlspecialchars($articulo['titulo']) ?></div>
                                    <?php if (!empty($articulo['resumen'])): ?>
                                        <small class="text-muted">
                                            <?= htmlspecialchars(substr($articulo['resumen'], 0, 100)) ?>
                                            <?= strlen($articulo['resumen']) > 100 ? '...' : '' ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?= ucfirst(str_replace('_', ' ', $articulo['categoria'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <small>
                                        <?php if ($articulo['autor_invitado']): ?>
                                            <i class="fas fa-user-edit text-info me-1"></i>
                                            <?= htmlspecialchars($articulo['autor_invitado']) ?>
                                        <?php else: ?>
                                            <i class="fas fa-user text-primary me-1"></i>
                                            <?= htmlspecialchars($articulo['nombre'] ?? 'Usuario') ?> 
                                            <?= htmlspecialchars($articulo['apellidos'] ?? '') ?>
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td><?= $estadoHtml ?></td>
                                <td>
                                    <small><?= $fecha ?></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark">
                                        <?= number_format($articulo['contador_visitas'] ?? 0) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <small>
                                        <i class="fas fa-clock text-muted me-1"></i>
                                        <?= $articulo['tiempo_lectura'] ?? 1 ?> min
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="index.php?ruta=admin/articulos/<?= $articulo['id'] ?>/editar" 
                                           class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($articulo['estado'] === 'publicado'): ?>
                                            <a href="/blog/<?= $articulo['slug'] ?>" 
                                               class="btn btn-sm btn-outline-info" title="Ver" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="index.php?ruta=admin/articulos/<?= $articulo['id'] ?>/preview" 
                                               class="btn btn-sm btn-outline-info" title="Vista Previa">
                                                <i class="fas fa-search"></i>
                                            </a>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmarEliminacion(<?= $articulo['id'] ?>, '<?= htmlspecialchars($articulo['titulo'], ENT_QUOTES) ?>')"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <?php if ($totalPaginas > 1): ?>
                <nav aria-label="Paginación de artículos">
                    <ul class="pagination justify-content-center">
                        <?php if ($paginaActual > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="index.php?ruta=admin/articulos?pagina=<?= $paginaActual - 1 ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                            <li class="page-item <?= $i === $paginaActual ? 'active' : '' ?>">
                                <a class="page-link" href="index.php?ruta=admin/articulos?pagina=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($paginaActual < $totalPaginas): ?>
                            <li class="page-item">
                                <a class="page-link" href="index.php?ruta=admin/articulos?pagina=<?= $paginaActual + 1 ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmarEliminacion(id, titulo) {
    if (confirm(`¿Estás seguro de que deseas eliminar el artículo "${titulo}"?\n\nEsta acción no se puede deshacer.`)) {
        // Crear formulario para envío POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/articulos/${id}/eliminar`;
        
        // Agregar token CSRF si es necesario
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_method';
        csrfInput.value = 'DELETE';
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function filtrarTabla() {
    const filtroEstado = document.getElementById('filtroEstado').value.toLowerCase();
    const filtroCategoria = document.getElementById('filtroCategoria').value.toLowerCase();
    const busqueda = document.getElementById('buscarArticulo').value.toLowerCase();
    const filas = document.querySelectorAll('#tablaArticulos tbody tr');
    
    filas.forEach(fila => {
        const estado = fila.getAttribute('data-estado').toLowerCase();
        const categoria = fila.getAttribute('data-categoria').toLowerCase();
        const titulo = fila.querySelector('td:nth-child(2)').textContent.toLowerCase();
        
        const coincideEstado = !filtroEstado || estado === filtroEstado;
        const coincideCategoria = !filtroCategoria || categoria === filtroCategoria;
        const coincideTitulo = !busqueda || titulo.includes(busqueda);
        
        if (coincideEstado && coincideCategoria && coincideTitulo) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });
}

function limpiarFiltros() {
    document.getElementById('filtroEstado').value = '';
    document.getElementById('filtroCategoria').value = '';
    document.getElementById('buscarArticulo').value = '';
    filtrarTabla();
}

// DataTable si está disponible
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $('#tablaArticulos').DataTable === 'function') {
        $('#tablaArticulos').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "columnDefs": [
                { "orderable": false, "targets": [0, 8] }
            ],
            "order": [[ 5, "desc" ]]
        });
    }
});
</script>

