<?php /** @var  gamboamartin\facturacion\controllers\controlador_fc_docto_relacionado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->inm_tipo_beneficiario_id; ?>
<?php echo $controlador->inputs->inm_prospecto_id; ?>
<?php echo $controlador->inputs->inm_parentesco_id; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>