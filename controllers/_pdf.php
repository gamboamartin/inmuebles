<?php
namespace gamboamartin\inmuebles\controllers;

use base\orm\modelo;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_co_acreditado;
use PDO;
use setasign\Fpdi\Fpdi;
use stdClass;
use Throwable;

class _pdf{

    private errores $error;
    private Fpdi $pdf;

    public function __construct(Fpdi $pdf){
        $this->error = new errores();
        $this->pdf = $pdf;
    }

    final public function add_template(string $file_plantilla, int $page, string $path_base, bool $plantilla_cargada){
        $this->pdf->AddPage();
        $tpl_idx = $this->tpl_idx(file_plantilla: $file_plantilla, page: $page,path_base:  $path_base,
            plantilla_cargada: $plantilla_cargada);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $tpl_idx);
        }
        $this->pdf->useTemplate($tpl_idx, null, null, null, null, true);
        return $this->pdf;
    }

    private function apartado_1(stdClass $data){
        $pdf = $this->entidades_infonavit(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $pdf);
        }

        $pdf = $this->es_segundo_credito(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $pdf);
        }
        return $pdf;
    }

    private function apartado_2(stdClass $data){
        $write = array();
        $row_condiciones['inm_comprador_descuento_pension_alimenticia_dh'] = array('x'=>77,'y'=>117, 'value_compare'=>0.0);
        $row_condiciones['inm_comprador_descuento_pension_alimenticia_fc'] = array('x'=>115,'y'=>117, 'value_compare'=>0.0);
        $row_condiciones['inm_comprador_monto_credito_solicitado_dh'] = array('x'=>79,'y'=>131, 'value_compare'=>0.0);
        $row_condiciones['inm_comprador_monto_ahorro_voluntario'] = array('x'=>51.5,'y'=>143, 'value_compare'=>0.0);

        foreach ($row_condiciones as $key =>$row){
            $pdf = $this->write_condicion(key: $key,row:  $data->inm_comprador,value_compare:  $row['value_compare'],x:  $row['x'],y: $row['y']);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al escribir en pdf', data: $pdf);
            }
            $write[] = $pdf;
        }
        return $write;
    }

    private function apartado_3(stdClass $data){
        $keys_ubicacion = $this->keys_ubicacion();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener keys_ubicacion', data: $keys_ubicacion);
        }

        $write = $this->write_data(keys: $keys_ubicacion,row:  $data->imp_rel_ubi_comp);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }


        $condiciones = array();
        $condiciones['SI'] = 84;

        $coord = $this->x_y_compare(condiciones: $condiciones,key:  'inm_comprador_con_discapacidad',
            row:  $data->inm_comprador, x_init:  94.5, y_init: 190);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener coordenadas', data: $coord);
        }

        $pdf = $this->write( valor: 'X', x: $coord->x, y: $coord->y);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $pdf);
        }


        $condiciones = array();
        $condiciones[3] = 67;
        $condiciones[4] = 114;
        $condiciones[5] = 163;


        $coord = $this->x_y_compare(condiciones: $condiciones,key:  'inm_destino_credito_id',
            row:  $data->inm_comprador, x_init:  21.5, y_init: 224.5);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener coordenadas', data: $coord);
        }

        $pdf = $this->write( valor: $data->imp_rel_ubi_comp['inm_rel_ubi_comp_precio_operacion'], x: $coord->x, y: $coord->y);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $pdf);
        }

        return $pdf;
    }

    private function apartado_4(stdClass $data){
        $keys_comprador = $this->keys_comprador();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener keys_ubicacion', data: $keys_comprador);
        }

        $write = $this->write_data(keys: $keys_comprador,row:  $data->inm_comprador);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }
        return $write;
    }

    private function apartado_5(stdClass $data){
        $write = $this->write_comprador_hoja_3(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }

        $pdf_exe = $this->write(valor: $data->com_cliente['com_cliente_rfc'], x: 132, y: 30);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $pdf_exe);
        }

        $pdf_exe = $this->write_domicilio(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir domicilio', data: $pdf_exe);
        }


        $write = $this->write_cliente_hoja_3(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }


        $write = $this->write_genero(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }

        $write = $this->write_estado_civil(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }

        return $write;
    }



    private function ciudad(stdClass $data): string
    {
        $ciudad = strtoupper($data->inm_comprador['dp_municipio_empresa_descripcion']);
        $ciudad .= ", ".strtoupper($data->inm_comprador['dp_estado_empresa_descripcion']);
        return $ciudad;
    }

    private function domicilio(stdClass $data): string
    {
        $domicilio = $data->com_cliente['dp_calle_descripcion'].' '.$data->com_cliente['com_cliente_numero_exterior'];
        $domicilio .= $data->com_cliente['com_cliente_numero_interior'];

        return $domicilio;
    }
    private function entidades_infonavit(stdClass $data){
        $entidades_pdf = array('inm_producto_infonavit','inm_tipo_credito','inm_attr_tipo_credito',
            'inm_destino_credito','inm_plazo_credito_sc','inm_tipo_discapacidad','inm_persona_discapacidad');
        $writes = array();
        foreach ($entidades_pdf as $name_entidad){
            $pdf = $this->write_x(name_entidad: $name_entidad, row: $data->inm_comprador);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al escribir en pdf', data: $pdf);
            }
            $writes[] = $pdf;
        }
        return $writes;
    }
    private function es_segundo_credito(stdClass $data){
        $x = 46.5;
        $y = 91.5;
        if ($data->inm_comprador['inm_comprador_es_segundo_credito'] === 'SI') {
            $x = 31.5;
        }

        $pdf = $this->write( valor: 'X', x: $x, y: $y);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $pdf);
        }
        return $pdf;
    }

    private function get_key_referencias(int $indice){
        $keys_referencias = (new _keys_selects())->keys_referencias();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener keys_referencias', data: $keys_referencias);
        }
        if($indice === 1) {
            $keys_referencias = (new _keys_selects())->keys_referencias_2();
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener keys_referencias', data: $keys_referencias);
            }
        }
        return $keys_referencias;
    }

    private function get_x_var(array $condiciones, string $key_id,array $row, float $x_init){
        $x = $x_init;

        $key_compare = $row[$key_id];

        if(isset($condiciones[$key_compare])){
            $x = $condiciones[$key_compare];
        }

        return $x;

    }

    final public function hoja_1(stdClass $data){
        /**
         * 1. CRÉDITO SOLICITADO
         */


        $pdf = $this->apartado_1(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $pdf);
        }

        /**
         * 2. DATOS PARA DETERMINAR EL MONTO DE CRÉDITO
         */

        $pdf->SetFont('Arial', 'B', 10);

        $pdf = $this->apartado_2(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $pdf);
        }

        /**
         * 3. DATOS DE LA VIVIENDA/TERRENO DESTINO DEL CRÉDITO
         */


        $pdf = $this->apartado_3(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $pdf);
        }

        /**
         * 4. DATOS DE LA EMPRESA O PATRÓN
         */

        $pdf = $this->apartado_4(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $pdf);
        }
        return $pdf;
    }

    final public function hoja_2(stdClass $data, PDO $link){
        /**
         * 5. DATOS DE IDENTIFICACIÓN DEL (DE LA) DERECHOHABIENTE / DATOS QUE SERÁN VALIDADOS
         */

        $write = $this->apartado_5(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }

        /**
         * 6. DATOS DE IDENTIFICACIÓN QUE SERÁN VALIDADOS (OBLIGATORIOS EN CRÉDITO CONYUGAL, FAMILIAR O CORRESIDENCIAL)
         */

        $write = $this->write_co_acreditados(data: $data,link:  $link);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }


        /**
         * 7. REFERENCIAS FAMILIARES DEL (DE LA) DERECHOHABIENTE / DATOS QUE SERÁN VALIDADOS
         */
        $write = $this->write_referencias(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }

        return $write;
    }


    final public function hoja_3(stdClass $data, modelo $modelo){
        $write = $this->write_comprador_a_8(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }

        $write = $this->write(valor: $data->inm_comprador['org_empresa_razon_social'], x:16,y: 62);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }

        $write = $this->write_cuidad(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }


        $write = $this->write_fecha(modelo: $modelo);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }
        return $write;
    }


    private function keys_cliente(): array
    {

        $keys_cliente['dp_colonia_descripcion']= array('x'=>16,'y'=>61);
        $keys_cliente['dp_estado_descripcion']= array('x'=>105,'y'=>61);
        $keys_cliente['dp_municipio_descripcion']= array('x'=>16,'y'=>68);
        $keys_cliente['dp_cp_descripcion']= array('x'=>82,'y'=>68);
        return $keys_cliente;
    }

    private function keys_comprador(): array
    {
        $keys_comprador['inm_comprador_nombre_empresa_patron']= array('x'=>16,'y'=>249);
        $keys_comprador['inm_comprador_nrp_nep']= array('x'=>140,'y'=>249);
        $keys_comprador['inm_comprador_lada_nep']= array('x'=>57,'y'=>256);
        $keys_comprador['inm_comprador_numero_nep']= array('x'=>70,'y'=>256);
        $keys_comprador['inm_comprador_extension_nep']= array('x'=>116,'y'=>256);
        return $keys_comprador;
    }

    private function keys_comprador_hoja_2(): array
    {

        $keys_comprador['inm_comprador_nss']= array('x'=>16,'y'=>30);
        $keys_comprador['inm_comprador_curp']= array('x'=>67,'y'=>30);
        $keys_comprador['inm_comprador_apellido_paterno']= array('x'=>16,'y'=>37);
        $keys_comprador['inm_comprador_apellido_materno']= array('x'=>106,'y'=>37);
        $keys_comprador['inm_comprador_nombre']= array('x'=>16,'y'=>44);
        $keys_comprador['inm_comprador_lada_com']= array('x'=>27,'y'=>76);
        $keys_comprador['inm_comprador_numero_com']= array('x'=>40,'y'=>76);
        $keys_comprador['inm_comprador_cel_com']= array('x'=>88,'y'=>76);
        $keys_comprador['inm_comprador_correo_com']= array('x'=>37.5,'y'=>85.5);
        return $keys_comprador;
    }

    private function keys_comprador_hoja_3(): array
    {
        $keys_comprador = array();
        $keys_comprador['org_empresa_razon_social']= array('x'=>16,'y'=>37);
        $keys_comprador['org_empresa_rfc']= array('x'=>22,'y'=>57);
        $keys_comprador['bn_cuenta_descripcion']= array('x'=>16,'y'=>85);
        return $keys_comprador;
    }

    private function keys_ubicacion(): array
    {
        $keys_ubicacion['dp_calle_ubicacion_descripcion']= array('x'=>15.5,'y'=>164);
        $keys_ubicacion['inm_ubicacion_numero_exterior']= array('x'=>15.5,'y'=>170);
        $keys_ubicacion['inm_ubicacion_numero_interior']= array('x'=>31,'y'=>170);
        $keys_ubicacion['inm_ubicacion_lote']= array('x'=>46,'y'=>170);
        $keys_ubicacion['inm_ubicacion_manzana']= array('x'=>61,'y'=>170);
        $keys_ubicacion['dp_colonia_ubicacion_descripcion']= array('x'=>81,'y'=>170);
        $keys_ubicacion['dp_estado_ubicacion_descripcion']= array('x'=>15.5,'y'=>176);
        $keys_ubicacion['dp_municipio_ubicacion_descripcion']= array('x'=>100,'y'=>176);
        $keys_ubicacion['dp_cp_ubicacion_descripcion']= array('x'=>173,'y'=>176);
        return $keys_ubicacion;
    }

    private function tpl_idx(string $file_plantilla, int $page, string $path_base, bool $plantilla_cargada): array|string
    {
        try {
            if(!$plantilla_cargada) {
                $this->pdf->setSourceFile($path_base . $file_plantilla);
            }
            $tpl_idx = $this->pdf->importPage($page);
        } catch (Throwable $e) {
            return $this->error->error(mensaje: 'Error al obtener plantilla', data: $e);
        }

        return $tpl_idx;
    }


    private function x_y_compare(array $condiciones, string $key, array $row, float $x_init, float $y_init){
        $x = $this->get_x_var(condiciones: $condiciones,key_id:  $key,
            row:  $row, x_init: $x_init);

        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener x', data: $x);
        }
        $y = $y_init;

        $data = new stdClass();
        $data->x = $x;
        $data->y = $y;

        return $data;
    }


    final public function write(string $valor,float $x, float $y): Fpdi
    {
        $valor = trim($valor);

        $valor = str_replace('á', 'A', $valor);
        $valor = str_replace('é', 'E', $valor);
        $valor = str_replace('í', 'I', $valor);
        $valor = str_replace('ó', 'O', $valor);
        $valor = str_replace('ú', 'U', $valor);
        $valor = str_replace('ñ', 'Ñ', $valor);

        $valor = mb_convert_encoding($valor, 'ISO-8859-1');

        $valor = strtoupper($valor);



        $this->pdf->SetXY($x, $y);
        $this->pdf->Write(0, $valor);
        return $this->pdf;
    }

    private function write_cuidad(stdClass $data){
        $ciudad = $this->ciudad(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener ciudad', data: $ciudad);
        }

        $write = $this->write(valor: $ciudad, x:36,y: 240);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }
        return $write;
    }

    private function write_cliente_hoja_3(stdClass $data){
        $keys_cliente = $this->keys_cliente();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener keys_cliente', data: $keys_cliente);
        }

        $write = $this->write_data(keys: $keys_cliente,row:  $data->com_cliente);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }
        return $write;
    }

    private function write_co_acreditado(int $inm_co_acreditado_id, PDO $link){
        $inm_co_acreditado = (new inm_co_acreditado(link: $link))->registro(registro_id: $inm_co_acreditado_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_co_acreditado',data:  $inm_co_acreditado);
        }


        $keys_co_acreditado = (new _keys_selects())->keys_co_acreditado();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar keys', data: $keys_co_acreditado);
        }


        $write = $this->write_data(keys: $keys_co_acreditado,row:  $inm_co_acreditado);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }

        $write = $this->write_co_acreditado_genero(inm_co_acreditado: $inm_co_acreditado);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }
        return $write;
    }

    private function write_co_acreditados(stdClass $data, PDO $link){
        $writes = array();
        foreach ($data->inm_rel_co_acreditados as $imp_rel_co_acred){
            $write = $this->write_co_acreditado($imp_rel_co_acred['inm_co_acreditado_id'],link:  $link);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
            }
            $writes[] = $write;
        }
        return $writes;
    }

    private function write_co_acreditado_genero(array $inm_co_acreditado): Fpdi
    {
        $x = 144;
        $y = 130;

        if($inm_co_acreditado['inm_co_acreditado_genero'] === 'F'){

            $x = 150.5;
        }

        $this->pdf->SetXY($x, $y);
        $this->pdf->Write(0, 'X');
        return $this->pdf;
    }

    private function write_comprador(stdClass $data){
        $keys_comprador = $this->keys_comprador_hoja_3();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener keys_comprador', data: $keys_comprador);
        }

        $write = $this->write_data(keys: $keys_comprador,row:  $data->inm_comprador);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }
        return $write;
    }

    private function write_comprador_a_8(stdClass $data){
        $pdf = $this->write_x(name_entidad: 'inm_tipo_inmobiliaria',row:  $data->inm_conf_empresa);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al escribir en pdf',data:  $pdf);
        }
        $write = $this->write_comprador(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }
        return $write;
    }

    private function write_comprador_hoja_3(stdClass $data){
        $keys_comprador = $this->keys_comprador_hoja_2();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener keys_comprador', data: $keys_comprador);
        }

        $write = $this->write_data(keys: $keys_comprador,row:  $data->inm_comprador);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }
        return $write;
    }

    private function write_condicion(string $key, array $row, mixed $value_compare, float $x, float $y){
        $write = false;
        if (round($row[$key], 2) > $value_compare) {
            $pdf = $this->write( valor: $row[$key], x: $x, y: $y);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al escribir en pdf', data: $pdf);
            }
            $write = true;
        }
        return $write;
    }

    final public function write_data(array $keys, array $row){
        $writes = array();
        foreach ($keys as $key=>$coordenadas){

            if(!isset($row[$key])){
                $row[$key] = '';
            }

            $pdf = $this->write(valor: $row[$key], x: $coordenadas['x'], y: $coordenadas['y']);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al escribir en pdf', data: $pdf);
            }
            $writes[] = $pdf;
        }
        return $writes;
    }

    private function write_dia(){
        $write = $this->write(valor: ((int)date('d')), x:119,y: 240);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }
        return $write;
    }

    private function write_domicilio(stdClass $data){
        $domicilio = $this->domicilio(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener domicilio', data: $domicilio);
        }

        $pdf_exe = $this->write(valor: $domicilio,x: 16,y: 54);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir domicilio', data: $pdf_exe);
        }
        return $pdf_exe;
    }

    private function write_estado_civil(stdClass $data): Fpdi
    {
        $this->pdf->SetXY($data->inm_comprador['inm_estado_civil_x'], $data->inm_comprador['inm_estado_civil_y']);
        $this->pdf->Write(0, 'X');

        if((int)$data->inm_comprador['inm_estado_civil_id'] !==1){
            $this->pdf->SetXY(58.5, 90);
            $this->pdf->Write(0, 'X');
        }
        return $this->pdf;
    }

    private function write_fecha(modelo $modelo){
        $write = $this->write_dia();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }


        $write = $this->write_mes(modelo: $modelo);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }


        $write = $this->write_year(modelo: $modelo);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }
        return $write;
    }

    private function write_genero(stdClass $data){
        $x = 144.5;
        $y = 77;

        if($data->inm_comprador['inm_comprador_genero'] === 'F'){

            $x = 150.5;
        }

        $pdf_exe = $this->write( valor: 'X', x: $x, y: $y);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $pdf_exe);
        }
        return $pdf_exe;
    }

    private function write_mes(modelo $modelo){
        $mes_letra = $modelo->mes['espaniol'][date('m')]['nombre'];


        $write = $this->write(valor: $mes_letra, x:128,y: 240);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }
        return $write;
    }

    private function write_referencia(array $inm_referencia, array $keys_referencias){

        $write = $this->write_data(keys: $keys_referencias,row:  $inm_referencia);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }
        return $write;
    }

    private function write_referencias(stdClass $data){
        $writes = array();
        foreach ($data->inm_referencias as $indice=>$inm_referencia){
            $keys_referencias = $this->get_key_referencias(indice: $indice);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener keys_referencias', data: $keys_referencias);
            }

            $write = $this->write_referencia(inm_referencia: $inm_referencia, keys_referencias: $keys_referencias);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
            }
            $writes[] = $write;
        }
        return $writes;
    }

    final public function write_x(string $name_entidad, array $row): Fpdi
    {
        $key_x = $name_entidad.'_x';
        $key_y = $name_entidad.'_y';

        $x = $row[$key_x];
        $y = $row[$key_y];

        $this->pdf = $this->write(valor: 'X',x: $x, y: $y);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al escribir en pdf',data:  $this->pdf);
        }

        return $this->pdf;
    }

    private function write_year(modelo $modelo){
        $year = $modelo->year['espaniol'][date('Y')]['abreviado'];

        $write = $this->write(valor: $year, x:178,y: 240);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al escribir en pdf', data: $write);
        }
        return $write;
    }



}

