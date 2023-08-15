<?php /** @var  gamboamartin\facturacion\controllers\controlador_fc_docto_relacionado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->nss; ?>
<?php echo $controlador->inputs->curp; ?>
<?php echo $controlador->inputs->rfc; ?>
<?php echo $controlador->inputs->apellido_paterno; ?>
<?php echo $controlador->inputs->apellido_materno; ?>
<?php echo $controlador->inputs->nombre; ?>

<?php echo $controlador->inputs->inm_producto_infonavit_id; ?>
<?php echo $controlador->inputs->inm_attr_tipo_credito_id; ?>
<?php echo $controlador->inputs->inm_destino_credito_id; ?>
<?php echo $controlador->inputs->es_segundo_credito; ?>
<?php echo $controlador->inputs->inm_plazo_credito_sc_id; ?>
<?php echo $controlador->inputs->descuento_pension_alimenticia_dh; ?>
<?php echo $controlador->inputs->descuento_pension_alimenticia_fc; ?>

<?php echo $controlador->inputs->monto_credito_solicitado_dh; ?>
<?php echo $controlador->inputs->monto_ahorro_voluntario; ?>
<?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>