<?php /** @var  gamboamartin\facturacion\controllers\controlador_fc_docto_relacionado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>







<div class="col-md-12">
    <hr>
    <h4>1. CRÉDITO SOLICITADO</h4>
    <hr>
</div>

<?php echo $controlador->inputs->inm_producto_infonavit_id; ?>
<?php echo $controlador->inputs->inm_attr_tipo_credito_id; ?>
<?php echo $controlador->inputs->inm_destino_credito_id; ?>

    <div class="control-group col-sm-6">
        <label class="control-label" for="inm_attr_tipo_credito_id">Es Segundo credito</label>
        <label class="form-check-label chk">
            <input type="radio" name="es_segundo_credito" value="NO" class="form-check-input" id="es_segundo_credito"
                   title="Es Segundo Credito" checked>
            NO
        </label>
        <label class="form-check-label chk">
            <input type="radio" name="es_segundo_credito" value="SI" class="form-check-input" id="es_segundo_credito"
                   title="Es Segundo Credito">
            SI
        </label>
    </div>


<?php echo $controlador->inputs->inm_plazo_credito_sc_id; ?>


    <div class="col-md-12">
        <hr>
        <h4>2. DATOS PARA DETERMINAR EL MONTO DE CRÉDITO</h4>
        <hr>
    </div>

<?php echo $controlador->inputs->descuento_pension_alimenticia_dh; ?>
<?php echo $controlador->inputs->descuento_pension_alimenticia_fc; ?>
<?php echo $controlador->inputs->monto_credito_solicitado_dh; ?>
<?php echo $controlador->inputs->monto_ahorro_voluntario; ?>


    <div class="col-md-12">
        <hr>
        <h4>3. DATOS DE LA VIVIENDA/TERRENO DESTINO DEL CRÉDITO</h4>
        <hr>
    </div>

    <div class="control-group col-sm-6">
        <label class="control-label" for="inm_attr_tipo_credito_id">Con Discapacidad</label>
        <label class="form-check-label chk">
            <input type="radio" name="con_discapacidad" value="NO" class="form-check-input" id="con_discapacidad"
                   title="Con discapacidad" checked>
            NO
        </label>
        <label class="form-check-label chk">
            <input type="radio" name="con_discapacidad" value="SI" class="form-check-input" id="con_discapacidad"
                   title="Con discapacidad">
            SI
        </label>
    </div>

<?php echo $controlador->inputs->inm_tipo_discapacidad_id; ?>
<?php echo $controlador->inputs->inm_persona_discapacidad_id; ?>


    <div class="col-md-12">
        <hr>
        <h4>4. DATOS DE LA EMPRESA O PATRÓN</h4>
        <hr>
    </div>

<?php echo $controlador->inputs->nombre_empresa_patron; ?>
<?php echo $controlador->inputs->nrp_nep; ?>
<?php echo $controlador->inputs->lada_nep; ?>
<?php echo $controlador->inputs->numero_nep; ?>
<?php echo $controlador->inputs->extension_nep; ?>

    <div class="col-md-12">
        <hr>
        <h4>5. DATOS DE IDENTIFICACIÓN DEL (DE LA) DERECHOHABIENTE / DATOS QUE SERÁN VALIDADOS</h4>
        <hr>
    </div>

<?php echo $controlador->inputs->nss; ?>
<?php echo $controlador->inputs->curp; ?>
<?php echo $controlador->inputs->rfc; ?>
<?php echo $controlador->inputs->apellido_paterno; ?>
<?php echo $controlador->inputs->apellido_materno; ?>
<?php echo $controlador->inputs->nombre; ?>

<?php echo $controlador->inputs->dp_pais_id; ?>
<?php echo $controlador->inputs->dp_estado_id; ?>
<?php echo $controlador->inputs->dp_municipio_id; ?>
<?php echo $controlador->inputs->dp_cp_id; ?>
<?php echo $controlador->inputs->dp_colonia_postal_id; ?>
<?php echo $controlador->inputs->dp_calle_pertenece_id; ?>
<?php echo $controlador->inputs->numero_exterior; ?>
<?php echo $controlador->inputs->numero_interior; ?>
<?php echo $controlador->inputs->lada_com; ?>
<?php echo $controlador->inputs->numero_com; ?>
<?php echo $controlador->inputs->cel_com; ?>

    <div class="control-group col-sm-6">
        <label class="control-label" for="inm_attr_tipo_credito_id">Genero</label>
        <label class="form-check-label chk">
            <input type="radio" name="genero" value="M" class="form-check-input" id="genero"
                   title="Genero" checked>
            M
        </label>
        <label class="form-check-label chk">
            <input type="radio" name="genero" value="F" class="form-check-input" id="genero"
                   title="Genero">
            F
        </label>
    </div>


<?php echo $controlador->inputs->correo_com; ?>
<?php echo $controlador->inputs->inm_estado_civil_id; ?>


    <div class="col-md-12">
        <hr>
        <h4>13. DATOS FISCALES PARA FACTURACION</h4>
        <hr>
    </div>

<?php echo $controlador->inputs->cat_sat_regimen_fiscal_id; ?>
<?php echo $controlador->inputs->cat_sat_moneda_id; ?>
<?php echo $controlador->inputs->cat_sat_forma_pago_id; ?>
<?php echo $controlador->inputs->cat_sat_metodo_pago_id; ?>
<?php echo $controlador->inputs->cat_sat_uso_cfdi_id; ?>
<?php echo $controlador->inputs->cat_sat_tipo_persona_id; ?>


    <div class="col-md-12">
        <hr>
        <h4>14. CONTROL INTERNO</h4>
        <hr>
    </div>

<?php echo $controlador->inputs->com_tipo_cliente_id; ?>



<?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>