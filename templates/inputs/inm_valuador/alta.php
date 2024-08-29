<?php /** @var  gamboamartin\facturacion\controllers\controlador_fc_docto_relacionado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->descripcion; ?>
<?php echo $controlador->inputs->gt_proveedor_id; ?>
<?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>