<?php /** @var  gamboamartin\facturacion\controllers\controlador_fc_docto_relacionado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->com_agente_id; ?>
<?php echo $controlador->inputs->com_tipo_prospecto_id; ?>
<?php echo $controlador->inputs->nombre; ?>
<?php echo $controlador->inputs->apellido_paterno; ?>
<?php echo $controlador->inputs->apellido_materno; ?>
<?php echo $controlador->inputs->telefono; ?>
<?php echo $controlador->inputs->correo_com; ?>
<?php echo $controlador->inputs->razon_social; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>