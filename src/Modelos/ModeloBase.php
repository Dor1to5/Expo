<?php
/**
 * Clase base abstracta para todos los modelos del sistema
 * 
 * Esta clase proporciona funcionalidades comunes para todos los modelos,
 * implementando operaciones CRUD básicas y métodos de utilidad.
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

namespace Modelos;

use Utilidades\BaseDatos;
use Exception;

/**
 * Clase abstracta ModeloBase - Base para todos los modelos
 */
abstract class ModeloBase {
    
    /**
     * Instancia de la base de datos
     * @var BaseDatos
     */
    protected BaseDatos $bd;
    
    /**
     * Nombre de la tabla en la base de datos
     * @var string
     */
    protected string $tabla;
    
    /**
     * Nombre de la clave primaria
     * @var string
     */
    protected string $clavePrimaria = 'id';
    
    /**
     * Campos que se pueden llenar masivamente
     * @var array
     */
    protected array $camposLlenables = [];
    
    /**
     * Campos que están ocultos en las respuestas
     * @var array
     */
    protected array $camposOcultos = [];
    
    /**
     * Reglas de validación para los campos
     * @var array
     */
    protected array $reglasValidacion = [];
    
    /**
     * Constructor base con conexión a BD
     * @param BaseDatos|null $baseDatos Instancia de base de datos (opcional)
     */
    public function __construct(?BaseDatos $baseDatos = null) {
        $this->bd = $baseDatos ?? BaseDatos::obtenerInstancia();
    }
    
