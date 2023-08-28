<?php /** @var  gamboamartin\facturacion\controllers\controlador_fc_docto_relacionado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->pr_sub_proceso_id; ?>
<?php echo $controlador->inputs->inm_comprador_id; ?>
<?php echo $controlador->inputs->fecha; ?>

<?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>