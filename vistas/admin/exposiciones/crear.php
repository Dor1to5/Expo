<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-plus me-2"></i>Nueva Exposición
    </h1>
    <a href="/admin/exposiciones" class="btn btn-secondary">
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
                    <i class="fas fa-building me-2"></i>Información de la Exposición
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/exposiciones/crear/procesar" id="formCrearExposicion">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required maxlength="255">
                                <div class="invalid-feedback">
                                    Por favor ingrese el título de la exposición.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
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
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion_corta" class="form-label">Descripción Corta</label>
                        <textarea class="form-control" id="descripcion_corta" name="descripcion_corta" 
                                  rows="2" maxlength="500" 
                                  placeholder="Resumen breve para listados (máximo 500 caracteres)"></textarea>
                        <div class="form-text">
                            <span id="contador-descripcion-corta">0</span>/500 caracteres
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción Completa <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="descripcion" name="descripcion" 
                                  rows="6" required placeholder="Descripción detallada de la exposición"></textarea>
                        <div class="invalid-feedback">
                            Por favor ingrese la descripción completa.
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ubicacion" class="form-label">Ubicación <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ubicacion" name="ubicacion" 
                                       required placeholder="Nombre del lugar o museo">
                                <div class="invalid-feedback">
                                    Por favor ingrese la ubicación.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="precio_entrada" class="form-label">Precio de Entrada (€)</label>
                                <input type="number" class="form-control" id="precio_entrada" name="precio_entrada" 
                                       min="0" step="0.01" value="0.00">
                                <div class="form-text">Ingrese 0 para exposiciones gratuitas</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="direccion_completa" class="form-label">Dirección Completa</label>
                        <textarea class="form-control" id="direccion_completa" name="direccion_completa" 
                                  rows="2" placeholder="Dirección completa del lugar"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                                <div class="invalid-feedback">
                                    Por favor seleccione la fecha de inicio.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_fin" class="form-label">Fecha de Fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                                <div class="invalid-feedback">
                                    Por favor seleccione la fecha de fin.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="imagen_principal" class="form-label">Imagen Principal</label>
                        <input type="url" class="form-control" id="imagen_principal" name="imagen_principal" 
                               placeholder="https://ejemplo.com/imagen.jpg">
                        <div class="form-text">URL de la imagen principal de la exposición</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="video_promocional" class="form-label">Video Promocional</label>
                                <input type="url" class="form-control" id="video_promocional" name="video_promocional" 
                                       placeholder="https://youtube.com/watch?v=...">
                                <div class="form-text">URL del video promocional (YouTube, Vimeo, etc.)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="enlace_compra" class="form-label">Enlace de Compra</label>
                                <input type="url" class="form-control" id="enlace_compra" name="enlace_compra" 
                                       placeholder="https://entradas.ejemplo.com">
                                <div class="form-text">URL para comprar entradas</div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="activa" name="activa" value="1" checked>
                                    <label class="form-check-label" for="activa">
                                        <i class="fas fa-eye text-success me-1"></i>Exposición Activa
                                    </label>
                                    <div class="form-text">Las exposiciones inactivas no se muestran públicamente</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="visible" name="visible" value="1" checked>
                                    <label class="form-check-label" for="visible">
                                        <i class="fas fa-globe text-info me-1"></i>Visible al Público
                                    </label>
                                    <div class="form-text">Controla la visibilidad pública</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="destacada" name="destacada" value="1">
                                    <label class="form-check-label" for="destacada">
                                        <i class="fas fa-star text-warning me-1"></i>Exposición Destacada
                                    </label>
                                    <div class="form-text">Aparecerá en secciones especiales</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/admin/exposiciones" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Crear Exposición
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
                    <small class="text-muted">Debe ser descriptivo y atractivo. Máximo 255 caracteres.</small>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-success">📂 Categorías</h6>
                    <small class="text-muted">Ayuda a organizar y filtrar las exposiciones por temática.</small>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-info">📅 Fechas</h6>
                    <small class="text-muted">La fecha de fin debe ser posterior a la fecha de inicio.</small>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-warning">💰 Precio</h6>
                    <small class="text-muted">Ingrese 0.00 para exposiciones gratuitas. Use punto decimal.</small>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-secondary">🖼️ Imágenes</h6>
                    <small class="text-muted">Use URLs de imágenes alojadas en servicios como Imgur, Google Drive, etc.</small>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCrearExposicion');
    const tituloInput = document.getElementById('titulo');
    const descripcionCortaInput = document.getElementById('descripcion_corta');
    const fechaInicioInput = document.getElementById('fecha_inicio');
    const fechaFinInput = document.getElementById('fecha_fin');
    const contadorDescripcionCorta = document.getElementById('contador-descripcion-corta');
    
    // Contador de caracteres para descripción corta
    descripcionCortaInput.addEventListener('input', function() {
        const longitud = this.value.length;
        contadorDescripcionCorta.textContent = longitud;
        
        if (longitud > 450) {
            contadorDescripcionCorta.style.color = '#dc3545';
        } else if (longitud > 400) {
            contadorDescripcionCorta.style.color = '#ffc107';
        } else {
            contadorDescripcionCorta.style.color = '#6c757d';
        }
    });
    
    // Validación de fechas
    function validarFechas() {
        const fechaInicio = new Date(fechaInicioInput.value);
        const fechaFin = new Date(fechaFinInput.value);
        
        if (fechaInicioInput.value && fechaFinInput.value) {
            if (fechaFin < fechaInicio) {
                fechaFinInput.setCustomValidity('La fecha de fin debe ser posterior a la fecha de inicio');
                fechaFinInput.classList.add('is-invalid');
            } else {
                fechaFinInput.setCustomValidity('');
                fechaFinInput.classList.remove('is-invalid');
            }
        }
    }
    
    fechaInicioInput.addEventListener('change', validarFechas);
    fechaFinInput.addEventListener('change', validarFechas);
    
    // Vista previa simple
    function actualizarVistaPrevia() {
        const titulo = tituloInput.value;
        const imagenUrl = document.getElementById('imagen_principal').value;
        const vistaPrevia = document.getElementById('vista-previa');
        
        if (titulo || imagenUrl) {
            let html = '';
            if (imagenUrl) {
                html += `<img src="${imagenUrl}" class="img-fluid rounded mb-2" style="max-height: 150px;">`;
            }
            if (titulo) {
                html += `<h6 class="text-primary">${titulo}</h6>`;
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
    document.getElementById('imagen_principal').addEventListener('input', actualizarVistaPrevia);
    
    // Validación del formulario
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        form.classList.add('was-validated');
    });
});
</script>
