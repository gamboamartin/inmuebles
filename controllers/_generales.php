<?php
namespace gamboamartin\inmuebles\controllers;
use gamboamartin\calculo\calculo;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_prospecto;
use PDO;
use stdClass;

class _generales{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    final public function data_nacimiento(string $entidad_edo, string $entidad_mun, string $entidad_name,stdClass $row): string
    {
        $key_fecha_nac = $entidad_name.'_fecha_nacimiento';

        $key_mun = $entidad_mun.'_descripcion';
        $key_edo = $entidad_edo.'_descripcion';

        $lugar_fecha_nac = $row->$key_mun;
        $lugar_fecha_nac .= ' '.$row->$key_edo;
        $lugar_fecha_nac .= ' EL DIA  ';
        $lugar_fecha_nac .= ' '.$row->$key_fecha_nac;

        return trim($lugar_fecha_nac);

    }

    private function data_prospecto(stdClass $inm_prospecto): array|stdClass
    {
        $nombre_completo = $this->nombre_completo(name_entidad: 'inm_prospecto',row:  $inm_prospecto);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener nombre_completo',data:  $nombre_completo);
        }

        $inm_prospecto->inm_prospecto_nombre_completo = $nombre_completo;
        $lugar_fecha_nac = $this->data_nacimiento(entidad_edo: 'dp_estado_nacimiento',
            entidad_mun: 'dp_municipio_nacimiento', entidad_name: 'inm_prospecto', row: $inm_prospecto);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener lugar_fecha_nac',data:  $lugar_fecha_nac);
        }

        $inm_prospecto->inm_prospecto_lugar_fecha_nac = $lugar_fecha_nac;
        $edad = (new calculo())->edad_hoy(fecha_nacimiento: $inm_prospecto->inm_prospecto_fecha_nacimiento);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener edad',data:  $edad);
        }
        $inm_prospecto->inm_prospecto_edad = $edad;
        $inm_prospecto_telefono_empresa = $inm_prospecto->inm_prospecto_lada_nep.$inm_prospecto->inm_prospecto_numero_nep;
        $inm_prospecto->inm_prospecto_telefono_empresa  = $inm_prospecto_telefono_empresa;

        return $inm_prospecto;
    }

    final public function inm_conyuge_init(): stdClass
    {
        $inm_conyuge = new stdClass();

        $inm_conyuge->inm_conyuge_nombre = '';
        $inm_conyuge->inm_conyuge_apellido_paterno = '';
        $inm_conyuge->inm_conyuge_apellido_materno = '';
        $inm_conyuge->dp_municipio_descripcion = '';
        $inm_conyuge->inm_conyuge_fecha_nacimiento = '';
        $inm_conyuge->dp_estado_descripcion = '';
        $inm_conyuge->inm_conyuge_edad = '';
        $inm_conyuge->inm_conyuge_estado_civil= '';
        $inm_conyuge->inm_nacionalidad_descripcion= '';
        $inm_conyuge->inm_conyuge_curp= '';
        $inm_conyuge->inm_conyuge_rfc= '';
        $inm_conyuge->inm_ocupacion_descripcion= '';
        $inm_conyuge->inm_conyuge_telefono_casa= '';
        $inm_conyuge->inm_conyuge_telefono_celular= '';
        return $inm_conyuge;
    }

    final public function inm_prospecto(int $inm_prospecto_id, PDO $link){
        $inm_prospecto = (new inm_prospecto(link: $link))->registro(registro_id: $inm_prospecto_id,retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_prospecto',data:  $inm_prospecto);
        }

        $inm_prospecto = $this->data_prospecto(inm_prospecto: $inm_prospecto);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al ajusta prospecto',data:  $inm_prospecto);
        }
        return $inm_prospecto;
    }

    final public function nombre_completo(string $name_entidad, stdClass $row): string
    {
        $key_nombre = $name_entidad.'_nombre';
        $key_apellido_paterno = $name_entidad.'_apellido_paterno';
        $key_apellido_materno = $name_entidad.'_apellido_materno';
        $nombre_completo = $row->$key_nombre;
        $nombre_completo .= ' '.$row->$key_apellido_paterno;
        $nombre_completo .= ' '.$row->$key_apellido_materno;

        return trim($nombre_completo);
    }

}
