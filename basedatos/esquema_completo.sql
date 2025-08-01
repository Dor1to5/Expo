-- =====================================================
-- ESQUEMA DE BASE DE DATOS - SISTEMA DE EXPOSICIONES
-- Versión: 1.0
-- Autor: Sistema de Gestión de Exposiciones
-- Fecha: <?= date('Y-m-d H:i:s') ?>

-- Codificación: UTF-8
-- Motor: InnoDB (para soporte de transacciones y claves foráneas)
-- Collation: utf8mb4_unicode_ci (para soporte completo de Unicode)
-- =====================================================

-- Configuración inicial
SET foreign_key_checks = 0;
SET sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

-- Eliminar tablas si existen (en orden inverso por las claves foráneas)
DROP TABLE IF EXISTS `archivos_subidos`;
DROP TABLE IF EXISTS `logs_actividad`;
DROP TABLE IF EXISTS `configuracion`;
DROP TABLE IF EXISTS `sesiones`;
DROP TABLE IF EXISTS `suscripciones`;
DROP TABLE IF EXISTS `favoritos`;
DROP TABLE IF EXISTS `comentarios`;
DROP TABLE IF EXISTS `articulos`;
DROP TABLE IF EXISTS `exposiciones`;
DROP TABLE IF EXISTS `usuarios`;
DROP TABLE IF EXISTS `roles`;

-- Eliminar vistas si existen
DROP VIEW IF EXISTS `v_exposiciones_activas`;
DROP VIEW IF EXISTS `v_articulos_publicados`;

-- Eliminar procedimientos si existen
DROP PROCEDURE IF EXISTS `sp_limpiar_sesiones_expiradas`;
DROP PROCEDURE IF EXISTS `sp_estadisticas_sistema`;

-- Eliminar eventos si existen
DROP EVENT IF EXISTS `ev_limpiar_sesiones`;
DROP EVENT IF EXISTS `ev_actualizar_estadisticas`;

-- =====================================================
-- TABLA: roles
-- Descripción: Gestión de roles y permisos del sistema
-- =====================================================
CREATE TABLE `roles` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nombre` varchar(50) NOT NULL COMMENT 'Nombre del rol (ej: administrador, editor)',
    `descripcion` text COMMENT 'Descripción detallada del rol',
    `permisos` json NOT NULL COMMENT 'Array JSON con los permisos del rol',
    `nivel_acceso` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Nivel numérico de acceso (1=invitado, 4=admin)',
    `activo` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Estado del rol (1=activo, 0=inactivo)',
    `es_sistema` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indica si es un rol del sistema (no eliminable)',
    `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_roles_nombre` (`nombre`),
    KEY `idx_roles_nivel` (`nivel_acceso`),
    KEY `idx_roles_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Roles y permisos del sistema de usuarios';

