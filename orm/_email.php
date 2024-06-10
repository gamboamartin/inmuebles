<?php

namespace gamboamartin\inmuebles\models;

use base\orm\modelo;
use gamboamartin\notificaciones\models\not_emisor;
use stdClass;

class _email
{
    public modelo $modelo;

    public function __construct(modelo $modelo)
    {
        $this->modelo = $modelo;
    }

    public function emisor(string $correo): array
    {
        $validacion = $this->validar_correo($correo);
        if (!$validacion) {
            return $this->modelo->error->error(mensaje: "El correo '$correo' no es válido.", data: $correo);
        }

        $filtro = array();
        $filtro['email'] = $correo;
        $not_emisor = (new not_emisor(link: $this->modelo->link))->filtro_and(filtro: $filtro);
        if (errores::$error) {
            return $this->modelo->error->error(mensaje: "Error al filtrar al emisor", data: $not_emisor);
        }

        if($not_emisor->nro_regisros == 0){
            return $this->modelo->error->error(mensaje: "No se encontró el emisor con el correo '$correo'",
                data: $not_emisor);
        }

        return $not_emisor->registros[0]['not_emisor_id'];
    }

    public function validar_correo($correo): mixed
    {
        return filter_var($correo, FILTER_VALIDATE_EMAIL);
    }

}