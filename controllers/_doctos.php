<?php
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\documento\models\doc_tipo_documento;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\models\inm_conf_docs_comprador;
use PDO;

class _doctos{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    final public function documentos_de_comprador(int $inm_comprador_id, PDO $link){

        $inm_comprador = (new inm_comprador(link: $link))->registro(registro_id: $inm_comprador_id,retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al Obtener comprador',data:  $inm_comprador);
        }

        $filtro['inm_attr_tipo_credito.id'] = $inm_comprador->inm_attr_tipo_credito_id;
        $filtro['inm_destino_credito.id'] = $inm_comprador->inm_destino_credito_id;
        $filtro['inm_producto_infonavit.id'] = $inm_comprador->inm_producto_infonavit_id;

        $r_inm_conf_docs_comprador = (new inm_conf_docs_comprador(link: $link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al Obtener configuraciones',data:  $r_inm_conf_docs_comprador);
        }

        $confs = $r_inm_conf_docs_comprador->registros;

        
        $values_in = array();
        foreach ($confs as $value){
            $values_in[] = $value['doc_tipo_documento_id'];
        }
        $in['llave'] = 'doc_tipo_documento.id';
        $in['values'] = $values_in;

        $r_doc_tipo_documento = (new doc_tipo_documento(link: $link))->filtro_and(in: $in);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al Obtener tipos de documento',data:  $r_doc_tipo_documento);
        }

        return $r_doc_tipo_documento->registros;
    }

}
