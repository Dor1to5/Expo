# CARPETA DEBUG - DOCUMENTACIÓN

## 📋 PROPÓSITO
Esta carpeta contiene todos los archivos de diagnóstico, testing y debug del sistema de gestión de exposiciones y artículos. 
Estos archivos fueron creados durante el desarrollo para identificar y solucionar problemas técnicos específicos.

## 📁 CONTENIDO DE LA CARPETA

### 🔍 ARCHIVOS DE DIAGNÓSTICO WEB
- **`sistema_web_completo.php`** - Test integral del sistema web completo
  - Verifica carga de archivos de configuración
  - Prueba el autoloader y carga de clases
  - Testa conexión a base de datos
  - Ejecuta controladores y métodos

- **`analisis_rutas_404.php`** - Diagnóstico específico para errores 404
  - Analiza problemas de rutas no encontradas
  - Verifica configuración de Apache y .htaccess
  - Testa resolución de URLs y parámetros GET
  - Incluye tests de ejecución directa de controladores

- **`verificador_sistema_rutas.php`** - Test completo del sistema de enrutado
  - Verifica funcionamiento del sistema de rutas
  - Prueba resolución de rutas públicas y administrativas
  - Incluye menú interactivo para testing

### 🧪 TESTS DE MODELOS Y BASE DE DATOS
- **`test_modelo_articulo.php`** - Test específico del modelo Articulo
  - Prueba creación de artículos
  - Verifica campos obligatorios y opcionales
  - Testa generación automática de slugs

- **`test_modelo_exposicion.php`** - Test específico del modelo Exposicion
  - Prueba creación de exposiciones
  - Verifica campos de fechas y precios
  - Testa categorías y ubicaciones

- **`verificacion_conexion_bd.php`** - Test básico de conexión a BD
  - Verifica configuración de base de datos
  - Prueba conexión PDO
  - Lista tablas disponibles

### 🎮 TESTS DE CONTROLADORES
- **`prueba_controladores_base.php`** - Test de instanciación de controladores
  - Verifica que los controladores se pueden crear
  - Prueba métodos básicos
  - Incluye tests de AdminUsuarioControlador

- **`diagnostico_exposiciones_articulos.php`** - Diagnóstico completo de CRUD
  - Test específico para exposiciones y artículos
  - Verifica controladores, modelos y vistas
  - Incluye pruebas de resolución de rutas
  - Testa existencia de archivos críticos

### ⚙️ TESTS DE CONFIGURACIÓN
- **`configuracion_php_servidor.php`** - Información del servidor PHP
  - Muestra phpinfo() completo
  - Verifica extensiones disponibles
  - Información de configuración PHP

- **`configuracion_sistema_inicial.php`** - Test de configuración inicial
  - Primer diagnóstico del sistema
  - Verifica estructura de directorios
  - Prueba configuraciones básicas

## 🚀 CÓMO USAR ESTOS ARCHIVOS

### Para desarrollo local:
```bash
# Ejecutar desde línea de comandos
php arch/debug/test_modelo_articulo.php

# O copiar al directorio web
cp arch/debug/sistema_web_completo.php /xampp/htdocs/proyecto/debug.php
```

### Para debugging web:
1. Copiar el archivo deseado al directorio `publico/`
2. Acceder via navegador: `http://localhost/proyecto/publico/debug.php`
3. Usar los menús interactivos para tests específicos

## 🔧 PROBLEMAS RESUELTOS

### Problema de Rutas 404
- **Causa**: Apache no procesaba rutas limpias correctamente
- **Solución**: Implementación de sistema dual (rutas limpias + parámetros GET)
- **Archivo**: `analisis_rutas_404.php`

### Problema de Constantes BD
- **Causa**: Constantes de BD no accesibles desde namespace `Utilidades`
- **Solución**: Prefijo `\` para acceso a constantes globales
- **Archivo**: `test_modelo_exposicion.php`

### Problema de Autoloader
- **Causa**: Clases no se cargaban correctamente
- **Solución**: Configuración de `spl_autoload_register` mejorada
- **Archivo**: `sistema_web_completo.php`

## 📊 ESTADÍSTICAS DE DEBUG
- **Archivos de debug creados**: 9
- **Problemas identificados y resueltos**: 8
- **Tests de funcionalidad**: 15+
- **Controladores verificados**: 9
- **Modelos testeados**: 3

## ⚠️ NOTAS IMPORTANTES
- Estos archivos contienen información sensible (configuración BD)
- NO incluir en producción
- Usar solo en entorno de desarrollo
- Algunos tests requieren datos de ejemplo en BD

## 🎯 PROPÓSITO FUTURO
Esta documentación y archivos servirán para:
- Debugging de problemas similares
- Onboarding de nuevos desarrolladores
- Testing de regresiones
- Documentación de arquitectura del sistema

---
**Creado**: 2 de agosto de 2025
**Autor**: Sistema de Gestión - ElNacho
**Versión**: 1.0
