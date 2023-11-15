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

                    <?php echo $controlador->inputs->inm_comprador_id; ?>
                    <?php echo $controlador->inputs->precio_operacion; ?>

                    <?php echo $controlador->inputs->dp_estado_id; ?>
                    <?php echo $controlador->inputs->dp_municipio_id; ?>
                    <?php echo $controlador->inputs->dp_cp_id; ?>
                    <?php echo $controlador->inputs->dp_colonia_postal_id; ?>
                    <?php echo $controlador->inputs->dp_calle_pertenece_id; ?>
                    <?php echo $controlador->inputs->numero_exterior; ?>
                    <?php echo $controlador->inputs->numero_interior; ?>
                    <?php echo $controlador->inputs->manzana; ?>
                    <?php echo $controlador->inputs->lote; ?>
                    <?php echo $controlador->inputs->inm_ubicacion_id; ?>
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
                                <h2>Clientes</h2>
                            </div>

                            <div class="table table-responsive">
                                <table class='table table-striped data-partida'>
                                    <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Nombre</th>
                                        <th>AP</th>
                                        <th>AM</th>
                                        <th>NSS</th>
                                        <th>Precio Operacion</th>
                                    <tr>
                                    </thead>
                                    <tbody>
                                    <?php    foreach ($controlador->imp_compradores as $inm_comprador){ ?>
                                    <tr>
                                        <td><?php echo $inm_comprador['inm_comprador_id'] ?></td>
                                        <td><?php echo $inm_comprador['inm_comprador_nombre'] ?></td>
                                        <td><?php echo $inm_comprador['inm_comprador_apellido_paterno'] ?></td>
                                        <td><?php echo $inm_comprador['inm_comprador_apellido_materno'] ?></td>
                                        <td><?php echo $inm_comprador['inm_comprador_nss'] ?></td>
                                        <td><?php echo $inm_comprador['inm_rel_ubi_comp_precio_operacion'] ?></td>
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


