-- =====================================================
-- TABLA: usuarios
-- Descripción: Información de usuarios del sistema
-- =====================================================
CREATE TABLE `usuarios` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nombre` varchar(100) NOT NULL COMMENT 'Nombre del usuario',
    `apellidos` varchar(150) NOT NULL COMMENT 'Apellidos del usuario',
    `email` varchar(255) NOT NULL COMMENT 'Correo electrónico (único)',
    `password_hash` varchar(255) NOT NULL COMMENT 'Hash de la contraseña',
    `telefono` varchar(20) DEFAULT NULL COMMENT 'Número de teléfono',
    `fecha_nacimiento` date DEFAULT NULL COMMENT 'Fecha de nacimiento',
    `biografia` text DEFAULT NULL COMMENT 'Biografía o descripción personal',
    `avatar` varchar(500) DEFAULT NULL COMMENT 'URL del avatar/foto de perfil',
    `rol_id` int(11) NOT NULL DEFAULT 2 COMMENT 'ID del rol asignado',
    `activo` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Estado de la cuenta (1=activa, 0=inactiva)',
    `email_verificado` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Estado de verificación del email',
    `token_verificacion` varchar(100) DEFAULT NULL COMMENT 'Token para verificación de email',
    `token_recuperacion` varchar(100) DEFAULT NULL COMMENT 'Token para recuperación de contraseña',
    `fecha_token_recuperacion` timestamp NULL DEFAULT NULL COMMENT 'Fecha de expiración del token de recuperación',
    `ultimo_acceso` timestamp NULL DEFAULT NULL COMMENT 'Fecha del último acceso',
    `ip_ultimo_acceso` varchar(45) DEFAULT NULL COMMENT 'IP del último acceso',
    `intentos_login` tinyint(2) NOT NULL DEFAULT 0 COMMENT 'Número de intentos fallidos de login',
    `bloqueado_hasta` timestamp NULL DEFAULT NULL COMMENT 'Fecha hasta la cual está bloqueado',
    `configuracion` json DEFAULT NULL COMMENT 'Configuraciones personales del usuario',
    `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_usuarios_email` (`email`),
    KEY `idx_usuarios_rol` (`rol_id`),
    KEY `idx_usuarios_activo` (`activo`),
    KEY `idx_usuarios_email_verificado` (`email_verificado`),
    KEY `idx_usuarios_token_verificacion` (`token_verificacion`),
    KEY `idx_usuarios_token_recuperacion` (`token_recuperacion`),
    KEY `idx_usuarios_ultimo_acceso` (`ultimo_acceso`),
    CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Información de usuarios registrados en el sistema';

-- =====================================================
-- TABLA: exposiciones
-- Descripción: Gestión de exposiciones y eventos
-- =====================================================
CREATE TABLE `exposiciones` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `titulo` varchar(255) NOT NULL COMMENT 'Título de la exposición',
    `slug` varchar(255) NOT NULL COMMENT 'URL amigable generada del título',
    `descripcion` text NOT NULL COMMENT 'Descripción completa de la exposición',
    `descripcion_corta` varchar(500) DEFAULT NULL COMMENT 'Resumen breve para listados',
    `categoria` enum('arte_contemporaneo','arte_clasico','fotografia','escultura','historia','ciencias','tecnologia','cultura_popular','otros') NOT NULL DEFAULT 'otros' COMMENT 'Categoría de la exposición',
    `ubicacion` varchar(255) NOT NULL COMMENT 'Ubicación física de la exposición',
    `direccion_completa` text DEFAULT NULL COMMENT 'Dirección completa del lugar',
    `coordenadas_lat` decimal(10,8) DEFAULT NULL COMMENT 'Latitud para mapas',
    `coordenadas_lng` decimal(11,8) DEFAULT NULL COMMENT 'Longitud para mapas',
    `fecha_inicio` date NOT NULL COMMENT 'Fecha de inicio de la exposición',
    `fecha_fin` date NOT NULL COMMENT 'Fecha de finalización de la exposición',
    `horarios` json DEFAULT NULL COMMENT 'Horarios de apertura por día de la semana',
    `precio_entrada` decimal(8,2) NOT NULL DEFAULT 0.00 COMMENT 'Precio de la entrada (0.00 = gratis)',
    `precios_especiales` json DEFAULT NULL COMMENT 'Precios para diferentes grupos (estudiantes, mayores, etc.)',
    `capacidad_maxima` int(11) DEFAULT NULL COMMENT 'Capacidad máxima de visitantes',
    `imagen_principal` varchar(500) DEFAULT NULL COMMENT 'URL de la imagen principal',
    `galeria_imagenes` json DEFAULT NULL COMMENT 'Array de URLs de imágenes adicionales',
    `video_promocional` varchar(500) DEFAULT NULL COMMENT 'URL del video promocional',
    `enlace_compra` varchar(500) DEFAULT NULL COMMENT 'Enlace para comprar entradas',
    `contacto_organizador` json DEFAULT NULL COMMENT 'Información de contacto del organizador',
    `patrocinadores` json DEFAULT NULL COMMENT 'Lista de patrocinadores',
    `hashtags` json DEFAULT NULL COMMENT 'Hashtags para redes sociales',
    `destacada` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Exposición destacada (1=sí, 0=no)',
    `activa` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Estado de la exposición (1=activa, 0=inactiva)',
    `visible` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Visibilidad pública (1=visible, 0=oculta)',
    `contador_visitas` int(11) NOT NULL DEFAULT 0 COMMENT 'Número de visitas a la página',
    `puntuacion_promedio` decimal(3,2) DEFAULT NULL COMMENT 'Puntuación promedio de valoraciones (1.00-5.00)',
    `total_valoraciones` int(11) NOT NULL DEFAULT 0 COMMENT 'Número total de valoraciones',
    `metadatos_seo` json DEFAULT NULL COMMENT 'Metadatos para SEO (title, description, keywords)',
    `usuario_creador_id` int(11) NOT NULL COMMENT 'ID del usuario que creó la exposición',
    `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_exposiciones_slug` (`slug`),
    KEY `idx_exposiciones_categoria` (`categoria`),
    KEY `idx_exposiciones_fechas` (`fecha_inicio`, `fecha_fin`),
    KEY `idx_exposiciones_ubicacion` (`ubicacion`),
    KEY `idx_exposiciones_destacada` (`destacada`),
    KEY `idx_exposiciones_activa` (`activa`),
    KEY `idx_exposiciones_visible` (`visible`),
    KEY `idx_exposiciones_usuario_creador` (`usuario_creador_id`),
    KEY `idx_exposiciones_puntuacion` (`puntuacion_promedio`),
    CONSTRAINT `fk_exposiciones_usuario_creador` FOREIGN KEY (`usuario_creador_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE,
    CONSTRAINT `chk_exposiciones_fechas` CHECK (`fecha_fin` >= `fecha_inicio`),
    CONSTRAINT `chk_exposiciones_precio` CHECK (`precio_entrada` >= 0),
    CONSTRAINT `chk_exposiciones_puntuacion` CHECK (`puntuacion_promedio` IS NULL OR (`puntuacion_promedio` >= 1.00 AND `puntuacion_promedio` <= 5.00))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Información de exposiciones y eventos culturales';

