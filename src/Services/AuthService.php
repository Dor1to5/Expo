<?php
namespace Services;

use Models\Usuario;

/**
 * Servicio de autenticación de usuarios
 */
class AuthService {
    /**
     * Verifica las credenciales del usuario
     */
    public function login($usuario, $password) {
        // Lógica de autenticación básica
        return ($usuario === 'admin' && $password === 'admin');
    }
}
