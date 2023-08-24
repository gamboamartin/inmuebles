<?php /** @var  gamboamartin\inmuebles\controllers\controlador_inm_doc_comprador $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<main class="main section-color-primary">
    <div class="container">

        <div class="row">

            <div class="col-lg-12">

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">

                    <form method="post" action="#"
                          class="form-additional">

                    <?php include (new views())->ruta_templates."head/title.php"; ?>
                    <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                    <?php include (new views())->ruta_templates."mensajes.php"; ?>


                    <?php echo $controlador->inputs->com_tipo_cliente_descripcion; ?>
                    <?php echo $controlador->inputs->nss; ?>
                    <?php echo $controlador->inputs->curp; ?>
                    <?php echo $controlador->inputs->rfc; ?>
                    <?php echo $controlador->inputs->apellido_paterno; ?>
                    <?php echo $controlador->inputs->apellido_materno; ?>
                    <?php echo $controlador->inputs->nombre; ?>

                        <div class="control-group btn-alta">
                            <div class="controls">
                                <button type="submit" class="btn btn-success">Descargar</button><br>
                            </div>
                        </div>
                    </form>

                </div>

            </div>


            <div class="col-lg-12">
                <div class="row">
                    <div class="col-md-12">
                        <?php if($controlador->es_imagen){ ?>
                            <img src="<?php echo $controlador->ruta_doc; ?>" class="rounded" alt="Cinque Terre">
                        <?php } ?>
                    </div>
                </div>

                <br>
            </div>

        </div>
    </div>
</main>


















