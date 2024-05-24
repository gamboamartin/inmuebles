<?php /** @var  gamboamartin\inmuebles\controllers\controlador_inm_prospecto $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<main class="main section-color-primary">
    <div class="container">

        <div class="row">

            <div class="col-lg-12">

                <div class="widget" >

                    <?php include (new views())->ruta_templates."head/title.php"; ?>
                    <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                    <?php include (new views())->ruta_templates."mensajes.php"; ?>


                    <?php echo $controlador->inputs->com_tipo_prospecto_id; ?>
                    <?php echo $controlador->inputs->nss; ?>
                    <?php echo $controlador->inputs->curp; ?>
                    <?php echo $controlador->inputs->rfc; ?>
                    <?php echo $controlador->inputs->apellido_paterno; ?>
                    <?php echo $controlador->inputs->apellido_materno; ?>
                    <?php echo $controlador->inputs->nombre; ?>

                </div>
            </div>

        </div>
    </div>
    <br>

    <div class="container" style="margin-top: 20px;">
        <div class="row">
            <div class="col-lg-12">
                <form id="form-documentos" action="<?php echo $controlador->link_agrupa_documentos; ?>" method="post"
                      enctype="multipart/form-data">
                    <input type="hidden" id="documentos" name="documentos" required>
                    <button id="agrupar" class="btn btn-success">Agrupar</button>
                </form>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-12 table-responsive">
                <table id="table-inm_prospecto" class="table mb-0 table-striped table-sm "></table>
            </div>
        </div>
    </div>
</main>

<dialog id="myModal">
    <span class="close-btn" id="closeModalBtn">&times;</span>
    <h2>Vista Previa</h2>
    <div class="content">
    </div>
</dialog>