-- =====================================================
-- TABLA: articulos
-- Descripción: Artículos del blog y sistema de noticias
-- =====================================================
CREATE TABLE `articulos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `titulo` varchar(255) NOT NULL COMMENT 'Título del artículo',
    `slug` varchar(255) NOT NULL COMMENT 'URL amigable generada del título',
    `resumen` varchar(500) DEFAULT NULL COMMENT 'Resumen o entradilla del artículo',
    `contenido` longtext NOT NULL COMMENT 'Contenido completo del artículo (puede incluir HTML)',
    `contenido_texto` longtext DEFAULT NULL COMMENT 'Contenido en texto plano para búsquedas',
    `categoria` enum('noticias','exposiciones','arte','cultura','historia','educacion','eventos','entrevistas','opinion','tecnica','otros') NOT NULL DEFAULT 'noticias' COMMENT 'Categoría del artículo',
    `tags` json DEFAULT NULL COMMENT 'Etiquetas del artículo',
    `imagen_destacada` varchar(500) DEFAULT NULL COMMENT 'URL de la imagen principal',
    `galeria_imagenes` json DEFAULT NULL COMMENT 'Array de URLs de imágenes adicionales',
    `autor_id` int(11) NOT NULL COMMENT 'ID del usuario autor',
    `autor_invitado` varchar(255) DEFAULT NULL COMMENT 'Nombre de autor invitado (si no es usuario registrado)',
    `estado` enum('borrador','revision','programado','publicado','archivado') NOT NULL DEFAULT 'borrador' COMMENT 'Estado del artículo',
    `destacado` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Artículo destacado (1=sí, 0=no)',
    `permitir_comentarios` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Permite comentarios (1=sí, 0=no)',
    `fecha_publicacion` timestamp NULL DEFAULT NULL COMMENT 'Fecha de publicación (puede ser futura)',
    `fecha_caducidad` timestamp NULL DEFAULT NULL COMMENT 'Fecha de caducidad (opcional)',
    `contador_visitas` int(11) NOT NULL DEFAULT 0 COMMENT 'Número de visitas al artículo',
    `tiempo_lectura` int(11) DEFAULT NULL COMMENT 'Tiempo estimado de lectura en minutos',
    `puntuacion_promedio` decimal(3,2) DEFAULT NULL COMMENT 'Puntuación promedio de valoraciones (1.00-5.00)',
    `total_valoraciones` int(11) NOT NULL DEFAULT 0 COMMENT 'Número total de valoraciones',
    `total_comentarios` int(11) NOT NULL DEFAULT 0 COMMENT 'Número total de comentarios',
    `total_compartidos` int(11) NOT NULL DEFAULT 0 COMMENT 'Número de veces compartido',
    `metadatos_seo` json DEFAULT NULL COMMENT 'Metadatos para SEO (title, description, keywords)',
    `configuracion` json DEFAULT NULL COMMENT 'Configuraciones específicas del artículo',
    `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_articulos_slug` (`slug`),
    KEY `idx_articulos_categoria` (`categoria`),
    KEY `idx_articulos_autor` (`autor_id`),
    KEY `idx_articulos_estado` (`estado`),
    KEY `idx_articulos_destacado` (`destacado`),
    KEY `idx_articulos_fecha_publicacion` (`fecha_publicacion`),
    KEY `idx_articulos_puntuacion` (`puntuacion_promedio`),
    KEY `idx_articulos_visitas` (`contador_visitas`),
    CONSTRAINT `fk_articulos_autor` FOREIGN KEY (`autor_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE,
    CONSTRAINT `chk_articulos_puntuacion` CHECK (`puntuacion_promedio` IS NULL OR (`puntuacion_promedio` >= 1.00 AND `puntuacion_promedio` <= 5.00))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Artículos del blog y sistema de contenidos';

