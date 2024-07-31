<?php /** @var gamboamartin\comercial\controllers\controlador_com_prospecto $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php include (new views())->ruta_templates."head/title.php"; ?>

                <?php include (new views())->ruta_templates."mensajes.php"; ?>

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <form enctype="multipart/form-data" method="post" action="<?php echo $controlador->link_fotografia_bd; ?>" class="form-additional">
                        <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                            <?php foreach ($controlador->fotos as $registro){ ?>
                                <div class="col-lg-6">
                                    <?php echo $registro['input']; ?>
                                    <?php
                                        foreach ($registro['fotos'] as $foto){
                                            foreach ($foto as $img){
                                                echo $img;
                                            }
                                        }
                                    ?>
                                </div>
                            <?php } ?>
                        <?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>
                    </form>

                </div>

            </div>
        </div>

    </div>

</main>

