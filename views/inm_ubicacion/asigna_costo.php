<?php /** @var  gamboamartin\inmuebles\controllers\controlador_inm_ubicacion $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<?php echo $controlador->inputs->registro_id; ?>
<main class="main section-color-primary">
    <div class="container">

        <div class="row">

            <div class="col-lg-12">

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">

                    <form method="post" action="<?php echo $controlador->link_costo_alta_bd; ?>"
                          class="form-additional">

                    <?php include (new views())->ruta_templates."head/title.php"; ?>
                    <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                    <?php include (new views())->ruta_templates."mensajes.php"; ?>



                    <?php echo $controlador->inputs->inm_concepto_id; ?>
                    <?php echo $controlador->inputs->referencia; ?>
                    <?php echo $controlador->inputs->fecha; ?>
                    <?php echo $controlador->inputs->monto; ?>
                    <?php echo $controlador->inputs->inm_costo_descripcion; ?>

                    <?php echo $controlador->forms_inputs_modifica; ?>

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

                            <?php include 'views/inm_ubicacion/_table_costo.php' ;?>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


















