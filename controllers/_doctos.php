<?php
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\documento\models\doc_tipo_documento;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_conf_docs_comprador;
use PDO;

class _doctos{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    final public function documentos_de_comprador(PDO $link){
        $confs = (new inm_conf_docs_comprador(link: $link))->registros_activos();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al Obtener configuraciones',data:  $confs);
        }
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
