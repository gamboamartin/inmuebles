<?php /** @var gamboamartin\comercial\controllers\controlador_com_prospecto $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php include (new views())->ruta_templates."head/title.php"; ?>

                <?php include (new views())->ruta_templates."mensajes.php"; ?>

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <form method="post" action="<?php echo $controlador->link_fotografia_bd; ?>" class="form-additional">
                        <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>

                        <?php foreach ($controlador->fotos as $tipo_documento_foto){
                            echo $tipo_documento_foto;
                        }
                        echo $controlador->inputs->pr_etapa_proceso_id; ?>
                        <?php echo $controlador->inputs->fecha; ?>
                        <?php echo $controlador->inputs->observaciones; ?>

                        <div class="controls">
                            <button type="submit" class="btn btn-success" value="fotos" name="btn_action_next">Carga Fotos</button><br>
                        </div>
                    </form>

                </div>

            </div>
        </div>

    </div>

</main>

