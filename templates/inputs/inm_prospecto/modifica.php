<?php /** @var  gamboamartin\inmuebles\controllers\controlador_inm_prospecto $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<?php echo $controlador->header_frontend->apartado_1; ?>
    <div  id="apartado_1">
<?php echo $controlador->inputs->com_agente_id; ?>
<?php echo $controlador->inputs->com_tipo_prospecto_id; ?>
<?php echo $controlador->inputs->com_medio_prospeccion_id; ?>
<?php echo $controlador->inputs->liga_red_social; ?>
<?php echo $controlador->inputs->nombre; ?>
<?php echo $controlador->inputs->apellido_paterno; ?>
<?php echo $controlador->inputs->apellido_materno; ?>
<?php echo $controlador->inputs->nss; ?>
<?php echo $controlador->inputs->curp; ?>
<?php echo $controlador->inputs->rfc; ?>
<?php echo $controlador->inputs->fecha_nacimiento; ?>
<?php echo $controlador->inputs->dp_estado_nacimiento_id; ?>
<?php echo $controlador->inputs->dp_municipio_nacimiento_id; ?>
<?php echo $controlador->inputs->inm_nacionalidad_id; ?>
<?php echo $controlador->inputs->inm_ocupacion_id; ?>
<?php echo $controlador->inputs->telefono_casa; ?>
<?php echo $controlador->inputs->observaciones; ?>
    </div>

<?php echo $controlador->header_frontend->apartado_2; ?>
    <div  id="apartado_2">
<?php echo $controlador->inputs->lada_com; ?>
<?php echo $controlador->inputs->numero_com; ?>
<?php echo $controlador->inputs->cel_com; ?>
<?php echo $controlador->inputs->correo_com; ?>
<?php echo $controlador->inputs->razon_social; ?>
<?php echo $controlador->inputs->direccion_empresa; ?>
<?php echo $controlador->inputs->area_empresa; ?>
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
<?php echo $controlador->inputs->sub_cuenta; ?>
<?php echo $controlador->inputs->monto_final; ?>
<?php echo $controlador->inputs->descuento; ?>
<?php echo $controlador->inputs->puntos; ?>

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
<?php echo $controlador->inputs->correo_empresa; ?>
    </div>

<?php echo $controlador->header_frontend->apartado_8; ?>
    <div  id="apartado_8">
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

<?php echo $controlador->header_frontend->apartado_9; ?>
    <div  id="apartado_9">
        <?php echo $controlador->inputs->beneficiario->inm_tipo_beneficiario_id; ?>
        <?php echo $controlador->inputs->beneficiario->inm_parentesco_id; ?>
        <?php echo $controlador->inputs->beneficiario->nombre; ?>
        <?php echo $controlador->inputs->beneficiario->apellido_paterno; ?>
        <?php echo $controlador->inputs->beneficiario->apellido_materno; ?>
        <div class="col-md-12 table-responsive gt_beneficiario_table">
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


<?php echo $controlador->header_frontend->apartado_10; ?>
    <div  id="apartado_10">
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

<?php echo $controlador->header_frontend->apartado_11; ?>
    <div  id="apartado_11">
        <?php echo $controlador->inputs->nss_extra; ?>
        <?php echo $controlador->inputs->correo_mi_cuenta_infonavit; ?>
        <?php echo $controlador->inputs->password_mi_cuenta_infonavit; ?>

    </div>



<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>