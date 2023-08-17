<?php /** @var  gamboamartin\facturacion\controllers\controlador_fc_docto_relacionado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->inm_tipo_credito_id; ?>
<?php echo $controlador->inputs->descripcion; ?>
<?php echo $controlador->inputs->x; ?>
<?php echo $controlador->inputs->y; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>