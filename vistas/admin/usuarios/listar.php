<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Gestión de Usuarios</h1>
    <a href="index.php?ruta=admin/usuarios/crear" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Crear Usuario
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            Lista de Usuarios (<?= $total_usuarios ?>)
        </h6>
    </div>
    <div class="card-body">
        <?php if (empty($usuarios)): ?>
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                <p class="text-muted">No hay usuarios registrados</p>
                <a href="index.php?ruta=admin/usuarios/crear" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Crear Primer Usuario
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Último Acceso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= $usuario['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <?= strtoupper(substr($usuario['nombre'], 0, 1)) ?>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold"><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']) ?></div>
                                            <small class="text-muted">ID: <?= $usuario['id'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($usuario['email']) ?></td>
                                <td>
                                    <span class="badge bg-primary"><?= htmlspecialchars($usuario['rol_nombre']) ?></span>
                                </td>
                                <td>
                                    <?php if ($usuario['activo']): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($usuario['ultimo_acceso']): ?>
                                        <small><?= date('d/m/Y H:i', strtotime($usuario['ultimo_acceso'])) ?></small>
                                    <?php else: ?>
                                        <small class="text-muted">Nunca</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="index.php?ruta=admin/usuarios/<?= $usuario['id'] ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="index.php?ruta=admin/usuarios/<?= $usuario['id'] ?>/editar" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($usuario['id'] != 1): // No eliminar admin principal ?>
                                            <button class="btn btn-sm btn-danger" onclick="eliminarUsuario(<?= $usuario['id'] ?>, '<?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']) ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para confirmar eliminación -->
<div class="modal fade" id="eliminarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar al usuario <strong id="nombreUsuario"></strong>?</p>
                <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="/admin/usuarios/eliminar" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <input type="hidden" name="id" id="idUsuarioEliminar">
                    <button type="submit" class="btn btn-danger">Eliminar Usuario</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function eliminarUsuario(id, nombre) {
    document.getElementById('nombreUsuario').textContent = nombre;
    document.getElementById('idUsuarioEliminar').value = id;
    const modal = new bootstrap.Modal(document.getElementById('eliminarModal'));
    modal.show();
}
</script>

