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
                                <div class="col-lg-12 contorno"  data-doc_tipo_documento_id ="<?php echo $registro['doc_tipo_documento_id']; ?>" >
                                    <?php echo $registro['input']; ?>
                                    <?php foreach ($registro['fotos'] as $foto){
                                            foreach ($foto as $img){?>
                                                <div class="col-lg-6 contenedor_img" data-doc_documento_id ="<?php echo $img['doc_documento_id']; ?>">
                                                    <?php echo $img['input']; ?>
                                                    <a class="btn btn-danger elimina_img"  data-inm_doc_prospecto_ubicacion_id =
                                                    "<?php echo $img['inm_doc_prospecto_ubicacion_id']; ?>">Elimina</a>.
                                                </div>
                                    <?php       }
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

<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <img class="imagen_modal">
    </div>
</div>
