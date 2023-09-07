<?php /** @var  gamboamartin\facturacion\controllers\controlador_fc_docto_relacionado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<?php echo $controlador->buttons['btn_collapse_all']; ?>


<div class="col-md-12">
    <hr>
    <h4>1. CRÉDITO SOLICITADO
        <?php echo $controlador->buttons['btn_collapse_a1']; ?>
    </h4>
    <hr>
</div>

    <div  id="apartado_1">

<?php echo $controlador->inputs->inm_producto_infonavit_id; ?>
<?php echo $controlador->inputs->inm_attr_tipo_credito_id; ?>
<?php echo $controlador->inputs->inm_destino_credito_id; ?>
<?php echo $controlador->inputs->es_segundo_credito; ?>
<?php echo $controlador->inputs->inm_plazo_credito_sc_id; ?>
    </div>


    <div class="col-md-12">
        <hr>
        <h4>
            2. DATOS PARA DETERMINAR EL MONTO DE CRÉDITO
            <a class="btn btn-primary"  href="#apartado_2" role="button" id="collapse_a2">
                +/-
            </a>
        </h4>
        <hr>
    </div>

    <div id="apartado_2">
<?php echo $controlador->inputs->descuento_pension_alimenticia_dh; ?>
<?php echo $controlador->inputs->descuento_pension_alimenticia_fc; ?>
<?php echo $controlador->inputs->monto_credito_solicitado_dh; ?>
<?php echo $controlador->inputs->monto_ahorro_voluntario; ?>
    </div>


    <div class="col-md-12">
        <hr>
        <h4>
            3. DATOS DE LA VIVIENDA/TERRENO DESTINO DEL CRÉDITO
            <a class="btn btn-primary" href="#apartado_3" role="button" id="collapse_a3">
                +/-
            </a>
        </h4>
        <hr>
    </div>

    <div  id="apartado_3">
<?php echo $controlador->inputs->con_discapacidad; ?>

<?php echo $controlador->inputs->inm_tipo_discapacidad_id; ?>
<?php echo $controlador->inputs->inm_persona_discapacidad_id; ?>
    </div>


    <div class="col-md-12">
        <hr>
        <h4>
            4. DATOS DE LA EMPRESA O PATRÓN
            <a class="btn btn-primary" href="#apartado_4" role="button" id="collapse_a4">
                +/-
            </a>
        </h4>
        <hr>
    </div>
    <div  id="apartado_4">
<?php echo $controlador->inputs->nombre_empresa_patron; ?>
<?php echo $controlador->inputs->nrp_nep; ?>
<?php echo $controlador->inputs->lada_nep; ?>
<?php echo $controlador->inputs->numero_nep; ?>
<?php echo $controlador->inputs->extension_nep; ?>
    </div>

    <div class="col-md-12">
        <hr>
        <h4>
            5. DATOS DE IDENTIFICACIÓN DEL (DE LA) DERECHOHABIENTE / DATOS QUE SERÁN VALIDADOS
            <a class="btn btn-primary" href="#apartado_5" role="button" id="collapse_a5">
                +/-
            </a>
        </h4>
        <hr>
    </div>
    <div  id="apartado_5">
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

    </div>


    <div class="col-md-12">
        <hr>
        <h4>
            13. DATOS FISCALES PARA FACTURACION
            <a class="btn btn-primary" href="#apartado_13" role="button" id="collapse_a13">
                +/-
            </a>
        </h4>
        <hr>
    </div>

    <div  id="apartado_13">
<?php echo $controlador->inputs->cat_sat_regimen_fiscal_id; ?>
<?php echo $controlador->inputs->cat_sat_moneda_id; ?>
<?php echo $controlador->inputs->cat_sat_forma_pago_id; ?>
<?php echo $controlador->inputs->cat_sat_metodo_pago_id; ?>
<?php echo $controlador->inputs->cat_sat_uso_cfdi_id; ?>
<?php echo $controlador->inputs->cat_sat_tipo_persona_id; ?>
<?php echo $controlador->inputs->bn_cuenta_id; ?>
    </div>


    <div class="col-md-12">
        <hr>
        <h4>
            14. CONTROL INTERNO
            <a class="btn btn-primary" href="#apartado_14" role="button" id="collapse_a14">
                +/-
            </a>
        </h4>
        <hr>
    </div>
    <div  id="apartado_14">
<?php echo $controlador->inputs->com_tipo_cliente_id; ?>
    </div>



<?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>