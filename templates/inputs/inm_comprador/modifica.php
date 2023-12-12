<?php /** @var  gamboamartin\facturacion\controllers\controlador_fc_docto_relacionado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<?php echo $controlador->buttons['btn_collapse_all']; ?>

<?php echo $controlador->header_frontend->apartado_1; ?>

    <div  id="apartado_1">
        <?php echo $controlador->inputs->inm_institucion_hipotecaria_id; ?>
<?php echo $controlador->inputs->inm_producto_infonavit_id; ?>
<?php echo $controlador->inputs->inm_attr_tipo_credito_id; ?>
<?php echo $controlador->inputs->inm_destino_credito_id; ?>
<?php echo $controlador->inputs->es_segundo_credito; ?>

<?php



$checked_genero_m = 'checked';
$checked_genero_f = '';
if($controlador->row_upd->genero === 'F'){
    $checked_genero_m = '';
    $checked_genero_f = 'checked';
}


?>

<?php echo $controlador->inputs->inm_plazo_credito_sc_id; ?>

    </div>


<?php echo $controlador->header_frontend->apartado_2; ?>

    <div  id="apartado_2">
<?php echo $controlador->inputs->descuento_pension_alimenticia_dh; ?>
<?php echo $controlador->inputs->descuento_pension_alimenticia_fc; ?>
<?php echo $controlador->inputs->monto_credito_solicitado_dh; ?>
<?php echo $controlador->inputs->monto_ahorro_voluntario; ?>
<?php echo $controlador->inputs->sub_cuenta; ?>
<?php echo $controlador->inputs->monto_final; ?>
<?php echo $controlador->inputs->descuento; ?>
<?php echo $controlador->inputs->puntos; ?>
    </div>


<?php echo $controlador->header_frontend->apartado_3; ?>

    <div  id="apartado_3">
<?php echo $controlador->inputs->con_discapacidad; ?>
<?php echo $controlador->inputs->inm_tipo_discapacidad_id; ?>
<?php echo $controlador->inputs->inm_persona_discapacidad_id; ?>
    </div>

<?php echo $controlador->header_frontend->apartado_4; ?>


    <div  id="apartado_4">
<?php echo $controlador->inputs->nombre_empresa_patron; ?>
<?php echo $controlador->inputs->nrp_nep; ?>
<?php echo $controlador->inputs->lada_nep; ?>
<?php echo $controlador->inputs->numero_nep; ?>
<?php echo $controlador->inputs->extension_nep; ?>
<?php echo $controlador->inputs->inm_sindicato_id; ?>
<?php echo $controlador->inputs->correo_empresa; ?>
    </div>


<?php echo $controlador->header_frontend->apartado_5; ?>

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
    </div>

<?php echo $controlador->inputs->correo_com; ?>
<?php echo $controlador->inputs->inm_estado_civil_id; ?>
        <?php echo $controlador->inputs->dp_estado_nacimiento_id; ?>
        <?php echo $controlador->inputs->dp_municipio_nacimiento_id; ?>
        <?php echo $controlador->inputs->fecha_nacimiento; ?>
        <?php echo $controlador->inputs->inm_nacionalidad_id; ?>
        <?php echo $controlador->inputs->inm_ocupacion_id; ?>
        <?php echo $controlador->inputs->telefono_casa; ?>

    </div>

<?php if ($controlador->aplica_seccion_co_acreditado){ ?>

<?php echo $controlador->header_frontend->apartado_6; ?>
<div  id="apartado_6">
    <?php echo $controlador->inputs->inm_co_acreditado->nss; ?>
    <?php echo $controlador->inputs->inm_co_acreditado->curp; ?>
    <?php echo $controlador->inputs->inm_co_acreditado->rfc; ?>
    <?php echo $controlador->inputs->inm_co_acreditado->apellido_paterno; ?>
    <?php echo $controlador->inputs->inm_co_acreditado->apellido_materno; ?>
    <?php echo $controlador->inputs->inm_co_acreditado->nombre; ?>
    <?php echo $controlador->inputs->inm_co_acreditado->lada; ?>
    <?php echo $controlador->inputs->inm_co_acreditado->numero; ?>
    <?php echo $controlador->inputs->inm_co_acreditado->celular; ?>
    <div class="control-group col-sm-6">
        <label class="control-label" for="inm_attr_tipo_credito_id">Genero</label>
        <label class="form-check-label chk">
            <input type="radio" name="inm_co_acreditado_genero" value="M" class="form-check-input" id="genero"
                   title="Genero" checked>
            M
        </label>
        <label class="form-check-label chk">
            <input type="radio" name="inm_co_acreditado_genero" value="F" class="form-check-input" id="genero"
                   title="Genero">
            F
        </label>
    </div>
    <?php echo $controlador->inputs->inm_co_acreditado->correo; ?>

</div>

<?php echo $controlador->header_frontend->apartado_7; ?>
<div  id="apartado_7">
    <?php echo $controlador->inputs->inm_co_acreditado->nombre_empresa_patron; ?>
    <?php echo $controlador->inputs->inm_co_acreditado->nrp; ?>
    <?php echo $controlador->inputs->inm_co_acreditado->lada_nep; ?>
    <?php echo $controlador->inputs->inm_co_acreditado->numero_nep; ?>
</div>

<?php } ?>


<?php echo $controlador->header_frontend->apartado_13; ?>

    <div  id="apartado_13">

<?php echo $controlador->inputs->cat_sat_regimen_fiscal_id; ?>
<?php echo $controlador->inputs->cat_sat_moneda_id; ?>
<?php echo $controlador->inputs->cat_sat_forma_pago_id; ?>
<?php echo $controlador->inputs->cat_sat_metodo_pago_id; ?>
<?php echo $controlador->inputs->cat_sat_uso_cfdi_id; ?>
<?php echo $controlador->inputs->cat_sat_tipo_persona_id; ?>
<?php echo $controlador->inputs->bn_cuenta_id; ?>

    </div>

<?php echo $controlador->header_frontend->apartado_14; ?>

    <div  id="apartado_14">
<?php echo $controlador->inputs->com_tipo_cliente_id; ?>
    </div>

<?php echo $controlador->header_frontend->apartado_15; ?>
<div  id="apartado_15">
    <?php echo $controlador->inputs->conyuge->nombre; ?>
    <?php echo $controlador->inputs->conyuge->apellido_paterno; ?>
    <?php echo $controlador->inputs->conyuge->apellido_materno; ?>
    <?php echo $controlador->inputs->conyuge->dp_estado_id; ?>
    <?php echo $controlador->inputs->conyuge->dp_municipio_id; ?>
    <?php echo $controlador->inputs->conyuge->fecha_nacimiento; ?>
    <?php echo $controlador->inputs->conyuge->inm_nacionalidad_id; ?>
    <?php echo $controlador->inputs->conyuge->curp; ?>
    <?php echo $controlador->inputs->conyuge->rfc; ?>
    <?php echo $controlador->inputs->conyuge->inm_ocupacion_id; ?>
    <?php echo $controlador->inputs->conyuge->telefono_casa; ?>
    <?php echo $controlador->inputs->conyuge->telefono_celular;  ?>
</div>

<?php echo $controlador->header_frontend->apartado_16; ?>
<div  id="apartado_16">
    <?php echo $controlador->inputs->beneficiario->inm_tipo_beneficiario_id; ?>
    <?php echo $controlador->inputs->beneficiario->inm_parentesco_id; ?>
    <?php echo $controlador->inputs->beneficiario->nombre; ?>
    <?php echo $controlador->inputs->beneficiario->apellido_paterno; ?>
    <?php echo $controlador->inputs->beneficiario->apellido_materno; ?>
    <div class="col-md-12 table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Id</th>
                <th>Tipo Beneficiario</th>
                <th>Parentesco</th>
                <th>Nombre</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Elimina</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($controlador->beneficiarios as $beneficiario){ ?>
                <tr>
                    <td><?php echo $beneficiario['inm_beneficiario_id']; ?></td>
                    <td><?php echo $beneficiario['inm_tipo_beneficiario_descripcion']; ?></td>
                    <td><?php echo $beneficiario['inm_parentesco_descripcion']; ?></td>
                    <td><?php echo $beneficiario['inm_beneficiario_nombre']; ?></td>
                    <td><?php echo $beneficiario['inm_beneficiario_apellido_paterno']; ?></td>
                    <td><?php echo $beneficiario['inm_beneficiario_apellido_materno']; ?></td>
                    <td><?php echo $beneficiario['btn_del']; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>


<?php echo $controlador->header_frontend->apartado_17; ?>
<div  id="apartado_17">
    <?php echo $controlador->inputs->referencia->nombre; ?>
    <?php echo $controlador->inputs->referencia->apellido_paterno; ?>
    <?php echo $controlador->inputs->referencia->apellido_materno; ?>
    <?php echo $controlador->inputs->referencia->lada; ?>
    <?php echo $controlador->inputs->referencia->numero; ?>
    <?php echo $controlador->inputs->referencia->celular; ?>
    <?php echo $controlador->inputs->referencia->dp_estado_id; ?>
    <?php echo $controlador->inputs->referencia->dp_municipio_id; ?>
    <?php echo $controlador->inputs->referencia->dp_cp_id; ?>
    <?php echo $controlador->inputs->referencia->dp_colonia_postal_id; ?>
    <?php echo $controlador->inputs->referencia->dp_calle_pertenece_id; ?>
    <?php echo $controlador->inputs->referencia->numero_dom; ?>
    <?php echo $controlador->inputs->referencia->inm_parentesco_id; ?>
    <div class="col-md-12 table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>AP</th>
                <th>AM</th>
                <th>Parentesto</th>
                <th>Celular</th>
                <th>Elimina</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($controlador->referencias as $referencia){ ?>
                <tr>
                    <td><?php echo $referencia['inm_referencia_prospecto_id']; ?></td>
                    <td><?php echo $referencia['inm_referencia_prospecto_nombre']; ?></td>
                    <td><?php echo $referencia['inm_referencia_prospecto_apellido_paterno']; ?></td>
                    <td><?php echo $referencia['inm_referencia_prospecto_apellido_materno']; ?></td>
                    <td><?php echo $referencia['inm_parentesco_descripcion']; ?></td>
                    <td><?php echo $referencia['inm_referencia_prospecto_celular']; ?></td>
                    <td><?php echo $referencia['btn_del']; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>


<?php echo $controlador->btn; ?>


