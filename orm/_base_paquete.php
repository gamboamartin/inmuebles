<?php
namespace gamboamartin\inmuebles\models;

class _base_paquete{

    /**
     * Integra una descripcion basada en nombres y generales
     * @param array $registro Registro en proceso
     * @return string
     */
    final public function descripcion(array $registro): string
    {
        $descripcion = $registro['nombre'];
        $descripcion .= ' '.$registro['apellido_paterno'];
        $descripcion .= ' '.$registro['apellido_materno'];
        $descripcion .= ' '.$registro['nss'];
        $descripcion .= ' '.$registro['curp'];
        $descripcion .= ' '.$registro['rfc'];
        return $descripcion;
    }
}
