<?php

/**
 * Reglas de validacion puras (sin dependencias de base de datos).
 *
 * Se extraen aqui para poder probarlas de forma automatizada con PHPUnit
 * en el stage "Pruebas" del pipeline de integracion continua.
 */
class Validaciones
{
    public static function variable_iniciada($variable)
    {
        return isset($variable) && !empty($variable);
    }

    public static function nombre_valido($nombre)
    {
        return self::variable_iniciada($nombre) && strlen($nombre) >= 3;
    }

    public static function identificacion_valida($identificacion)
    {
        return self::variable_iniciada($identificacion) && strlen($identificacion) >= 6;
    }

    public static function correo_valido($correo)
    {
        return self::variable_iniciada($correo)
            && filter_var($correo, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function telefono_valido($telefono)
    {
        return self::variable_iniciada($telefono) && strlen($telefono) >= 5;
    }

    public static function clave_valida($clave)
    {
        return self::variable_iniciada($clave) && strlen($clave) >= 6;
    }

    public static function claves_coinciden($clave1, $clave2)
    {
        return self::variable_iniciada($clave2) && $clave1 === $clave2;
    }
}
