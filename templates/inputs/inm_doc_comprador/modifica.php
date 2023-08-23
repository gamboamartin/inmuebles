<?php /** @var  gamboamartin\facturacion\controllers\controlador_fc_docto_relacionado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->inm_comprador_id; ?>
<?php echo $controlador->inputs->doc_tipo_documento_id; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>