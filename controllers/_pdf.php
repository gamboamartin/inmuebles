<?php
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\errores\errores;
use setasign\Fpdi\Fpdi;
use stdClass;

class _pdf{

    private errores $error;
    private Fpdi $pdf;

    public function __construct(Fpdi $pdf){
        $this->error = new errores();
        $this->pdf = $pdf;
    }

    final public function credito_solicitado(stdClass $data){
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

    final public function keys_comprador(): array
    {
        $keys_comprador['inm_comprador_nombre_empresa_patron']= array('x'=>16,'y'=>249);
        $keys_comprador['inm_comprador_nrp_nep']= array('x'=>140,'y'=>249);
        $keys_comprador['inm_comprador_lada_nep']= array('x'=>57,'y'=>256);
        $keys_comprador['inm_comprador_numero_nep']= array('x'=>70,'y'=>256);
        $keys_comprador['inm_comprador_extension_nep']= array('x'=>116,'y'=>256);
        return $keys_comprador;
    }

    final public function keys_ubicacion(): array
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

    final public function write_condicion(string $key, array $row, mixed $value_compare, float $x, float $y){
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



}

