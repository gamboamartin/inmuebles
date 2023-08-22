<?php
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\errores\errores;
use setasign\Fpdi\Fpdi;

class _pdf{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    final public function write(Fpdi $pdf,string $valor,float $x, float $y): Fpdi
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



        $pdf->SetXY($x, $y);
        $pdf->Write(0, $valor);
        return $pdf;
    }
    final public function write_x(string $name_entidad, Fpdi $pdf, array $row): Fpdi
    {
        $key_x = $name_entidad.'_x';
        $key_y = $name_entidad.'_y';

        $x = $row[$key_x];
        $y = $row[$key_y];

        $pdf = $this->write(pdf: $pdf,valor: 'X',x: $x, y: $y);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al escribir en pdf',data:  $pdf);
        }

        return $pdf;
    }



}

