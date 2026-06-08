<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/Validaciones.inc.php';

class ValidacionesTest extends TestCase
{
    public function test_nombre_valido_acepta_nombres_largos()
    {
        $this->assertTrue(Validaciones::nombre_valido('Ana'));
        $this->assertTrue(Validaciones::nombre_valido('Juan Perez'));
    }

    public function test_nombre_valido_rechaza_vacios_o_cortos()
    {
        $this->assertFalse(Validaciones::nombre_valido(''));
        $this->assertFalse(Validaciones::nombre_valido('Al'));
    }

    public function test_identificacion_requiere_seis_caracteres()
    {
        $this->assertTrue(Validaciones::identificacion_valida('1234567'));
        $this->assertFalse(Validaciones::identificacion_valida('123'));
    }

    public function test_correo_valido()
    {
        $this->assertTrue(Validaciones::correo_valido('usuario@ganandez.com'));
        $this->assertFalse(Validaciones::correo_valido('usuario-sin-arroba'));
        $this->assertFalse(Validaciones::correo_valido(''));
    }

    public function test_telefono_valido()
    {
        $this->assertTrue(Validaciones::telefono_valido('3001234567'));
        $this->assertFalse(Validaciones::telefono_valido('123'));
    }

    public function test_clave_valida_minimo_seis()
    {
        $this->assertTrue(Validaciones::clave_valida('secreta123'));
        $this->assertFalse(Validaciones::clave_valida('123'));
    }

    public function test_claves_deben_coincidir()
    {
        $this->assertTrue(Validaciones::claves_coinciden('secreta123', 'secreta123'));
        $this->assertFalse(Validaciones::claves_coinciden('secreta123', 'otra456'));
    }
}
