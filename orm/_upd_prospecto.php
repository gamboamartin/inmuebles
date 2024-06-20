<?php
namespace gamboamartin\inmuebles\models;

use gamboamartin\comercial\models\com_direccion;
use gamboamartin\comercial\models\com_direccion_prospecto;
use gamboamartin\comercial\models\com_tipo_direccion;
use gamboamartin\errores\errores;
use gamboamartin\validacion\validacion;
use PDO;
use stdClass;

class _upd_prospecto{
    private errores $error;
    private validacion $validacion;

    public function __construct(){
        $this->error = new errores();
        $this->validacion = new validacion();
    }

    private function ajusta_beneficiario(stdClass $datos, int $inm_prospecto_id, PDO $link){

        $r_inm_beneficiario_bd = $this->inserta_beneficiario(beneficiario: $datos->row,
            inm_prospecto_id: $inm_prospecto_id,link: $link);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar r_inm_beneficiario_bd', data: $r_inm_beneficiario_bd);
        }
        $datos = $r_inm_beneficiario_bd;

        return $datos;
    }

    /**
     * Ajusta los datos de un conyuge
     * @param stdClass $datos Datos de integracion de conyuge
     * @param int $inm_prospecto_id Identificador de prospecto
     * @param PDO $link Conexion a la base de datos
     * @return array|stdClass
     */
    private function ajusta_conyuge(stdClass $datos, int $inm_prospecto_id, PDO $link): array|stdClass
    {
        if(!$datos->existe) {
            $r_inm_rel_conyuge_prospecto_bd = $this->inserta_conyuge(conyuge: $datos->row,
                inm_prospecto_id: $inm_prospecto_id,link: $link);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar conyuge', data: $r_inm_rel_conyuge_prospecto_bd);
            }
            $data = $r_inm_rel_conyuge_prospecto_bd;
        }
        else{
            $r_modifica_conyuge = $this->modifica_conyuge(
                conyuge: $datos->row,inm_prospecto_id:  $inm_prospecto_id,link: $link);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al modificar conyuge', data: $r_modifica_conyuge);
            }
            $data = $r_modifica_conyuge;
        }

        return $data;
    }

    private function ajusta_referencia(stdClass $datos, int $inm_prospecto_id, PDO $link){

        $r_inm_referencia_bd = $this->inserta_referencia(referencia: $datos->row,
            inm_prospecto_id: $inm_prospecto_id,link: $link);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar r_inm_beneficiario_bd', data: $r_inm_referencia_bd);
        }
        $datos = $r_inm_referencia_bd;

        return $datos;
    }

    /**
     * Obtiene un conyuge basado en el prospecto
     * @param bool $columnas_en_bruto si es true da resultado en campos tal como estan en base de datos
     * @param int $inm_prospecto_id Identificador de prospecto
     * @param PDO $link Conexion a la base de datos
     * @param bool $retorno_obj Si esta como true el resultado lo integra como un objeto
     * @return array|stdClass
     * @version 2.275.2
     */
    final public function inm_conyuge(bool $columnas_en_bruto, int $inm_prospecto_id, PDO $link,
                                      bool $retorno_obj): array|stdClass
    {
        if($inm_prospecto_id<=0){
            return $this->error->error(mensaje: 'Error inm_prospecto_id debe ser mayor a 0', data:  $inm_prospecto_id);
        }
        $filtro = array();
        $filtro['inm_prospecto.id'] = $inm_prospecto_id;

        $r_inm_rel_conyuge_prospecto = (new inm_rel_conyuge_prospecto(link: $link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener conyuge relacion',
                data:  $r_inm_rel_conyuge_prospecto);
        }
        if($r_inm_rel_conyuge_prospecto->n_registros === 0){
            return $this->error->error(mensaje: 'Error no existe relacion',data:  $r_inm_rel_conyuge_prospecto);
        }
        if($r_inm_rel_conyuge_prospecto->n_registros > 1){
            return $this->error->error(mensaje: 'Error de integridad',data:  $r_inm_rel_conyuge_prospecto);
        }

        $inm_rel_conyuge_prospecto = $r_inm_rel_conyuge_prospecto->registros[0];

        $inm_conyuge = (new inm_conyuge(link: $link))->registro(
            registro_id: $inm_rel_conyuge_prospecto['inm_conyuge_id'],columnas_en_bruto: $columnas_en_bruto,
            retorno_obj: $retorno_obj);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener conyuge',data:  $inm_conyuge);
        }

        return $inm_conyuge;
    }

    public function inserta_beneficiario(array $beneficiario, int $inm_prospecto_id, PDO $link): array|stdClass
    {
        $keys = array('nombre','apellido_paterno','inm_tipo_beneficiario_id','inm_parentesco_id');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $beneficiario);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar conyuge',data:  $valida);
        }
        $keys = array('inm_tipo_beneficiario_id','inm_parentesco_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $beneficiario);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar beneficiario',data:  $valida);
        }
        if($inm_prospecto_id <= 0){
            return $this->error->error(mensaje: 'Error inm_prospecto_id debe ser mayor a 0',data:  $inm_prospecto_id);
        }
        $beneficiario['inm_prospecto_id'] = $inm_prospecto_id;

        $alta_beneficiario= (new inm_beneficiario(link: $link))->alta_registro(registro: $beneficiario);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar alta_beneficiario', data: $alta_beneficiario);
        }

        $data = new stdClass();
        $data->alta_conyuge = $alta_beneficiario;

        return $data;
    }

    public function inserta_domicilio(array $domicilio, int $inm_prospecto_id, PDO $link): array|stdClass
    {
        $keys = array('cp','colonia','calle','texto_exterior');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $domicilio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar direccion',data:  $valida);
        }

        $keys = array('dp_municipio_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $domicilio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar beneficiario',data:  $valida);
        }

        if($inm_prospecto_id <= 0){
            return $this->error->error(mensaje: 'Error inm_prospecto_id debe ser mayor a 0',data:  $inm_prospecto_id);
        }

        $r_com_prospecto = (new inm_prospecto(link: $link))->registro(registro_id: $inm_prospecto_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar beneficiario',data:  $valida);
        }

        $tipo_direccion = (new com_tipo_direccion(link: $link))->filtro_and();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener tipo_direccion',data:  $tipo_direccion);
        }

        if ($tipo_direccion->n_registros === 0) {
            return ['error' => 'No existe un tipo de dirección valido registrado en el sistema'] ;
        }

        $alta_relacion = array();
        foreach ($tipo_direccion->registros as $value) {
            $domicilio['com_tipo_direccion_id'] = $value['com_tipo_direccion_id'];
            $alta_direccion = (new com_direccion(link: $link))->alta_registro(registro: $domicilio);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar alta_direccion', data: $alta_direccion);
            }

            $relacion['com_direccion_id'] = $alta_direccion->registro_id;
            $relacion['com_prospecto_id'] = $r_com_prospecto['com_prospecto_id'];
            $alta_relacion = (new com_direccion_prospecto(link: $link))->alta_registro(registro: $relacion);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar alta_relacion', data: $alta_relacion);
            }
        }

        return $alta_relacion;
    }

    /**
     * Inserta conyuge y genera la liga con el prospecto
     * @param array $conyuge Registro de conyuge a insertar
     * @param int $inm_prospecto_id Prospecto donde se relacionara el conyuge
     * @param PDO $link Conexion a la base de datos
     * @return array|stdClass
     * @version 2.269.2
     */
    private function inserta_conyuge(array $conyuge, int $inm_prospecto_id, PDO $link): array|stdClass
    {
        $keys = array('nombre','apellido_paterno','curp','rfc','dp_municipio_id','inm_nacionalidad_id',
            'inm_ocupacion_id','telefono_casa','telefono_celular','fecha_nacimiento');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $conyuge);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar conyuge',data:  $valida);
        }
        $keys = array('dp_municipio_id','inm_nacionalidad_id', 'inm_ocupacion_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $conyuge);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar conyuge',data:  $valida);
        }
        if($inm_prospecto_id <= 0){
            return $this->error->error(mensaje: 'Error inm_prospecto_id debe ser mayor a 0',data:  $inm_prospecto_id);
        }

        $alta_conyuge = (new inm_conyuge(link: $link))->alta_registro(registro: $conyuge);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar conyuge', data: $alta_conyuge);
        }

        $inm_rel_conyuge_prospecto_ins = (new _inm_prospecto())->inm_rel_conyuge_prospecto_ins(
            inm_conyuge_id: $alta_conyuge->registro_id, inm_prospecto_id: $inm_prospecto_id);

        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al maquetar conyuge relacion',
                data: $inm_rel_conyuge_prospecto_ins);
        }

        $r_inm_rel_conyuge_prospecto_bd = (new inm_rel_conyuge_prospecto(link: $link))->alta_registro(
            registro: $inm_rel_conyuge_prospecto_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar conyuge', data: $r_inm_rel_conyuge_prospecto_bd);
        }
        $data = new stdClass();
        $data->alta_conyuge = $alta_conyuge;
        $data->inm_rel_conyuge_prospecto_ins = $inm_rel_conyuge_prospecto_ins;
        $data->r_inm_rel_conyuge_prospecto_bd = $r_inm_rel_conyuge_prospecto_bd;

        return $data;
    }

    public function inserta_referencia(array $referencia, int $inm_prospecto_id, PDO $link): array|stdClass
    {
        $keys = array('nombre','apellido_paterno','dp_calle_pertenece_id','inm_parentesco_id');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $referencia);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar referencia',data:  $valida);
        }
        $keys = array('dp_calle_pertenece_id','inm_parentesco_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $referencia);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar beneficiario',data:  $valida);
        }
        if($inm_prospecto_id <= 0){
            return $this->error->error(mensaje: 'Error inm_prospecto_id debe ser mayor a 0',data:  $inm_prospecto_id);
        }
        $referencia['inm_prospecto_id'] = $inm_prospecto_id;

        $alta_referencia= (new inm_referencia_prospecto(link: $link))->alta_registro(registro: $referencia);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar alta_referencia', data: $alta_referencia);
        }

        $data = new stdClass();
        $data->alta_referencia = $alta_referencia;


        return $data;
    }

    /**
     * Modifica los datos de un conyuge ligado al prospecto
     * @param array $conyuge Registro de conyuge a transaccionar
     * @param int $inm_prospecto_id Identificador de prospecto
     * @param PDO $link Conexion a la base de datos
     * @return array|stdClass
     * @version 2.278.2
     */
    private function modifica_conyuge(array $conyuge, int $inm_prospecto_id, PDO $link): array|stdClass
    {
        if($inm_prospecto_id<=0){
            return $this->error->error(mensaje: 'Error inm_prospecto_id debe ser mayor a 0', data:  $inm_prospecto_id);
        }
        $inm_conyuge_previo = $this->inm_conyuge(columnas_en_bruto: true, inm_prospecto_id: $inm_prospecto_id,
            link: $link, retorno_obj: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener conyuge', data: $inm_conyuge_previo);
        }

        $inm_conyuge_id = $inm_conyuge_previo->id;

        $r_modifica_conyuge = (new inm_conyuge(link: $link))->modifica_bd(registro: $conyuge,id: $inm_conyuge_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al modificar conyuge', data: $r_modifica_conyuge);
        }

        $data = new stdClass();
        $data->inm_conyuge_previo = $inm_conyuge_previo;
        $data->r_modifica_conyuge = $r_modifica_conyuge;

        return $data;
    }

    final public function transacciona_beneficiario(int $inm_prospecto_id, PDO $link){
        $datos = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->dato(existe:false,key_data: 'beneficiario');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener dato de beneficiario',data:  $datos);
        }

        if($datos->tiene_dato){
            $result_beneficiario = $this->ajusta_beneficiario(datos: $datos,inm_prospecto_id: $inm_prospecto_id,link: $link);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar beneficiario', data: $result_beneficiario);
            }
            $datos->result_beneficiario = $result_beneficiario;
        }
        return $datos;

    }

    final public function transacciona_conyuge(int $inm_prospecto_id, PDO $link){
        $datos = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->datos_conyuge(
            link: $link,inm_prospecto_id: $inm_prospecto_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener dato conyuge',data:  $datos);
        }

        if($datos->tiene_dato){
            $result_conyuge = $this->ajusta_conyuge(datos: $datos,inm_prospecto_id: $inm_prospecto_id,link: $link);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar conyuge', data: $result_conyuge);
            }
            $datos->result_conyuge = $result_conyuge;
        }
        return $datos;

    }
    final public function transacciona_referencia(int $inm_prospecto_id, PDO $link){
        $datos = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->dato(existe: false,key_data: 'referencia');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener dato de referencia',data:  $datos);
        }

        if($datos->tiene_dato){
            $result_referencia = $this->ajusta_referencia(datos: $datos,inm_prospecto_id: $inm_prospecto_id,link: $link);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar referencia', data: $result_referencia);
            }
            $datos->result_referencia = $result_referencia;
        }
        return $datos;

    }

    final public function transacciona_direccion(int $inm_prospecto_id, PDO $link){
        $datos = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->dato(existe: false,key_data: 'direccion');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener dato de direccion',data:  $datos);
        }

        if($datos->tiene_dato){
            $result_direccion = $this->ajusta_direccion(datos: $datos,inm_prospecto_id: $inm_prospecto_id,link: $link);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar direccion', data: $result_direccion);
            }
            $datos->result_direccion = $result_direccion;
        }
        return $datos;

    }

    private function ajusta_direccion(stdClass $datos, int $inm_prospecto_id, PDO $link){

        $r_inm_direccion_bd = $this->inserta_domicilio(domicilio: $datos->row,
            inm_prospecto_id: $inm_prospecto_id,link: $link);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar r_inm_beneficiario_bd', data: $r_inm_direccion_bd);
        }
        $datos = $r_inm_direccion_bd;

        return $datos;
    }

    public function inserta_direccion(array $direccion, int $inm_prospecto_id, PDO $link): array|stdClass
    {
        $keys = array('cp','colonia','calle','texto_exterior');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $direccion);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar direccion',data:  $valida);
        }

        $keys = array('dp_municipio_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $direccion);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar beneficiario',data:  $valida);
        }

        $alta_direccion= (new com_direccion(link: $link))->alta_registro(registro: $direccion);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar alta_direccion', data: $alta_direccion);
        }

        if($inm_prospecto_id <= 0){
            return $this->error->error(mensaje: 'Error inm_prospecto_id debe ser mayor a 0',data:  $inm_prospecto_id);
        }

        $direccion_prospecto['inm_prospecto_id'] = $inm_prospecto_id;
        $direccion_prospecto['com_direccion_id'] = $alta_direccion['registro_id'];

        $alta_direccion_prosp= (new com_direccion_prospecto(link: $link))->alta_registro(registro: $direccion_prospecto);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar alta_direccion', data: $alta_direccion_prosp);
        }

        $data = new stdClass();
        $data->alta_direccion = $alta_direccion;


        return $data;
    }
}
