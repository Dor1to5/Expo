<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-plus me-2"></i>Nuevo Artículo
    </h1>
    <a href="index.php?ruta=admin/articulos" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver a Lista
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

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-newspaper me-2"></i>Contenido del Artículo
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/articulos/crear/procesar" id="formCrearArticulo">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required maxlength="255">
                        <div class="invalid-feedback">
                            Por favor ingrese el título del artículo.
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
                                <select class="form-select" id="categoria" name="categoria" required>
                                    <option value="">Seleccionar categoría...</option>
                                    <?php foreach ($categorias as $clave => $nombre): ?>
                                        <option value="<?= $clave ?>"><?= htmlspecialchars($nombre) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor seleccione una categoría.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="">Seleccionar estado...</option>
                                    <?php foreach ($estados as $clave => $nombre): ?>
                                        <option value="<?= $clave ?>" <?= $clave === 'borrador' ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($nombre) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor seleccione un estado.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="resumen" class="form-label">Resumen</label>
                        <textarea class="form-control" id="resumen" name="resumen" 
                                  rows="3" maxlength="500" 
                                  placeholder="Resumen o entradilla del artículo (máximo 500 caracteres)"></textarea>
                        <div class="form-text">
                            <span id="contador-resumen">0</span>/500 caracteres
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contenido" class="form-label">Contenido <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="contenido" name="contenido" 
                                  rows="15" required placeholder="Contenido completo del artículo"></textarea>
                        <div class="invalid-feedback">
                            Por favor ingrese el contenido del artículo.
                        </div>
                        <div class="form-text">
                            Puede usar HTML básico para formato. Tiempo de lectura estimado: <span id="tiempo-lectura">1 min</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tags" class="form-label">Etiquetas</label>
                        <input type="text" class="form-control" id="tags" name="tags" 
                               placeholder="Separar etiquetas con comas (ej: arte, cultura, historia)">
                        <div class="form-text">Las etiquetas ayudan a categorizar y buscar el artículo</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="imagen_destacada" class="form-label">Imagen Destacada</label>
                        <input type="url" class="form-control" id="imagen_destacada" name="imagen_destacada" 
                               placeholder="https://ejemplo.com/imagen.jpg">
                        <div class="form-text">URL de la imagen principal del artículo</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_publicacion" class="form-label">Fecha de Publicación</label>
                                <input type="datetime-local" class="form-control" id="fecha_publicacion" name="fecha_publicacion">
                                <div class="form-text">Dejar vacío para publicar inmediatamente</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="autor_invitado" class="form-label">Autor Invitado</label>
                                <input type="text" class="form-control" id="autor_invitado" name="autor_invitado" 
                                       placeholder="Nombre del autor invitado">
                                <div class="form-text">Solo si el autor no es un usuario registrado</div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="destacado" name="destacado" value="1">
                                    <label class="form-check-label" for="destacado">
                                        <i class="fas fa-star text-warning me-1"></i>Artículo Destacado
                                    </label>
                                    <div class="form-text">Aparecerá en secciones especiales y sidebar</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="permitir_comentarios" name="permitir_comentarios" value="1" checked>
                                    <label class="form-check-label" for="permitir_comentarios">
                                        <i class="fas fa-comments text-info me-1"></i>Permitir Comentarios
                                    </label>
                                    <div class="form-text">Los usuarios podrán comentar este artículo</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-outline-info me-md-2" onclick="previsualizarArticulo()">
                            <i class="fas fa-eye me-2"></i>Vista Previa
                        </button>
                        <a href="index.php?ruta=admin/articulos" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Crear Artículo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>Información de Ayuda
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-primary">📝 Título</h6>
                    <small class="text-muted">Debe ser descriptivo y atractivo para el SEO. Máximo 255 caracteres.</small>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-success">📂 Categoría</h6>
                    <small class="text-muted">Ayuda a organizar el contenido y facilita la navegación.</small>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-info">📊 Estados</h6>
                    <ul class="list-unstyled">
                        <li><span class="badge bg-secondary">Borrador</span> - Solo visible para editores</li>
                        <li><span class="badge bg-warning">En Revisión</span> - Pendiente de aprobación</li>
                        <li><span class="badge bg-info">Programado</span> - Se publicará automáticamente</li>
                        <li><span class="badge bg-success">Publicado</span> - Visible para todos</li>
                        <li><span class="badge bg-dark">Archivado</span> - No visible públicamente</li>
                    </ul>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-warning">🏷️ Etiquetas</h6>
                    <small class="text-muted">Ayudan con el SEO y permiten búsquedas más precisas.</small>
                </div>
            </div>
        </div>
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar me-2"></i>Estadísticas
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Palabras:</small>
                    <span class="float-end" id="contador-palabras">0</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Caracteres:</small>
                    <span class="float-end" id="contador-caracteres">0</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Tiempo de lectura:</small>
                    <span class="float-end" id="tiempo-lectura-sidebar">1 min</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Párrafos:</small>
                    <span class="float-end" id="contador-parrafos">0</span>
                </div>
            </div>
        </div>
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-eye me-2"></i>Vista Previa
                </h6>
            </div>
            <div class="card-body">
                <div id="vista-previa" class="text-center text-muted">
                    <i class="fas fa-image fa-3x mb-2"></i>
                    <p>Complete el formulario para ver una vista previa</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Vista Previa -->
