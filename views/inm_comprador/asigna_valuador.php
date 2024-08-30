<?php /** @var  gamboamartin\inmuebles\controllers\controlador_inm_ubicacion $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<main class="main section-color-primary">
    <div class="container">

        <div class="row">

            <div class="col-lg-12">

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">

                    <form method="post" action="<?php echo $controlador->link_inm_rel_cliente_valuador_alta_bd; ?>"
                          class="form-additional">

                    <?php include (new views())->ruta_templates."head/title.php"; ?>
                    <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                    <?php include (new views())->ruta_templates."mensajes.php"; ?>

                    <?php echo $controlador->inputs->com_cliente_id; ?>
                    <?php echo $controlador->inputs->inm_valuador_id; ?>
                    <?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>
                    </form>

                </div>

            </div>


            <div class="container">
                <div class="row">
                    <div class="col-md-12">

                        <div class="widget widget-box box-container widget-mylistings">
                            <div class="widget-header" style="display: flex;justify-content: space-between;align-items: center;">
                                <h2>Relacion</h2>
                            </div>

                            <div class="table table-responsive">
                                <table class='table table-striped data-partida'>
                                    <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Rfc Cliente</th>
                                        <th>Cliente</th>
                                        <th>Rfc Valuador</th>
                                        <th>Valuador</th>
                                    <tr>
                                    </thead>
                                    <tbody>
                                    <?php    foreach ($controlador->inm_clientes_valuadores as $inm_ubicacion){ ?>
                                    <tr>
                                        <td><?php echo $inm_ubicacion['inm_rel_cliente_valuador_id'] ?></td>
                                        <td><?php echo $inm_ubicacion['com_cliente_rfc'] ?></td>
                                        <td><?php echo $inm_ubicacion['com_cliente_razon_social'] ?></td>
                                        <td><?php echo $inm_ubicacion['gt_proveedor_rfc'] ?></td>
                                        <td><?php echo $inm_ubicacion['gt_proveedor_razon_social'] ?></td>
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


















