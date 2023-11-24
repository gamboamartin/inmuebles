<?php /** @var  gamboamartin\inmuebles\controllers\controlador_inm_prospecto $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->com_agente_id; ?>
<?php echo $controlador->inputs->comi_conf_comision_id; ?>
<?php echo $controlador->inputs->inm_ubicacion_id; ?>
<?php echo $controlador->inputs->inm_comprador_id; ?>
<?php echo $controlador->inputs->monto_pago; ?>
    <div class="control-group col-sm-6">
        <label class="control-label" for="fecha_pago">Fecha Pago</label>
        <div class="controls">
            <input type="date" name="fecha_pago" value="" class="form-control" required="" id="fecha_pago" placeholder="Fecha Pago" title="Fecha Pago">
        </div>
    </div>

<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>