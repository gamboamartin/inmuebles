<?php /** @var  gamboamartin\facturacion\controllers\controlador_fc_docto_relacionado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->gt_proveedor_id; ?>
<?php echo $controlador->inputs->dp_municipio_id; ?>
<?php echo $controlador->inputs->numero_notaria; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>