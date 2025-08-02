<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-building me-2"></i>Gestión de Exposiciones
    </h1>
    <a href="/admin/exposiciones/crear" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nueva Exposición
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
                            Total Exposiciones
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalExposiciones ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
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
                            Activas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= count(array_filter($exposiciones, fn($e) => $e['activa'] == 1)) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-eye fa-2x text-gray-300"></i>
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
                            Destacadas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= count(array_filter($exposiciones, fn($e) => $e['destacada'] == 1)) ?>
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
                            En Curso
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php 
                            $hoy = date('Y-m-d');
                            $enCurso = array_filter($exposiciones, fn($e) => 
                                $e['fecha_inicio'] <= $hoy && $e['fecha_fin'] >= $hoy && $e['activa'] == 1
                            );
                            echo count($enCurso);
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Exposiciones -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Lista de Exposiciones</h6>
    </div>
    <div class="card-body">
        <?php if (empty($exposiciones)): ?>
            <div class="text-center py-5">
                <i class="fas fa-building fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">No hay exposiciones registradas</h5>
                <p class="text-gray-500">Comienza creando tu primera exposición</p>
                <a href="/admin/exposiciones/crear" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Crear Primera Exposición
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Título</th>
                            <th>Ubicación</th>
                            <th>Fechas</th>
                            <th>Estado</th>
                            <th>Visitas</th>
                            <th>Creador</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exposiciones as $exposicion): ?>
                            <?php
                            $hoy = date('Y-m-d');
                            $estadoExposicion = '';
                            $claseEstado = '';
                            
                            if (!$exposicion['activa']) {
                                $estadoExposicion = '<span class="badge bg-secondary">Inactiva</span>';
                            } elseif ($exposicion['fecha_inicio'] > $hoy) {
                                $estadoExposicion = '<span class="badge bg-info">Próxima</span>';
                            } elseif ($exposicion['fecha_fin'] < $hoy) {
                                $estadoExposicion = '<span class="badge bg-warning">Finalizada</span>';
                            } else {
                                $estadoExposicion = '<span class="badge bg-success">En Curso</span>';
                            }
                            
                            if ($exposicion['destacada']) {
                                $estadoExposicion .= ' <span class="badge bg-primary">Destacada</span>';
                            }
                            ?>
                            <tr>
                                <td class="text-center">
                                    <?php if (!empty($exposicion['imagen_principal'])): ?>
                                        <img src="<?= htmlspecialchars($exposicion['imagen_principal']) ?>" 
                                             alt="<?= htmlspecialchars($exposicion['titulo']) ?>" 
                                             class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="font-weight-bold"><?= htmlspecialchars($exposicion['titulo']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($exposicion['categoria']) ?></small>
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                    <?= htmlspecialchars($exposicion['ubicacion']) ?>
                                </td>
                                <td>
                                    <small>
                                        <strong>Inicio:</strong> <?= date('d/m/Y', strtotime($exposicion['fecha_inicio'])) ?><br>
                                        <strong>Fin:</strong> <?= date('d/m/Y', strtotime($exposicion['fecha_fin'])) ?>
                                    </small>
                                </td>
                                <td><?= $estadoExposicion ?></td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark">
                                        <?= number_format($exposicion['contador_visitas'] ?? 0) ?>
                                    </span>
                                </td>
                                <td>
                                    <small>
                                        <?= htmlspecialchars($exposicion['nombre'] ?? 'Usuario') ?> 
                                        <?= htmlspecialchars($exposicion['apellidos'] ?? '') ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/admin/exposiciones/<?= $exposicion['id'] ?>/editar" 
                                           class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/admin/exposiciones/<?= $exposicion['id'] ?>" 
                                           class="btn btn-sm btn-outline-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmarEliminacion(<?= $exposicion['id'] ?>, '<?= htmlspecialchars($exposicion['titulo'], ENT_QUOTES) ?>')"
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
                <nav aria-label="Paginación de exposiciones">
                    <ul class="pagination justify-content-center">
                        <?php if ($paginaActual > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="/admin/exposiciones?pagina=<?= $paginaActual - 1 ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                            <li class="page-item <?= $i === $paginaActual ? 'active' : '' ?>">
                                <a class="page-link" href="/admin/exposiciones?pagina=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($paginaActual < $totalPaginas): ?>
                            <li class="page-item">
                                <a class="page-link" href="/admin/exposiciones?pagina=<?= $paginaActual + 1 ?>">
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
    if (confirm(`¿Estás seguro de que deseas eliminar la exposición "${titulo}"?\n\nEsta acción no se puede deshacer.`)) {
        // Crear formulario para envío POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/exposiciones/${id}/eliminar`;
        
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

// DataTable si está disponible
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $('#dataTable').DataTable === 'function') {
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "columnDefs": [
                { "orderable": false, "targets": [0, 7] }
            ],
            "order": [[ 3, "desc" ]]
        });
    }
});
</script>