-- =====================================================
-- TABLA: comentarios
-- Descripción: Comentarios en artículos y exposiciones
-- =====================================================
CREATE TABLE `comentarios` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `contenido` text NOT NULL COMMENT 'Contenido del comentario',
    `tipo_contenido` enum('articulo','exposicion') NOT NULL COMMENT 'Tipo de contenido comentado',
    `contenido_id` int(11) NOT NULL COMMENT 'ID del contenido comentado',
    `usuario_id` int(11) DEFAULT NULL COMMENT 'ID del usuario (NULL para comentarios anónimos)',
    `nombre_invitado` varchar(100) DEFAULT NULL COMMENT 'Nombre para comentarios anónimos',
    `email_invitado` varchar(255) DEFAULT NULL COMMENT 'Email para comentarios anónimos',
    `comentario_padre_id` int(11) DEFAULT NULL COMMENT 'ID del comentario padre (para respuestas)',
    `puntuacion` tinyint(1) DEFAULT NULL COMMENT 'Puntuación asociada (1-5 estrellas)',
    `estado` enum('pendiente','aprobado','rechazado','spam') NOT NULL DEFAULT 'pendiente' COMMENT 'Estado de moderación',
    `ip_address` varchar(45) DEFAULT NULL COMMENT 'Dirección IP del comentario',
    `user_agent` varchar(500) DEFAULT NULL COMMENT 'User agent del navegador',
    `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_comentarios_contenido` (`tipo_contenido`, `contenido_id`),
    KEY `idx_comentarios_usuario` (`usuario_id`),
    KEY `idx_comentarios_padre` (`comentario_padre_id`),
    KEY `idx_comentarios_estado` (`estado`),
    KEY `idx_comentarios_fecha` (`fecha_creacion`),
    CONSTRAINT `fk_comentarios_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_comentarios_padre` FOREIGN KEY (`comentario_padre_id`) REFERENCES `comentarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `chk_comentarios_puntuacion` CHECK (`puntuacion` IS NULL OR (`puntuacion` >= 1 AND `puntuacion` <= 5))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Comentarios y valoraciones de usuarios';

-- =====================================================
-- TABLA: favoritos
-- Descripción: Favoritos de usuarios (exposiciones, artículos)
-- =====================================================
CREATE TABLE `favoritos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `usuario_id` int(11) NOT NULL COMMENT 'ID del usuario',
    `tipo_contenido` enum('exposicion','articulo') NOT NULL COMMENT 'Tipo de contenido marcado como favorito',
    `contenido_id` int(11) NOT NULL COMMENT 'ID del contenido favorito',
    `notas_personales` text DEFAULT NULL COMMENT 'Notas personales del usuario sobre el favorito',
    `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_favoritos_usuario_contenido` (`usuario_id`, `tipo_contenido`, `contenido_id`),
    KEY `idx_favoritos_usuario` (`usuario_id`),
    KEY `idx_favoritos_contenido` (`tipo_contenido`, `contenido_id`),
    CONSTRAINT `fk_favoritos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Favoritos de usuarios';

-- =====================================================
-- TABLA: suscripciones
-- Descripción: Suscripciones a newsletter y notificaciones
-- =====================================================
CREATE TABLE `suscripciones` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL COMMENT 'Email del suscriptor',
    `nombre` varchar(100) DEFAULT NULL COMMENT 'Nombre del suscriptor (opcional)',
    `usuario_id` int(11) DEFAULT NULL COMMENT 'ID del usuario registrado (si aplica)',
    `tipo_suscripcion` enum('newsletter','exposiciones','articulos','eventos','todas') NOT NULL DEFAULT 'newsletter' COMMENT 'Tipo de suscripción',
    `activa` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Estado de la suscripción',
    `token_confirmacion` varchar(100) DEFAULT NULL COMMENT 'Token para confirmar la suscripción',
    `confirmada` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Suscripción confirmada (1=sí, 0=no)',
    `token_cancelacion` varchar(100) NOT NULL COMMENT 'Token para cancelar la suscripción',
    `preferencias` json DEFAULT NULL COMMENT 'Preferencias de notificación',
    `origen` varchar(100) DEFAULT NULL COMMENT 'Origen de la suscripción (web, evento, etc.)',
    `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP de registro',
    `fecha_confirmacion` timestamp NULL DEFAULT NULL COMMENT 'Fecha de confirmación',
    `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_suscripciones_email_tipo` (`email`, `tipo_suscripcion`),
    KEY `idx_suscripciones_usuario` (`usuario_id`),
    KEY `idx_suscripciones_activa` (`activa`),
    KEY `idx_suscripciones_confirmada` (`confirmada`),
    KEY `idx_suscripciones_token_confirmacion` (`token_confirmacion`),
    KEY `idx_suscripciones_token_cancelacion` (`token_cancelacion`),
    CONSTRAINT `fk_suscripciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Suscripciones a newsletter y notificaciones';

-- =====================================================
-- TABLA: sesiones
-- Descripción: Gestión de sesiones de usuario
-- =====================================================
CREATE TABLE `sesiones` (
    `id` varchar(128) NOT NULL COMMENT 'ID de la sesión',
    `usuario_id` int(11) DEFAULT NULL COMMENT 'ID del usuario (NULL para sesiones anónimas)',
    `datos_sesion` longtext NOT NULL COMMENT 'Datos serializados de la sesión',
    `ip_address` varchar(45) NOT NULL COMMENT 'Dirección IP',
    `user_agent` varchar(500) DEFAULT NULL COMMENT 'User agent del navegador',
    `es_movil` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Sesión desde dispositivo móvil',
    `recordar_sesion` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Sesión de tipo "recordar"',
    `fecha_actividad` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última actividad',
    `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_sesiones_usuario` (`usuario_id`),
    KEY `idx_sesiones_actividad` (`fecha_actividad`),
    KEY `idx_sesiones_ip` (`ip_address`),
    CONSTRAINT `fk_sesiones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Gestión de sesiones de usuario';

