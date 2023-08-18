<?php /** @var  gamboamartin\facturacion\controllers\controlador_fc_docto_relacionado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<?php /**
 *
 * GENERALES
 *
 **/
?>

<?php echo $controlador->inputs->com_tipo_cliente_id; ?>
<?php echo $controlador->inputs->nss; ?>
<?php echo $controlador->inputs->curp; ?>
<?php echo $controlador->inputs->rfc; ?>
<?php echo $controlador->inputs->apellido_paterno; ?>
<?php echo $controlador->inputs->apellido_materno; ?>
<?php echo $controlador->inputs->nombre; ?>

<?php /**
 *
 * CONTACTO
 *
 **/
?>

<?php echo $controlador->inputs->dp_pais_id; ?>
<?php echo $controlador->inputs->dp_estado_id; ?>
<?php echo $controlador->inputs->dp_municipio_id; ?>
<?php echo $controlador->inputs->dp_cp_id; ?>
<?php echo $controlador->inputs->dp_colonia_postal_id; ?>
<?php echo $controlador->inputs->dp_calle_pertenece_id; ?>
<?php echo $controlador->inputs->numero_exterior; ?>
<?php echo $controlador->inputs->numero_interior; ?>
<?php echo $controlador->inputs->telefono; ?>

<?php /**
 *
 * INFONAVIT
 *
 **/
?>

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


    <div class="control-group col-sm-12">
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


<?php echo $controlador->inputs->descuento_pension_alimenticia_dh; ?>
<?php echo $controlador->inputs->descuento_pension_alimenticia_fc; ?>
<?php echo $controlador->inputs->monto_credito_solicitado_dh; ?>
<?php echo $controlador->inputs->monto_ahorro_voluntario; ?>

<?php /**
 *
 * DATOS EMPRESA
 *
 **/
?>
<?php echo $controlador->inputs->nombre_empresa_patron; ?>
<?php echo $controlador->inputs->nrp_nep; ?>
<?php echo $controlador->inputs->lada_nep; ?>
<?php echo $controlador->inputs->numero_nep; ?>
<?php echo $controlador->inputs->extension_nep; ?>

<?php /**
 *
 * FISCALES
 *
 **/
?>

<?php echo $controlador->inputs->cat_sat_regimen_fiscal_id; ?>
<?php echo $controlador->inputs->cat_sat_moneda_id; ?>
<?php echo $controlador->inputs->cat_sat_forma_pago_id; ?>
<?php echo $controlador->inputs->cat_sat_metodo_pago_id; ?>
<?php echo $controlador->inputs->cat_sat_uso_cfdi_id; ?>
<?php echo $controlador->inputs->cat_sat_tipo_persona_id; ?>



<?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>