<?php /** @var  gamboamartin\inmuebles\controllers\controlador_inm_ubicacion $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<?php echo $controlador->inputs->registro_id; ?>
<main class="main section-color-primary">
    <div class="container">

        <div class="row">

            <div class="col-lg-12">

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">

                    <form method="post" action="<?php echo $controlador->link_rel_ubi_comp_alta_bd; ?>"
                          class="form-additional">

                    <?php include (new views())->ruta_templates."head/title.php"; ?>
                    <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                    <?php include (new views())->ruta_templates."mensajes.php"; ?>

                    <?php echo $controlador->inputs->inm_ubicacion_id; ?>
                    <?php echo $controlador->inputs->precio_operacion; ?>

                    <?php echo $controlador->inputs->com_tipo_cliente_id; ?>
                    <?php echo $controlador->inputs->nss; ?>
                    <?php echo $controlador->inputs->curp; ?>
                    <?php echo $controlador->inputs->rfc; ?>
                    <?php echo $controlador->inputs->apellido_paterno; ?>
                    <?php echo $controlador->inputs->apellido_materno; ?>
                    <?php echo $controlador->inputs->nombre; ?>
                    <?php echo $controlador->inputs->inm_comprador_id; ?>
                    <?php echo $controlador->inputs->inm_comprador_id; ?>
                    <?php echo $controlador->inputs->seccion_retorno; ?>
                    <?php echo $controlador->inputs->btn_action_next; ?>
                    <?php echo $controlador->inputs->id_retorno; ?>
                    <?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>
                    </form>

                </div>

            </div>


            <div class="container">
                <div class="row">
                    <div class="col-md-12">

                        <div class="widget widget-box box-container widget-mylistings">
                            <div class="widget-header" style="display: flex;justify-content: space-between;align-items: center;">
                                <h2>Ubicaciones</h2>
                            </div>

                            <div class="table table-responsive">
                                <table class='table table-striped data-partida'>
                                    <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Estado</th>
                                        <th>Municipio</th>
                                        <th>CP</th>
                                        <th>Colonia</th>
                                        <th>Calle</th>
                                        <th>Ext</th>
                                        <th>Int</th>
                                        <th>Manzana</th>
                                        <th>Lote</th>
                                    <tr>
                                    </thead>
                                    <tbody>
                                    <?php    foreach ($controlador->inm_ubicaciones as $inm_ubicacion){ ?>
                                    <tr>
                                        <td><?php echo $inm_ubicacion['inm_ubicacion_id'] ?></td>
                                        <td><?php echo $inm_ubicacion['dp_estado_ubicacion_descripcion'] ?></td>
                                        <td><?php echo $inm_ubicacion['dp_municipio_ubicacion_descripcion'] ?></td>
                                        <td><?php echo $inm_ubicacion['dp_cp_ubicacion_descripcion'] ?></td>
                                        <td><?php echo $inm_ubicacion['dp_colonia_ubicacion_descripcion'] ?></td>
                                        <td><?php echo $inm_ubicacion['dp_calle_ubicacion_descripcion'] ?></td>
                                        <td><?php echo $inm_ubicacion['inm_ubicacion_numero_exterior'] ?></td>
                                        <td><?php echo $inm_ubicacion['inm_ubicacion_numero_interior'] ?></td>
                                        <td><?php echo $inm_ubicacion['inm_ubicacion_manzana'] ?></td>
                                        <td><?php echo $inm_ubicacion['inm_ubicacion_lote'] ?></td>
                                    <tr>
                                    <?php }  ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>




                </div>
    </div>
</main>


