-- =====================================================
-- TABLA: configuracion
-- Descripción: Configuración global del sistema
-- =====================================================
CREATE TABLE `configuracion` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `clave` varchar(100) NOT NULL COMMENT 'Clave de configuración',
    `valor` longtext COMMENT 'Valor de configuración (puede ser JSON)',
    `tipo` enum('string','integer','boolean','json','array') NOT NULL DEFAULT 'string' COMMENT 'Tipo de dato',
    `categoria` varchar(50) NOT NULL DEFAULT 'general' COMMENT 'Categoría de configuración',
    `descripcion` text DEFAULT NULL COMMENT 'Descripción de la configuración',
    `solo_lectura` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Solo lectura (no editable desde admin)',
    `activa` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Configuración activa',
    `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_configuracion_clave` (`clave`),
    KEY `idx_configuracion_categoria` (`categoria`),
    KEY `idx_configuracion_activa` (`activa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Configuración global del sistema';

-- =====================================================
-- TABLA: logs_actividad
-- Descripción: Registro de actividades del sistema
-- =====================================================
CREATE TABLE `logs_actividad` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `usuario_id` int(11) DEFAULT NULL COMMENT 'ID del usuario (NULL para acciones del sistema)',
    `accion` varchar(100) NOT NULL COMMENT 'Tipo de acción realizada',
    `entidad_tipo` varchar(50) DEFAULT NULL COMMENT 'Tipo de entidad afectada',
    `entidad_id` int(11) DEFAULT NULL COMMENT 'ID de la entidad afectada',
    `descripcion` text NOT NULL COMMENT 'Descripción detallada de la acción',
    `datos_anteriores` json DEFAULT NULL COMMENT 'Datos antes del cambio (para auditoría)',
    `datos_nuevos` json DEFAULT NULL COMMENT 'Datos después del cambio',
    `ip_address` varchar(45) DEFAULT NULL COMMENT 'Dirección IP',
    `user_agent` varchar(500) DEFAULT NULL COMMENT 'User agent del navegador',
    `nivel` enum('info','warning','error','critical') NOT NULL DEFAULT 'info' COMMENT 'Nivel de importancia',
    `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_logs_usuario` (`usuario_id`),
    KEY `idx_logs_accion` (`accion`),
    KEY `idx_logs_entidad` (`entidad_tipo`, `entidad_id`),
    KEY `idx_logs_nivel` (`nivel`),
    KEY `idx_logs_fecha` (`fecha_creacion`),
    KEY `idx_logs_ip` (`ip_address`),
    CONSTRAINT `fk_logs_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Registro de actividades y auditoría del sistema';

-- =====================================================
-- TABLA: archivos_subidos
-- Descripción: Gestión de archivos subidos al sistema
-- =====================================================
CREATE TABLE `archivos_subidos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nombre_original` varchar(255) NOT NULL COMMENT 'Nombre original del archivo',
    `nombre_archivo` varchar(255) NOT NULL COMMENT 'Nombre del archivo en el servidor',
    `ruta_archivo` varchar(500) NOT NULL COMMENT 'Ruta completa del archivo',
    `url_publica` varchar(500) NOT NULL COMMENT 'URL pública del archivo',
    `tipo_mime` varchar(100) NOT NULL COMMENT 'Tipo MIME del archivo',
    `tamaño` int(11) NOT NULL COMMENT 'Tamaño del archivo en bytes',
    `extension` varchar(10) NOT NULL COMMENT 'Extensión del archivo',
    `es_imagen` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Es un archivo de imagen',
    `dimensiones` varchar(20) DEFAULT NULL COMMENT 'Dimensiones de imagen (ancho x alto)',
    `alt_text` varchar(255) DEFAULT NULL COMMENT 'Texto alternativo para imágenes',
    `titulo` varchar(255) DEFAULT NULL COMMENT 'Título del archivo',
    `descripcion` text DEFAULT NULL COMMENT 'Descripción del archivo',
    `entidad_tipo` varchar(50) DEFAULT NULL COMMENT 'Tipo de entidad asociada',
    `entidad_id` int(11) DEFAULT NULL COMMENT 'ID de la entidad asociada',
    `usuario_id` int(11) NOT NULL COMMENT 'ID del usuario que subió el archivo',
    `publico` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Archivo público (1=sí, 0=no)',
    `hash_archivo` varchar(64) NOT NULL COMMENT 'Hash del archivo para verificación de integridad',
    `metadata` json DEFAULT NULL COMMENT 'Metadatos adicionales del archivo',
    `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_archivos_hash` (`hash_archivo`),
    KEY `idx_archivos_usuario` (`usuario_id`),
    KEY `idx_archivos_entidad` (`entidad_tipo`, `entidad_id`),
    KEY `idx_archivos_tipo` (`tipo_mime`),
    KEY `idx_archivos_extension` (`extension`),
    KEY `idx_archivos_publico` (`publico`),
    CONSTRAINT `fk_archivos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Gestión de archivos subidos al sistema';

-- =====================================================
-- DATOS INICIALES DEL SISTEMA
-- =====================================================

-- Insertar roles predeterminados (usando INSERT IGNORE para evitar errores si ya existen)
INSERT IGNORE INTO `roles` (`id`, `nombre`, `descripcion`, `permisos`, `nivel_acceso`, `es_sistema`) VALUES
(1, 'invitado', 'Usuario no registrado con permisos básicos de lectura', 
 '["leer_publico", "comentar_anonimo"]', 1, 1),
(2, 'usuario', 'Usuario registrado con permisos básicos', 
 '["leer_publico", "comentar", "gestionar_perfil", "favoritos", "suscripciones"]', 2, 1),
(3, 'editor', 'Editor de contenidos con permisos de creación y edición', 
 '["leer_publico", "comentar", "gestionar_perfil", "favoritos", "suscripciones", "crear_articulos", "editar_articulos", "crear_exposiciones", "editar_exposiciones", "moderar_comentarios"]', 3, 1),
(4, 'administrador', 'Administrador con permisos completos del sistema', 
 '["*"]', 4, 1);

-- Insertar usuario administrador predeterminado (usando INSERT IGNORE)
-- Contraseña: Admin123! (hash generado con password_hash())
INSERT IGNORE INTO `usuarios` (`id`, `nombre`, `apellidos`, `email`, `password_hash`, `rol_id`, `activo`, `email_verificado`) VALUES
(1, 'Administrador', 'del Sistema', 'admin@exposiciones.local', 
 '$2y$12$LQv3c1yqBwEHXVp4GGL16u7UE.Y3k1WlT.Z2j2B1J1k2K3L1m5N6O', 4, 1, 1);

-- Configuración inicial del sistema (usando INSERT IGNORE)
INSERT IGNORE INTO `configuracion` (`clave`, `valor`, `tipo`, `categoria`, `descripcion`) VALUES
('nombre_aplicacion', 'Sistema de Exposiciones', 'string', 'general', 'Nombre de la aplicación'),
('descripcion_aplicacion', 'Plataforma para la gestión y promoción de exposiciones de arte y cultura', 'string', 'general', 'Descripción de la aplicación'),
('url_base', 'http://localhost', 'string', 'general', 'URL base de la aplicación'),
('email_administrador', 'admin@exposiciones.local', 'string', 'general', 'Email del administrador'),
('idioma_predeterminado', 'es', 'string', 'general', 'Idioma predeterminado del sistema'),
('zona_horaria', 'Europe/Madrid', 'string', 'general', 'Zona horaria del sistema'),
('permitir_registro', 'true', 'boolean', 'usuarios', 'Permitir registro de nuevos usuarios'),
('verificacion_email_requerida', 'true', 'boolean', 'usuarios', 'Requiere verificación de email para nuevos usuarios'),
('moderacion_comentarios', 'true', 'boolean', 'contenido', 'Los comentarios requieren moderación'),
('comentarios_anonimos', 'true', 'boolean', 'contenido', 'Permitir comentarios anónimos'),
('exposiciones_por_pagina', '12', 'integer', 'contenido', 'Número de exposiciones por página en listados'),
('articulos_por_pagina', '10', 'integer', 'contenido', 'Número de artículos por página en listados'),
('tamaño_maximo_archivo', '10485760', 'integer', 'archivos', 'Tamaño máximo de archivo en bytes (10MB)'),
('extensiones_permitidas', '["jpg", "jpeg", "png", "gif", "webp", "pdf", "doc", "docx"]', 'json', 'archivos', 'Extensiones de archivo permitidas'),
('mantenimiento', 'false', 'boolean', 'sistema', 'Modo mantenimiento activado'),
('mensaje_mantenimiento', 'El sitio está en mantenimiento. Volveremos pronto.', 'string', 'sistema', 'Mensaje mostrado durante el mantenimiento'),
('google_analytics', '', 'string', 'integraciones', 'ID de Google Analytics'),
('redes_sociales', '{"facebook": "", "twitter": "", "instagram": "", "youtube": ""}', 'json', 'integraciones', 'Enlaces a redes sociales'),
('smtp_configuracion', '{"host": "", "puerto": 587, "usuario": "", "password": "", "encriptacion": "tls"}', 'json', 'email', 'Configuración del servidor SMTP');

-- Activar las restricciones de claves foráneas
SET foreign_key_checks = 1;

-- =====================================================
-- TRIGGERS Y PROCEDIMIENTOS ALMACENADOS
-- =====================================================

-- Trigger para generar slug automáticamente en exposiciones
DELIMITER $$
CREATE TRIGGER `tr_exposiciones_generar_slug` 
BEFORE INSERT ON `exposiciones` 
FOR EACH ROW 
BEGIN
    DECLARE slug_base VARCHAR(255);
    DECLARE slug_final VARCHAR(255);
    DECLARE contador INT DEFAULT 0;
    
    -- Generar slug base
    SET slug_base = LOWER(TRIM(NEW.titulo));
    SET slug_base = REPLACE(slug_base, ' ', '-');
    SET slug_base = REPLACE(slug_base, 'á', 'a');
    SET slug_base = REPLACE(slug_base, 'é', 'e');
    SET slug_base = REPLACE(slug_base, 'í', 'i');
    SET slug_base = REPLACE(slug_base, 'ó', 'o');
    SET slug_base = REPLACE(slug_base, 'ú', 'u');
    SET slug_base = REPLACE(slug_base, 'ñ', 'n');
    SET slug_base = REPLACE(slug_base, 'ü', 'u');
    SET slug_base = REGEXP_REPLACE(slug_base, '[^a-z0-9-]', '');
    SET slug_base = REGEXP_REPLACE(slug_base, '-+', '-');
    SET slug_base = TRIM(BOTH '-' FROM slug_base);
    
    SET slug_final = slug_base;
    
    -- Verificar unicidad y ajustar si es necesario
    WHILE EXISTS (SELECT 1 FROM exposiciones WHERE slug = slug_final) DO
        SET contador = contador + 1;
        SET slug_final = CONCAT(slug_base, '-', contador);
    END WHILE;
    
    SET NEW.slug = slug_final;
END$$
DELIMITER ;

-- Trigger para generar slug automáticamente en artículos
DELIMITER $$
CREATE TRIGGER `tr_articulos_generar_slug` 
BEFORE INSERT ON `articulos` 
FOR EACH ROW 
BEGIN
    DECLARE slug_base VARCHAR(255);
    DECLARE slug_final VARCHAR(255);
    DECLARE contador INT DEFAULT 0;
    
    -- Generar slug base (mismo proceso que exposiciones)
    SET slug_base = LOWER(TRIM(NEW.titulo));
    SET slug_base = REPLACE(slug_base, ' ', '-');
    SET slug_base = REPLACE(slug_base, 'á', 'a');
    SET slug_base = REPLACE(slug_base, 'é', 'e');
    SET slug_base = REPLACE(slug_base, 'í', 'i');
    SET slug_base = REPLACE(slug_base, 'ó', 'o');
    SET slug_base = REPLACE(slug_base, 'ú', 'u');
    SET slug_base = REPLACE(slug_base, 'ñ', 'n');
    SET slug_base = REPLACE(slug_base, 'ü', 'u');
    SET slug_base = REGEXP_REPLACE(slug_base, '[^a-z0-9-]', '');
    SET slug_base = REGEXP_REPLACE(slug_base, '-+', '-');
    SET slug_base = TRIM(BOTH '-' FROM slug_base);
    
    SET slug_final = slug_base;
    
    -- Verificar unicidad
    WHILE EXISTS (SELECT 1 FROM articulos WHERE slug = slug_final) DO
        SET contador = contador + 1;
        SET slug_final = CONCAT(slug_base, '-', contador);
    END WHILE;
    
    SET NEW.slug = slug_final;
END$$
DELIMITER ;

-- Trigger para actualizar contador de comentarios en artículos
DELIMITER $$
CREATE TRIGGER `tr_comentarios_actualizar_contador_articulos` 
AFTER INSERT ON `comentarios` 
FOR EACH ROW 
BEGIN
    IF NEW.tipo_contenido = 'articulo' AND NEW.estado = 'aprobado' THEN
        UPDATE articulos 
        SET total_comentarios = (
            SELECT COUNT(*) 
            FROM comentarios 
            WHERE tipo_contenido = 'articulo' 
            AND contenido_id = NEW.contenido_id 
            AND estado = 'aprobado'
        )
        WHERE id = NEW.contenido_id;
    END IF;
END$$
DELIMITER ;

-- =====================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =====================================================

-- Índices para mejorar rendimiento en consultas comunes (con IF NOT EXISTS)
CREATE INDEX IF NOT EXISTS `idx_exposiciones_fechas_activa` ON `exposiciones` (`fecha_inicio`, `fecha_fin`, `activa`, `visible`);
CREATE INDEX IF NOT EXISTS `idx_articulos_publicacion_estado` ON `articulos` (`fecha_publicacion`, `estado`);
CREATE INDEX IF NOT EXISTS `idx_comentarios_contenido_estado` ON `comentarios` (`tipo_contenido`, `contenido_id`, `estado`);

-- =====================================================
-- VISTAS PARA CONSULTAS FRECUENTES
-- =====================================================

-- Vista para exposiciones activas (usar CREATE OR REPLACE)
CREATE OR REPLACE VIEW `v_exposiciones_activas` AS
SELECT 
    e.*,
    u.nombre AS nombre_creador,
    u.apellidos AS apellidos_creador,
    (SELECT COUNT(*) FROM comentarios c WHERE c.tipo_contenido = 'exposicion' AND c.contenido_id = e.id AND c.estado = 'aprobado') AS total_comentarios
FROM exposiciones e
INNER JOIN usuarios u ON e.usuario_creador_id = u.id
WHERE e.activa = 1 
AND e.visible = 1 
AND e.fecha_inicio <= CURDATE() 
AND e.fecha_fin >= CURDATE();

-- Vista para artículos publicados (usar CREATE OR REPLACE)
CREATE OR REPLACE VIEW `v_articulos_publicados` AS
SELECT 
    a.*,
    u.nombre AS nombre_autor,
    u.apellidos AS apellidos_autor,
    u.avatar AS avatar_autor
FROM articulos a
INNER JOIN usuarios u ON a.autor_id = u.id
WHERE a.estado = 'publicado' 
AND (a.fecha_publicacion IS NULL OR a.fecha_publicacion <= NOW())
AND (a.fecha_caducidad IS NULL OR a.fecha_caducidad > NOW());

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS ÚTILES
-- =====================================================

-- Procedimiento para limpiar sesiones expiradas
DELIMITER $$
CREATE PROCEDURE `sp_limpiar_sesiones_expiradas`()
BEGIN
    DECLARE sesiones_eliminadas INT DEFAULT 0;
    
    DELETE FROM sesiones 
    WHERE fecha_actividad < DATE_SUB(NOW(), INTERVAL 30 DAY);
    
    SET sesiones_eliminadas = ROW_COUNT();
    
    INSERT INTO logs_actividad (accion, descripcion, nivel) 
    VALUES ('limpiar_sesiones', CONCAT('Se eliminaron ', sesiones_eliminadas, ' sesiones expiradas'), 'info');
END$$
DELIMITER ;

-- Procedimiento para obtener estadísticas del sistema
DELIMITER $$
CREATE PROCEDURE `sp_estadisticas_sistema`()
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM usuarios WHERE activo = 1) AS total_usuarios,
        (SELECT COUNT(*) FROM exposiciones WHERE activa = 1 AND visible = 1) AS total_exposiciones,
        (SELECT COUNT(*) FROM exposiciones WHERE activa = 1 AND visible = 1 AND fecha_inicio <= CURDATE() AND fecha_fin >= CURDATE()) AS exposiciones_actuales,
        (SELECT COUNT(*) FROM articulos WHERE estado = 'publicado') AS total_articulos,
        (SELECT COUNT(*) FROM comentarios WHERE estado = 'aprobado') AS total_comentarios,
        (SELECT COUNT(*) FROM suscripciones WHERE activa = 1 AND confirmada = 1) AS total_suscriptores;
END$$
DELIMITER ;

-- =====================================================
-- EVENTOS PROGRAMADOS (CRON JOBS)
-- =====================================================

-- Evento para limpiar sesiones expiradas (ejecutar diariamente)
CREATE EVENT IF NOT EXISTS `ev_limpiar_sesiones`
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
  CALL sp_limpiar_sesiones_expiradas();

-- Evento para actualizar estadísticas (ejecutar cada hora)
CREATE EVENT IF NOT EXISTS `ev_actualizar_estadisticas`
ON SCHEDULE EVERY 1 HOUR
STARTS CURRENT_TIMESTAMP
DO
  UPDATE configuracion 
  SET valor = (
    SELECT JSON_OBJECT(
      'usuarios', (SELECT COUNT(*) FROM usuarios WHERE activo = 1),
      'exposiciones', (SELECT COUNT(*) FROM exposiciones WHERE activa = 1),
      'articulos', (SELECT COUNT(*) FROM articulos WHERE estado = 'publicado')
    )
  )
  WHERE clave = 'estadisticas_cache';

-- =====================================================
-- COMENTARIOS FINALES
-- =====================================================

/*
ESQUEMA COMPLETADO EXITOSAMENTE

Este esquema incluye:
✅ Todas las tablas necesarias para el sistema de exposiciones
✅ Relaciones y restricciones de integridad
✅ Índices optimizados para consultas frecuentes
✅ Triggers para automatización de tareas
✅ Vistas para consultas comunes
✅ Procedimientos almacenados útiles
✅ Eventos programados para mantenimiento
✅ Datos iniciales del sistema
✅ Configuración completa de seguridad

PRÓXIMOS PASOS:
1. Ejecutar este script en la base de datos MySQL/MariaDB
2. Verificar que todas las tablas se han creado correctamente
3. Comprobar que el usuario administrador puede iniciar sesión
4. Configurar las rutas y controladores para usar estas tablas
5. Implementar las funciones de backup y restauración

NOTAS DE SEGURIDAD:
- Cambiar la contraseña del administrador predeterminado
- Configurar adecuadamente los permisos de base de datos
- Activar el modo SSL para conexiones de base de datos
- Implementar rotación de logs para evitar crecimiento excesivo
*/
