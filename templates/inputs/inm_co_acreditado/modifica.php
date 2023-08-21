<?php /** @var  gamboamartin\facturacion\controllers\controlador_fc_docto_relacionado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

    <div class="col-md-12">
        <hr>
        <h4>6. DATOS DE IDENTIFICACIÓN QUE SERÁN VALIDADOS (OBLIGATORIOS EN CRÉDITO CONYUGAL, FAMILIAR O CORRESIDENCIAL)</h4>
        <hr>
    </div>

<?php echo $controlador->inputs->nss; ?>
<?php echo $controlador->inputs->curp; ?>
<?php echo $controlador->inputs->rfc; ?>
<?php echo $controlador->inputs->apellido_paterno; ?>
<?php echo $controlador->inputs->apellido_materno; ?>
<?php echo $controlador->inputs->nombre; ?>
<?php echo $controlador->inputs->lada; ?>
<?php echo $controlador->inputs->numero; ?>
<?php echo $controlador->inputs->celular; ?>

<?php
$checked_genero_m = 'checked';
$checked_genero_f = '';
if($controlador->row_upd->genero === 'F'){
    $checked_genero_m = '';
    $checked_genero_f = 'checked';
}

?>

    <div class="control-group col-sm-6">
        <label class="control-label" for="inm_attr_tipo_credito_id">Genero</label>
        <label class="form-check-label chk">
            <input type="radio" name="genero" value="M"
                   class="form-check-input" id="genero"
                   title="Genero" <?php echo $checked_genero_m; ?> >
            M
        </label>
        <label class="form-check-label chk">
            <input type="radio" name="genero" value="F"
                   class="form-check-input" id="genero"
                   title="Genero" <?php echo $checked_genero_f; ?>>
            F
        </label>
    </div><?php echo $controlador->inputs->correo; ?>


    <div class="col-md-12">
        <hr>
        <h4>7. DATOS DE LA EMPRESA O PATRÓN CO ACREDITADO</h4>
        <hr>
    </div>

<?php echo $controlador->inputs->nombre_empresa_patron; ?>
<?php echo $controlador->inputs->nrp; ?>
<?php echo $controlador->inputs->lada_nep; ?>
<?php echo $controlador->inputs->numero_nep; ?>
<?php echo $controlador->inputs->extension_nep; ?>

<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>