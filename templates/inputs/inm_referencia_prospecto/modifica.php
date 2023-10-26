<?php /** @var  gamboamartin\facturacion\controllers\controlador_fc_docto_relacionado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

    <div class="col-md-12">
        <hr>
        <h4>7. REFERENCIAS FAMILIARES DEL (DE LA) DERECHOHABIENTE / DATOS QUE SERÁN VALIDADOS</h4>
        <hr>
    </div>


<?php echo $controlador->inputs->inm_prospecto_id; ?>
<?php echo $controlador->inputs->inm_parentesco_id; ?>
<?php echo $controlador->inputs->apellido_paterno; ?>
<?php echo $controlador->inputs->apellido_materno; ?>
<?php echo $controlador->inputs->nombre; ?>
<?php echo $controlador->inputs->lada; ?>
<?php echo $controlador->inputs->numero; ?>
<?php echo $controlador->inputs->celular; ?>



<?php echo $controlador->inputs->dp_pais_id; ?>
<?php echo $controlador->inputs->dp_estado_id; ?>
<?php echo $controlador->inputs->dp_municipio_id; ?>
<?php echo $controlador->inputs->dp_cp_id; ?>
<?php echo $controlador->inputs->dp_colonia_postal_id; ?>
<?php echo $controlador->inputs->dp_calle_pertenece_id; ?>
<?php echo $controlador->inputs->numero_dom; ?>

<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>