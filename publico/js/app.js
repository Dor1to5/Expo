/*!
 * JavaScript Principal - Sistema de Exposiciones
 * Versión: 1.0.0
 * Autor: Sistema de Gestión de Exposiciones
 * Descripción: Funcionalidades JavaScript principales del sistema
 */

'use strict';

// =====================================================
// CONFIGURACIÓN GLOBAL
// =====================================================
window.SistemaExposiciones = window.SistemaExposiciones || {};

(function() {
    // Configuración por defecto
    const CONFIG = {
        // URLs de la API
        api: {
            base: '/api',
            favoritos: '/api/favoritos',
            comentarios: '/api/comentarios',
            valoraciones: '/api/valoraciones',
            suscripciones: '/api/suscripciones'
        },
        
        // Configuración de alertas
        alertas: {
            duracion: 5000,
            posicion: 'top-end'
        },
        
        // Configuración de animaciones
        animaciones: {
            duracion: 300,
            easing: 'ease-in-out'
        },
        
        // Configuración de validación
        validacion: {
            email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            telefono: /^[+]?[\d\s\-\(\)]{9,}$/,
            password: {
                minLength: 8,
                requireNumbers: true,
                requireSpecialChars: true,
                requireMixedCase: true
            }
        }
    };

    // =====================================================
    // UTILIDADES GLOBALES
    // =====================================================
    const Utils = {
        /**
         * Realiza una petición fetch con configuración por defecto
         */
        async fetch(url, options = {}) {
            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': window.csrfToken || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            };

            const config = { ...defaultOptions, ...options };
            
            if (config.body && typeof config.body === 'object') {
                config.body = JSON.stringify(config.body);
            }

            try {
                const response = await fetch(url, config);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return await response.json();
                }
                
                return await response.text();
            } catch (error) {
                console.error('Error en la petición:', error);
                throw error;
            }
        },

        /**
         * Muestra una notificación toast
         */
        showToast(mensaje, tipo = 'info', opciones = {}) {
            const toastId = 'toast-' + Date.now();
            const iconos = {
                success: 'fas fa-check-circle',
                error: 'fas fa-exclamation-triangle',
                warning: 'fas fa-exclamation-triangle',
                info: 'fas fa-info-circle'
            };

            const colores = {
                success: 'success',
                error: 'danger',
                warning: 'warning',
                info: 'info'
            };

            const toast = document.createElement('div');
            toast.id = toastId;
            toast.className = `toast align-items-center text-bg-${colores[tipo]} border-0`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');

            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="${iconos[tipo]} me-2"></i>
                        ${mensaje}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                            data-bs-dismiss="toast" aria-label="Cerrar"></button>
                </div>
            `;

            // Contenedor de toasts
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'toast-container position-fixed top-0 end-0 p-3';
                container.style.zIndex = '9999';
                document.body.appendChild(container);
            }

            container.appendChild(toast);

            // Inicializar el toast de Bootstrap
            const bsToast = new bootstrap.Toast(toast, {
                delay: opciones.delay || CONFIG.alertas.duracion
            });

            bsToast.show();

            // Eliminar el elemento del DOM después de ocultarse
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });

            return bsToast;
        },

        /**
         * Valida un email
         */
        validateEmail(email) {
            return CONFIG.validacion.email.test(email);
        },

        /**
         * Valida una contraseña
         */
        validatePassword(password) {
            const config = CONFIG.validacion.password;
            const errors = [];

            if (password.length < config.minLength) {
                errors.push(`Mínimo ${config.minLength} caracteres`);
            }

            if (config.requireNumbers && !/\d/.test(password)) {
                errors.push('Debe contener números');
            }

            if (config.requireSpecialChars && !/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                errors.push('Debe contener caracteres especiales');
            }

            if (config.requireMixedCase && (!/[a-z]/.test(password) || !/[A-Z]/.test(password))) {
                errors.push('Debe contener mayúsculas y minúsculas');
            }

            return {
                valid: errors.length === 0,
                errors: errors,
                strength: this.getPasswordStrength(password)
            };
        },

        /**
         * Calcula la fuerza de una contraseña
         */
        getPasswordStrength(password) {
            let strength = 0;
            
            if (password.length >= 8) strength += 25;
            if (/[a-z]/.test(password)) strength += 25;
            if (/[A-Z]/.test(password)) strength += 25;
            if (/\d/.test(password)) strength += 25;
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 10;

            return Math.min(strength, 100);
        },

        /**
         * Debounce function
         */
        debounce(func, wait, immediate) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    timeout = null;
                    if (!immediate) func(...args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func(...args);
            };
        },

        /**
         * Throttle function
         */
        throttle(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        /**
         * Formatea una fecha
         */
        formatDate(date, options = {}) {
            const defaultOptions = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            
            const config = { ...defaultOptions, ...options };
            return new Date(date).toLocaleDateString('es-ES', config);
        },

        /**
         * Formatea un número
         */
        formatNumber(number, decimals = 0) {
            return new Intl.NumberFormat('es-ES', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            }).format(number);
        },

        /**
         * Genera un slug a partir de un texto
         */
        generateSlug(text) {
            return text
                .toLowerCase()
                .trim()
                .replace(/[áàäâ]/g, 'a')
                .replace(/[éèëê]/g, 'e')
                .replace(/[íìïî]/g, 'i')
                .replace(/[óòöô]/g, 'o')
                .replace(/[úùüû]/g, 'u')
                .replace(/ñ/g, 'n')
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
        }
    };

    // =====================================================
    // GESTIÓN DE FAVORITOS
    // =====================================================
    const Favoritos = {
        async toggle(tipo, id) {
            try {
                const response = await Utils.fetch(CONFIG.api.favoritos, {
                    method: 'POST',
                    body: {
                        tipo: tipo,
                        id: id,
                        accion: 'toggle'
                    }
                });

                if (response.success) {
                    this.updateUI(tipo, id, response.esFavorito);
                    Utils.showToast(response.mensaje, 'success');
                    return response.esFavorito;
                } else {
                    Utils.showToast(response.mensaje || 'Error al procesar la solicitud', 'error');
                    return null;
                }
            } catch (error) {
                console.error('Error al gestionar favorito:', error);
                Utils.showToast('Error de conexión', 'error');
                return null;
            }
        },

        updateUI(tipo, id, esFavorito) {
            const buttons = document.querySelectorAll(`[data-favorito-tipo="${tipo}"][data-favorito-id="${id}"]`);
            
            buttons.forEach(button => {
                const icon = button.querySelector('i');
                if (icon) {
                    if (esFavorito) {
                        icon.classList.remove('far', 'text-muted');
                        icon.classList.add('fas', 'text-danger');
                        button.setAttribute('title', 'Quitar de favoritos');
                    } else {
                        icon.classList.remove('fas', 'text-danger');
                        icon.classList.add('far', 'text-muted');
                        button.setAttribute('title', 'Añadir a favoritos');
                    }
                }
            });
        },

        init() {
            document.addEventListener('click', (e) => {
                const button = e.target.closest('[data-favorito-tipo]');
                if (button) {
                    e.preventDefault();
                    const tipo = button.dataset.favoritoTipo;
                    const id = button.dataset.favoritoId;
                    this.toggle(tipo, id);
                }
            });
        }
    };

    // =====================================================
    // GESTIÓN DE COMENTARIOS
    // =====================================================
    const Comentarios = {
        async enviar(tipo, id, contenido, padre = null) {
            try {
                const response = await Utils.fetch(CONFIG.api.comentarios, {
                    method: 'POST',
                    body: {
                        tipo: tipo,
                        id: id,
                        contenido: contenido,
                        padre: padre
                    }
                });

                if (response.success) {
                    Utils.showToast('Comentario enviado correctamente', 'success');
                    this.limpiarFormulario();
                    if (response.comentario) {
                        this.agregarComentarioDOM(response.comentario);
                    }
                } else {
                    Utils.showToast(response.mensaje || 'Error al enviar el comentario', 'error');
                }
            } catch (error) {
                console.error('Error al enviar comentario:', error);
                Utils.showToast('Error de conexión', 'error');
            }
        },

        limpiarFormulario() {
            const form = document.getElementById('form-comentario');
            if (form) {
                form.reset();
                const textarea = form.querySelector('textarea');
                if (textarea) {
                    textarea.style.height = 'auto';
                }
            }
        },

        agregarComentarioDOM(comentario) {
            const container = document.getElementById('comentarios-lista');
            if (container) {
                const comentarioHTML = this.generarComentarioHTML(comentario);
                container.insertAdjacentHTML('afterbegin', comentarioHTML);
            }
        },

        generarComentarioHTML(comentario) {
            return `
                <div class="comentario mb-3 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>${comentario.autor_nombre}</strong>
                            <small class="text-muted ms-2">${Utils.formatDate(comentario.fecha_creacion)}</small>
                        </div>
                        <span class="badge bg-warning">Pendiente de moderación</span>
                    </div>
                    <div class="mt-2">
                        ${comentario.contenido}
                    </div>
                </div>
            `;
        },

        init() {
            const form = document.getElementById('form-comentario');
            if (form) {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const formData = new FormData(form);
                    const tipo = form.dataset.tipo;
                    const id = form.dataset.id;
                    const contenido = formData.get('contenido');
                    const padre = formData.get('padre') || null;
                    
                    if (contenido.trim()) {
                        this.enviar(tipo, id, contenido, padre);
                    }
                });
            }

            // Auto-resize de textareas
            document.querySelectorAll('textarea').forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                });
            });
        }
    };

    // =====================================================
    // GESTIÓN DE FORMULARIOS
    // =====================================================
    const Formularios = {
        init() {
            // Validación en tiempo real
            document.querySelectorAll('form[data-validate]').forEach(form => {
                this.setupValidation(form);
            });

            // Toggle de contraseñas
            document.querySelectorAll('[data-toggle-password]').forEach(button => {
                button.addEventListener('click', this.togglePassword);
            });

            // Indicador de fuerza de contraseña
            document.querySelectorAll('input[type="password"][data-strength]').forEach(input => {
                this.setupPasswordStrength(input);
            });
        },

        setupValidation(form) {
            const inputs = form.querySelectorAll('input, select, textarea');
            
            inputs.forEach(input => {
                input.addEventListener('blur', () => this.validateField(input));
                input.addEventListener('input', Utils.debounce(() => this.validateField(input), 300));
            });

            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        },

        validateField(field) {
            let valid = true;
            let message = '';

            if (field.hasAttribute('required') && !field.value.trim()) {
                valid = false;
                message = 'Este campo es obligatorio';
            } else if (field.type === 'email' && field.value && !Utils.validateEmail(field.value)) {
                valid = false;
                message = 'Introduce un email válido';
            } else if (field.type === 'password' && field.value) {
                const validation = Utils.validatePassword(field.value);
                if (!validation.valid) {
                    valid = false;
                    message = validation.errors.join(', ');
                }
            }

            this.setFieldValidation(field, valid, message);
            return valid;
        },

        validateForm(form) {
            const fields = form.querySelectorAll('input, select, textarea');
            let valid = true;

            fields.forEach(field => {
                if (!this.validateField(field)) {
                    valid = false;
                }
            });

            return valid;
        },

        setFieldValidation(field, valid, message) {
            field.classList.toggle('is-invalid', !valid);
            field.classList.toggle('is-valid', valid && field.value.trim());

            const feedback = field.parentNode.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.textContent = message;
            }
        },

        togglePassword(e) {
            const button = e.currentTarget;
            const targetId = button.dataset.togglePassword;
            const target = document.getElementById(targetId);
            
            if (target) {
                const isPassword = target.type === 'password';
                target.type = isPassword ? 'text' : 'password';
                
                const icon = button.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye', !isPassword);
                    icon.classList.toggle('fa-eye-slash', isPassword);
                }
            }
        },

        setupPasswordStrength(input) {
            const container = input.parentNode;
            let strengthBar = container.querySelector('.password-strength');
            
            if (!strengthBar) {
                strengthBar = document.createElement('div');
                strengthBar.className = 'password-strength mt-1';
                strengthBar.innerHTML = `
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <small class="strength-text text-muted"></small>
                `;
                container.appendChild(strengthBar);
            }

            input.addEventListener('input', () => {
                const strength = Utils.getPasswordStrength(input.value);
                const progressBar = strengthBar.querySelector('.progress-bar');
                const strengthText = strengthBar.querySelector('.strength-text');

                progressBar.style.width = strength + '%';
                
                let colorClass = 'bg-danger';
                let text = 'Muy débil';
                
                if (strength >= 75) {
                    colorClass = 'bg-success';
                    text = 'Fuerte';
                } else if (strength >= 50) {
                    colorClass = 'bg-warning';
                    text = 'Moderada';
                } else if (strength >= 25) {
                    colorClass = 'bg-info';
                    text = 'Débil';
                }

                progressBar.className = `progress-bar ${colorClass}`;
                strengthText.textContent = text;
            });
        }
    };

    // =====================================================
    // GESTIÓN DE BÚSQUEDA
    // =====================================================
    const Busqueda = {
        init() {
            const searchForms = document.querySelectorAll('[data-search-form]');
            
            searchForms.forEach(form => {
                const input = form.querySelector('input[type="search"], input[name="buscar"]');
                if (input) {
                    input.addEventListener('input', Utils.debounce(() => {
                        this.realizarBusqueda(input.value, form);
                    }, 500));
                }
            });
        },

        async realizarBusqueda(termino, form) {
            if (termino.length < 3) return;

            const tipo = form.dataset.searchType || 'general';
            
            try {
                const response = await Utils.fetch(`/api/buscar?q=${encodeURIComponent(termino)}&tipo=${tipo}`);
                this.mostrarResultados(response.resultados, form);
            } catch (error) {
                console.error('Error en la búsqueda:', error);
            }
        },

        mostrarResultados(resultados, form) {
            let container = form.querySelector('.search-results');
            
            if (!container) {
                container = document.createElement('div');
                container.className = 'search-results position-absolute bg-white border rounded shadow-sm';
                container.style.cssText = 'top: 100%; left: 0; right: 0; z-index: 1000; max-height: 300px; overflow-y: auto;';
                form.style.position = 'relative';
                form.appendChild(container);
            }

            if (resultados.length === 0) {
                container.innerHTML = '<div class="p-3 text-muted">No se encontraron resultados</div>';
            } else {
                container.innerHTML = resultados.map(resultado => `
                    <a href="${resultado.url}" class="d-block p-2 text-decoration-none border-bottom">
                        <div class="fw-semibold">${resultado.titulo}</div>
                        <small class="text-muted">${resultado.descripcion}</small>
                    </a>
                `).join('');
            }

            container.style.display = 'block';

            // Ocultar resultados al hacer clic fuera
            document.addEventListener('click', (e) => {
                if (!form.contains(e.target)) {
                    container.style.display = 'none';
                }
            }, { once: true });
        }
    };

    // =====================================================
    // GESTIÓN DE ANIMACIONES
    // =====================================================
    const Animaciones = {
        init() {
            // Intersection Observer para animaciones al hacer scroll
            if ('IntersectionObserver' in window) {
                this.setupScrollAnimations();
            }

            // Animaciones de carga
            this.animateOnLoad();
        },

        setupScrollAnimations() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const element = entry.target;
                        element.classList.add('animate__animated');
                        
                        const animation = element.dataset.animation || 'fadeInUp';
                        element.classList.add(`animate__${animation}`);
                        
                        observer.unobserve(element);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            document.querySelectorAll('[data-animation]').forEach(el => {
                observer.observe(el);
            });
        },

        animateOnLoad() {
            // Animación de números contadores
            document.querySelectorAll('[data-counter]').forEach(el => {
                this.animateCounter(el);
            });
        },

        animateCounter(element) {
            const target = parseInt(element.dataset.counter);
            const duration = parseInt(element.dataset.duration) || 2000;
            const increment = target / (duration / 16);
            
            let current = 0;
            const timer = setInterval(() => {
                current += increment;
                element.textContent = Math.floor(current).toLocaleString('es-ES');
                
                if (current >= target) {
                    element.textContent = target.toLocaleString('es-ES');
                    clearInterval(timer);
                }
            }, 16);
        }
    };

    // =====================================================
    // INICIALIZACIÓN
    // =====================================================
    const App = {
        init() {
            // Verificar que Bootstrap esté cargado
            if (typeof bootstrap === 'undefined') {
                console.error('Bootstrap JavaScript no está cargado');
                return;
            }

            // Inicializar módulos
            Favoritos.init();
            Comentarios.init();
            Formularios.init();
            Busqueda.init();
            Animaciones.init();

            // Configurar tooltips y popovers de Bootstrap
            this.initBootstrapComponents();

            // Configurar eventos globales
            this.setupGlobalEvents();

            console.log('Sistema de Exposiciones inicializado correctamente');
        },

        initBootstrapComponents() {
            // Inicializar tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            // Inicializar popovers
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
        },

        setupGlobalEvents() {
            // Confirmar acciones destructivas
            document.addEventListener('click', (e) => {
                const element = e.target.closest('[data-confirm]');
                if (element) {
                    const message = element.dataset.confirm || '¿Estás seguro?';
                    if (!confirm(message)) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                }
            });

            // Auto-submit en cambios de select
            document.addEventListener('change', (e) => {
                if (e.target.matches('[data-auto-submit]')) {
                    e.target.closest('form').submit();
                }
            });

            // Copiar al portapapeles
            document.addEventListener('click', (e) => {
                const button = e.target.closest('[data-clipboard]');
                if (button) {
                    const text = button.dataset.clipboard;
                    this.copyToClipboard(text);
                    Utils.showToast('Copiado al portapapeles', 'success');
                }
            });
        },

        async copyToClipboard(text) {
            try {
                await navigator.clipboard.writeText(text);
            } catch (err) {
                // Fallback para navegadores sin soporte
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
            }
        }
    };

    // Exportar API pública
    window.SistemaExposiciones = {
        CONFIG,
        Utils,
        Favoritos,
        Comentarios,
        Formularios,
        Busqueda,
        Animaciones,
        App
    };

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => App.init());
    } else {
        App.init();
    }

})();

// =====================================================
// FUNCIONES GLOBALES DE CONVENIENCIA
// =====================================================

// Función global para mostrar toasts
window.mostrarToast = function(mensaje, tipo = 'info', opciones = {}) {
    return window.SistemaExposiciones.Utils.showToast(mensaje, tipo, opciones);
};

// Función global para hacer peticiones
window.apiCall = function(url, options = {}) {
    return window.SistemaExposiciones.Utils.fetch(url, options);
};

// Función global para validar emails
window.validarEmail = function(email) {
    return window.SistemaExposiciones.Utils.validateEmail(email);
};

// =====================================================
// SERVICE WORKER REGISTRATION (OPCIONAL)
// =====================================================
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('SW registrado correctamente: ', registration);
            })
            .catch(registrationError => {
                console.log('SW falló al registrarse: ', registrationError);
            });
    });
}
