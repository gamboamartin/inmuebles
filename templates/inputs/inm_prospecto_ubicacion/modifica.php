<?php /** @var  gamboamartin\inmuebles\controllers\controlador_inm_prospecto $controlador controlador en ejecucion */ ?>
<?php use config\views; ?>

<?php echo $controlador->header_frontend->apartado_1; ?>
    <div id="apartado_1">
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
        <?php //echo $controlador->inputs->telefono_casa; ?>
        <?php echo $controlador->inputs->observaciones; ?>
    </div>

<?php echo $controlador->header_frontend->apartado_2; ?>
    <div id="apartado_2">
        <?php echo $controlador->inputs->lada_com; ?>
        <?php echo $controlador->inputs->numero_com; ?>
        <?php echo $controlador->inputs->cel_com; ?>
        <?php echo $controlador->inputs->correo_com; ?>
        <?php echo $controlador->inputs->razon_social; ?>
    </div>

<?php echo $controlador->header_frontend->apartado_3; ?>
    <div id="apartado_3">

        <?php echo $controlador->inputs->dp_pais_id; ?>
        <?php echo $controlador->inputs->dp_estado_id; ?>
        <?php echo $controlador->inputs->dp_municipio_id; ?>
        <?php echo $controlador->inputs->dp_cp_id; ?>
        <?php echo $controlador->inputs->dp_colonia_postal_id; ?>
        <?php echo $controlador->inputs->dp_calle_pertenece_id; ?>
        <?php echo $controlador->inputs->texto_exterior; ?>
        <?php echo $controlador->inputs->texto_interior; ?>

        <div class="col-md-12 table-responsive com_direccion_table">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Tipo</th>
                    <th>Pais</th>
                    <th>Estado</th>
                    <th>Municipio</th>
                    <th>CP</th>
                    <th>Colonia</th>
                    <th>Calle</th>
                    <th>Exterior</th>
                    <th>Interior</th>
                    <th>Modifica</th>
                    <th>Elimina</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($controlador->direcciones as $direccion){ ?>
                    <tr>
                        <td><?php echo $direccion['com_direccion_id']; ?></td>
                        <td><?php echo $direccion['com_tipo_direccion_descripcion']; ?></td>
                        <td><?php echo $direccion['dp_pais_descripcion']; ?></td>
                        <td><?php echo $direccion['dp_estado_descripcion']; ?></td>
                        <td><?php echo $direccion['dp_municipio_descripcion']; ?></td>
                        <td><?php echo $direccion['dp_cp_descripcion']; ?></td>
                        <td><?php echo $direccion['dp_colonia_postal_descripcion']; ?></td>
                        <td><?php echo $direccion['dp_calle_pertenece_descripcion']; ?></td>
                        <td><?php echo $direccion['com_direccion_texto_exterior']; ?></td>
                        <td><?php echo $direccion['com_direccion_texto_interior']; ?></td>
                        <td><?php echo $direccion['btn_mod']; ?></td>
                        <td><?php echo $direccion['btn_del']; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

    </div>


<?php include (new views())->ruta_templates . 'botons/submit/modifica_bd.php'; ?>