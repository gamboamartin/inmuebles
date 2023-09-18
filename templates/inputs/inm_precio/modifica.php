<?php /** @var  gamboamartin\facturacion\controllers\controlador_fc_docto_relacionado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->inm_ubicacion_id; ?>
<?php echo $controlador->inputs->inm_institucion_hipotecaria_id; ?>
<?php echo $controlador->inputs->precio_venta; ?>
<?php echo $controlador->inputs->porcentaje_descuento_maximo; ?>
<?php echo $controlador->inputs->monto_descuento_maximo; ?>
<?php echo $controlador->inputs->porcentaje_comisiones_maximo; ?>
<?php echo $controlador->inputs->monto_comisiones_maximo; ?>
<?php echo $controlador->inputs->porcentaje_devolucion_maximo; ?>
<?php echo $controlador->inputs->monto_devolucion_maximo; ?>
<?php echo $controlador->inputs->fecha_inicial; ?>
<?php echo $controlador->inputs->fecha_final; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>