    /**
     * Obtiene todos los registros con paginación opcional
     * @param int $limite Número máximo de registros a devolver
     * @param int $offset Número de registros a saltar
     * @param array $condiciones Condiciones WHERE adicionales
     * @param string $ordenPor Campo para ordenar los resultados
     * @param string $direccionOrden Dirección del orden (ASC/DESC)
     * @return array Array de registros
     * @throws Exception Si hay error en la consulta
     */
    public function obtenerTodos(
        int $limite = 0,
        int $offset = 0,
        array $condiciones = [],
        string $ordenPor = '',
        string $direccionOrden = 'ASC'
    ): array {
        $sql = "SELECT * FROM {$this->tabla}";
        $parametros = [];
        
        // Agregar condiciones WHERE si existen
        if (!empty($condiciones)) {
            $clausulasWhere = [];
            foreach ($condiciones as $campo => $valor) {
                $clausulasWhere[] = "{$campo} = :{$campo}";
                $parametros[$campo] = $valor;
            }
            $sql .= " WHERE " . implode(' AND ', $clausulasWhere);
        }
        
        // Agregar ORDER BY si se especifica
        if (!empty($ordenPor)) {
            $direccionOrden = strtoupper($direccionOrden) === 'DESC' ? 'DESC' : 'ASC';
            $sql .= " ORDER BY {$ordenPor} {$direccionOrden}";
        }
        
        // Agregar LIMIT y OFFSET si se especifican
        if ($limite > 0) {
            $sql .= " LIMIT {$limite}";
            if ($offset > 0) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        return $this->bd->seleccionar($sql, $parametros);
    }
    
    /**
     * Obtiene un registro por su ID
     * @param int $id ID del registro a buscar
     * @return array|null Registro encontrado o null si no existe
     * @throws Exception Si hay error en la consulta
     */
    public function obtenerPorId(int $id): ?array {
        $sql = "SELECT * FROM {$this->tabla} WHERE {$this->clavePrimaria} = :id LIMIT 1";
        return $this->bd->seleccionarUno($sql, ['id' => $id]);
    }
    
    /**
     * Obtiene registros que coincidan con criterios específicos
     * @param array $criterios Criterios de búsqueda
     * @param int $limite Límite de resultados
     * @return array Array de registros que coinciden
     * @throws Exception Si hay error en la consulta
     */
    public function obtenerPor(array $criterios, int $limite = 0): array {
        return $this->obtenerTodos($limite, 0, $criterios);
    }
    
    /**
     * Obtiene un solo registro que coincida con criterios específicos
     * @param array $criterios Criterios de búsqueda
     * @return array|null Primer registro que coincide o null
     * @throws Exception Si hay error en la consulta
     */
    public function obtenerUnoPor(array $criterios): ?array {
        $resultados = $this->obtenerPor($criterios, 1);
        return !empty($resultados) ? $resultados[0] : null;
    }
    
    /**
     * Crea un nuevo registro en la base de datos
     * @param array $datos Datos del nuevo registro
     * @return int ID del registro creado
     * @throws Exception Si hay error en la validación o inserción
     */
    public function crear(array $datos): int {
        // Validar datos antes de insertar
        $this->validarDatos($datos);
        
        // Filtrar solo campos llenables
        $datosFiltrados = $this->filtrarCamposLlenables($datos);
        
        if (empty($datosFiltrados)) {
            throw new Exception("No hay datos válidos para insertar");
        }
        
        // Construir consulta SQL
        $campos = array_keys($datosFiltrados);
        $placeholders = array_map(fn($campo) => ":{$campo}", $campos);
        
        $sql = "INSERT INTO {$this->tabla} (" . implode(', ', $campos) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        return $this->bd->insertar($sql, $datosFiltrados);
    }
    
    /**
     * Actualiza un registro existente
     * @param int $id ID del registro a actualizar
     * @param array $datos Nuevos datos del registro
     * @return bool True si se actualizó correctamente
     * @throws Exception Si hay error en la validación o actualización
     */
    public function actualizar(int $id, array $datos): bool {
        // Verificar que el registro existe
        if (!$this->existe($id)) {
            throw new Exception("El registro con ID {$id} no existe");
        }
        
        // Validar datos antes de actualizar
        $this->validarDatos($datos, $id);
        
        // Filtrar solo campos llenables
        $datosFiltrados = $this->filtrarCamposLlenables($datos);
        
        if (empty($datosFiltrados)) {
            throw new Exception("No hay datos válidos para actualizar");
        }
        
        // Construir consulta SQL
        $setClausulas = array_map(fn($campo) => "{$campo} = :{$campo}", array_keys($datosFiltrados));
        $sql = "UPDATE {$this->tabla} SET " . implode(', ', $setClausulas) . 
               " WHERE {$this->clavePrimaria} = :id";
        
        $datosFiltrados['id'] = $id;
        
        $filasAfectadas = $this->bd->actualizar($sql, $datosFiltrados);
        return $filasAfectadas > 0;
    }
    
    /**
     * Elimina un registro de la base de datos
     * @param int $id ID del registro a eliminar
     * @return bool True si se eliminó correctamente
     * @throws Exception Si hay error en la eliminación
     */
    public function eliminar(int $id): bool {
        // Verificar que el registro existe
        if (!$this->existe($id)) {
            throw new Exception("El registro con ID {$id} no existe");
        }
        
        $sql = "DELETE FROM {$this->tabla} WHERE {$this->clavePrimaria} = :id";
        $filasEliminadas = $this->bd->eliminar($sql, ['id' => $id]);
        
        return $filasEliminadas > 0;
    }
    
    /**
     * Verifica si existe un registro con el ID especificado
     * @param int $id ID a verificar
     * @return bool True si existe el registro
     */
    public function existe(int $id): bool {
        $sql = "SELECT COUNT(*) as total FROM {$this->tabla} WHERE {$this->clavePrimaria} = :id";
        $resultado = $this->bd->seleccionarUno($sql, ['id' => $id]);
        return ($resultado['total'] ?? 0) > 0;
    }
    
    /**
     * Cuenta el número total de registros
     * @param array $condiciones Condiciones WHERE opcionales
     * @return int Número total de registros
     */
    public function contar(array $condiciones = []): int {
        $sql = "SELECT COUNT(*) as total FROM {$this->tabla}";
        $parametros = [];
        
        if (!empty($condiciones)) {
            $clausulasWhere = [];
            foreach ($condiciones as $campo => $valor) {
                $clausulasWhere[] = "{$campo} = :{$campo}";
                $parametros[$campo] = $valor;
            }
            $sql .= " WHERE " . implode(' AND ', $clausulasWhere);
        }
        
        $resultado = $this->bd->seleccionarUno($sql, $parametros);
        return (int) ($resultado['total'] ?? 0);
    }
    
    /**
     * Valida los datos según las reglas definidas en el modelo
     * @param array $datos Datos a validar
     * @param int|null $idExcluir ID a excluir en validaciones de unicidad
     * @throws Exception Si los datos no son válidos
     */
    protected function validarDatos(array $datos, ?int $idExcluir = null): void {
        foreach ($this->reglasValidacion as $campo => $reglas) {
            $valor = $datos[$campo] ?? null;
            
            foreach ($reglas as $regla) {
                $this->aplicarReglaValidacion($campo, $valor, $regla, $idExcluir);
            }
        }
    }
    
    /**
     * Aplica una regla de validación específica
     * @param string $campo Nombre del campo
     * @param mixed $valor Valor del campo
     * @param string $regla Regla a aplicar
     * @param int|null $idExcluir ID a excluir en validaciones
     * @throws Exception Si la validación falla
     */
    protected function aplicarReglaValidacion(string $campo, $valor, string $regla, ?int $idExcluir = null): void {
        switch ($regla) {
            case 'requerido':
                if (empty($valor) && $valor !== '0') {
                    throw new Exception("El campo {$campo} es requerido");
                }
                break;
                
            case 'email':
                if (!empty($valor) && !filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("El campo {$campo} debe ser una dirección de email válida");
                }
                break;
                
            case 'unico':
                if (!empty($valor) && $this->esValorDuplicado($campo, $valor, $idExcluir)) {
                    throw new Exception("El valor del campo {$campo} ya existe en el sistema");
                }
                break;
        }
        
        // Validaciones con parámetros (ej: 'min:3', 'max:255')
        if (str_contains($regla, ':')) {
            [$tipoRegla, $parametro] = explode(':', $regla, 2);
            
            switch ($tipoRegla) {
                case 'min':
                    if (!empty($valor) && strlen($valor) < (int)$parametro) {
                        throw new Exception("El campo {$campo} debe tener al menos {$parametro} caracteres");
                    }
                    break;
                    
                case 'max':
                    if (!empty($valor) && strlen($valor) > (int)$parametro) {
                        throw new Exception("El campo {$campo} no puede tener más de {$parametro} caracteres");
                    }
                    break;
            }
        }
    }
    
    /**
     * Verifica si un valor es duplicado en la base de datos
     * @param string $campo Nombre del campo
     * @param mixed $valor Valor a verificar
     * @param int|null $idExcluir ID a excluir de la verificación
     * @return bool True si el valor está duplicado
     */
    protected function esValorDuplicado(string $campo, $valor, ?int $idExcluir = null): bool {
        $sql = "SELECT COUNT(*) as total FROM {$this->tabla} WHERE {$campo} = :{$campo}";
        $parametros = [$campo => $valor];
        
        if ($idExcluir !== null) {
            $sql .= " AND {$this->clavePrimaria} != :id_excluir";
            $parametros['id_excluir'] = $idExcluir;
        }
        
        $resultado = $this->bd->seleccionarUno($sql, $parametros);
        return ($resultado['total'] ?? 0) > 0;
    }
    
    /**
     * Filtra los datos para incluir solo campos llenables
     * @param array $datos Datos originales
     * @return array Datos filtrados
     */
    protected function filtrarCamposLlenables(array $datos): array {
        if (empty($this->camposLlenables)) {
            return $datos;
        }
        
        return array_intersect_key($datos, array_flip($this->camposLlenables));
    }
    
    /**
     * Oculta campos sensibles de los datos de respuesta
     * @param array $datos Datos originales
     * @return array Datos sin campos ocultos
     */
    protected function ocultarCamposSensibles(array $datos): array {
        foreach ($this->camposOcultos as $campo) {
            unset($datos[$campo]);
        }
        
        return $datos;
    }
    
    /**
     * Obtiene el nombre de la tabla
     * @return string Nombre de la tabla
     */
    public function obtenerNombreTabla(): string {
        return $this->tabla;
    }
    
    /**
     * Obtiene el nombre de la clave primaria
     * @return string Nombre de la clave primaria
     */
    public function obtenerClavePrimaria(): string {
        return $this->clavePrimaria;
    }
}