<div class="modal fade" id="modalVistaPrevia" tabindex="-1" aria-labelledby="modalVistaPreviaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVistaPreviaLabel">Vista Previa del Artículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="contenido-preview"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCrearArticulo');
    const tituloInput = document.getElementById('titulo');
    const resumenInput = document.getElementById('resumen');
    const contenidoInput = document.getElementById('contenido');
    const estadoSelect = document.getElementById('estado');
    const fechaPublicacionInput = document.getElementById('fecha_publicacion');
    
    // Contadores
    const contadorResumen = document.getElementById('contador-resumen');
    const contadorPalabras = document.getElementById('contador-palabras');
    const contadorCaracteres = document.getElementById('contador-caracteres');
    const contadorParrafos = document.getElementById('contador-parrafos');
    const tiempoLectura = document.getElementById('tiempo-lectura');
    const tiempoLecturaSidebar = document.getElementById('tiempo-lectura-sidebar');
    
    // Contador de caracteres para resumen
    resumenInput.addEventListener('input', function() {
        const longitud = this.value.length;
        contadorResumen.textContent = longitud;
        
        if (longitud > 450) {
            contadorResumen.style.color = '#dc3545';
        } else if (longitud > 400) {
            contadorResumen.style.color = '#ffc107';
        } else {
            contadorResumen.style.color = '#6c757d';
        }
    });
    
    // Estadísticas del contenido
    contenidoInput.addEventListener('input', function() {
        const texto = this.value;
        const textoLimpio = texto.replace(/<[^>]*>/g, ''); // Remover HTML
        
        // Contar palabras
        const palabras = textoLimpio.trim() ? textoLimpio.trim().split(/\s+/).length : 0;
        contadorPalabras.textContent = palabras;
        
        // Contar caracteres
        contadorCaracteres.textContent = textoLimpio.length;
        
        // Contar párrafos
        const parrafos = texto.split(/\n\s*\n/).filter(p => p.trim().length > 0).length;
        contadorParrafos.textContent = parrafos;
        
        // Calcular tiempo de lectura (200 palabras por minuto)
        const minutos = Math.max(1, Math.ceil(palabras / 200));
        tiempoLectura.textContent = `${minutos} min`;
        tiempoLecturaSidebar.textContent = `${minutos} min`;
        
        actualizarVistaPrevia();
    });
    
    // Validación de fecha para artículos programados
    estadoSelect.addEventListener('change', function() {
        if (this.value === 'programado') {
            fechaPublicacionInput.required = true;
            fechaPublicacionInput.closest('.mb-3').querySelector('.form-text').innerHTML = 
                '<span class="text-danger">Requerido para artículos programados</span>';
        } else {
            fechaPublicacionInput.required = false;
            fechaPublicacionInput.closest('.mb-3').querySelector('.form-text').innerHTML = 
                'Dejar vacío para publicar inmediatamente';
        }
    });
    
    // Vista previa simple
    function actualizarVistaPrevia() {
        const titulo = tituloInput.value;
        const imagenUrl = document.getElementById('imagen_destacada').value;
        const resumen = resumenInput.value;
        const vistaPrevia = document.getElementById('vista-previa');
        
        if (titulo || imagenUrl || resumen) {
            let html = '';
            if (imagenUrl) {
                html += `<img src="${imagenUrl}" class="img-fluid rounded mb-2" style="max-height: 120px;">`;
            }
            if (titulo) {
                html += `<h6 class="text-primary mb-2">${titulo}</h6>`;
            }
            if (resumen) {
                html += `<small class="text-muted">${resumen.substring(0, 100)}${resumen.length > 100 ? '...' : ''}</small>`;
            }
            vistaPrevia.innerHTML = html;
        } else {
            vistaPrevia.innerHTML = `
                <i class="fas fa-image fa-3x mb-2"></i>
                <p>Complete el formulario para ver una vista previa</p>
            `;
        }
    }
    
    tituloInput.addEventListener('input', actualizarVistaPrevia);
    resumenInput.addEventListener('input', actualizarVistaPrevia);
    document.getElementById('imagen_destacada').addEventListener('input', actualizarVistaPrevia);
    
    // Validación del formulario
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        form.classList.add('was-validated');
    });
});

function previsualizarArticulo() {
    const titulo = document.getElementById('titulo').value;
    const resumen = document.getElementById('resumen').value;
    const contenido = document.getElementById('contenido').value;
    const imagen = document.getElementById('imagen_destacada').value;
    const autor = document.getElementById('autor_invitado').value || 'Autor del sistema';
    
    let html = '<article class="preview-article">';
    
    if (imagen) {
        html += `<img src="${imagen}" class="img-fluid rounded mb-3" style="max-height: 300px; width: 100%; object-fit: cover;">`;
    }
    
    if (titulo) {
        html += `<h1 class="mb-3">${titulo}</h1>`;
    }
    
    html += `<p class="text-muted mb-3"><i class="fas fa-user me-2"></i>${autor} • <i class="fas fa-clock me-2"></i>${document.getElementById('tiempo-lectura').textContent}</p>`;
    
    if (resumen) {
        html += `<div class="lead mb-4">${resumen}</div>`;
    }
    
    if (contenido) {
        html += `<div class="article-content">${contenido.replace(/\n/g, '<br>')}</div>`;
    }
    
    html += '</article>';
    
    document.getElementById('contenido-preview').innerHTML = html;
    new bootstrap.Modal(document.getElementById('modalVistaPrevia')).show();
}
</script>

