<?php /** @var  gamboamartin\facturacion\controllers\controlador_fc_docto_relacionado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->nombre; ?>
<?php echo $controlador->inputs->apellido_paterno; ?>
<?php echo $controlador->inputs->apellido_materno; ?>
<?php echo $controlador->inputs->dp_estado_id; ?>
<?php echo $controlador->inputs->dp_municipio_id; ?>
<?php echo $controlador->inputs->fecha_nacimiento; ?>
<?php echo $controlador->inputs->inm_nacionalidad_id; ?>
<?php echo $controlador->inputs->curp; ?>
<?php echo $controlador->inputs->rfc; ?>
<?php echo $controlador->inputs->inm_ocupacion_id; ?>
<?php echo $controlador->inputs->telefono_casa; ?>
<?php echo $controlador->inputs->telefono_celular; ?>
<?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>