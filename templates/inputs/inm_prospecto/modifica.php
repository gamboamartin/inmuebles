<?php /** @var  gamboamartin\facturacion\controllers\controlador_fc_docto_relacionado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<?php echo $controlador->header_frontend->apartado_1; ?>
    <div  id="apartado_1">
<?php echo $controlador->inputs->com_agente_id; ?>
<?php echo $controlador->inputs->com_tipo_prospecto_id; ?>
<?php echo $controlador->inputs->nombre; ?>
<?php echo $controlador->inputs->apellido_paterno; ?>
<?php echo $controlador->inputs->apellido_materno; ?>
<?php echo $controlador->inputs->nss; ?>
<?php echo $controlador->inputs->curp; ?>
<?php echo $controlador->inputs->rfc; ?>
<?php echo $controlador->inputs->dp_estado_nacimiento_id; ?>
<?php echo $controlador->inputs->dp_municipio_nacimiento_id; ?>
<?php echo $controlador->inputs->observaciones; ?>
    </div>

<?php echo $controlador->header_frontend->apartado_2; ?>
    <div  id="apartado_2">
<?php echo $controlador->inputs->lada_com; ?>
<?php echo $controlador->inputs->numero_com; ?>
<?php echo $controlador->inputs->cel_com; ?>
<?php echo $controlador->inputs->correo_com; ?>
<?php echo $controlador->inputs->razon_social; ?>
    </div>

<?php echo $controlador->header_frontend->apartado_3; ?>
    <div  id="apartado_3">

<?php echo $controlador->inputs->dp_pais_id; ?>
<?php echo $controlador->inputs->dp_estado_id; ?>
<?php echo $controlador->inputs->dp_municipio_id; ?>
<?php echo $controlador->inputs->dp_cp_id; ?>
<?php echo $controlador->inputs->dp_colonia_postal_id; ?>
<?php echo $controlador->inputs->dp_calle_pertenece_id; ?>
<?php echo $controlador->inputs->numero_exterior; ?>
<?php echo $controlador->inputs->numero_interior; ?>
    </div>

<?php echo $controlador->header_frontend->apartado_4; ?>
    <div  id="apartado_4">

<?php echo $controlador->inputs->inm_institucion_hipotecaria_id; ?>
<?php echo $controlador->inputs->inm_producto_infonavit_id; ?>
<?php echo $controlador->inputs->inm_attr_tipo_credito_id; ?>
<?php echo $controlador->inputs->inm_destino_credito_id; ?>
<?php echo $controlador->inputs->es_segundo_credito; ?>
<?php echo $controlador->inputs->inm_plazo_credito_sc_id; ?>
    </div>

<?php echo $controlador->header_frontend->apartado_5; ?>
    <div  id="apartado_5">

<?php echo $controlador->inputs->descuento_pension_alimenticia_dh; ?>
<?php echo $controlador->inputs->descuento_pension_alimenticia_fc; ?>
<?php echo $controlador->inputs->monto_credito_solicitado_dh; ?>
<?php echo $controlador->inputs->monto_ahorro_voluntario; ?>

    </div>

<?php echo $controlador->header_frontend->apartado_6; ?>
    <div  id="apartado_6">

<?php echo $controlador->inputs->con_discapacidad; ?>
<?php echo $controlador->inputs->inm_tipo_discapacidad_id; ?>
<?php echo $controlador->inputs->inm_persona_discapacidad_id; ?>
    </div>

<?php echo $controlador->header_frontend->apartado_7; ?>
    <div  id="apartado_7">

<?php echo $controlador->inputs->nombre_empresa_patron; ?>
<?php echo $controlador->inputs->nrp_nep; ?>
<?php echo $controlador->inputs->lada_nep; ?>
<?php echo $controlador->inputs->numero_nep; ?>
<?php echo $controlador->inputs->extension_nep; ?>
<?php echo $controlador->inputs->inm_sindicato_id; ?>
    </div>










<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>