<?php /** @var  gamboamartin\inmuebles\controllers\controlador_inm_ubicacion $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<?php echo $controlador->inputs->registro_id; ?>
<main class="main section-color-primary">
    <div class="container">

        <div class="row">

            <div class="col-lg-12">

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">

                    <form method="post" action="<?php echo $controlador->link_opinion_valor_alta_bd; ?>"
                          class="form-additional">

                    <?php include (new views())->ruta_templates."head/title.php"; ?>
                    <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                    <?php include (new views())->ruta_templates."mensajes.php"; ?>



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
                                <h2>Costos</h2>
                            </div>

                            <div class="table table-responsive">
                                <table class='table table-striped data-partida'>
                                    <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Tipo Concepto</th>
                                        <th>Concepto</th>
                                        <th>Monto</th>
                                        <th>Fecha</th>
                                        <th>Ref</th>
                                        <th>Descripcion</th>
                                    <tr>
                                    </thead>
                                    <tbody>
                                    <?php    foreach ($controlador->inm_costos as $inm_costo){ ?>
                                    <tr>
                                        <td><?php echo $inm_costo['inm_costo_id'] ?></td>
                                        <td><?php echo $inm_costo['inm_tipo_concepto_descripcion'] ?></td>
                                        <td><?php echo $inm_costo['inm_concepto_descripcion'] ?></td>
                                        <td><?php echo $inm_costo['inm_costo_monto'] ?></td>
                                        <td><?php echo $inm_costo['inm_costo_fecha'] ?></td>
                                        <td><?php echo $inm_costo['inm_costo_referencia'] ?></td>
                                        <td><?php echo $inm_costo['inm_costo_descripcion'] ?></td>
                                    <tr>
                                    <?php }  ?>
                                    </tbody>
                                    <thead>
                                    <tr>
                                        <th colspan="7">Total: <?php echo $controlador->costo; ?></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


















