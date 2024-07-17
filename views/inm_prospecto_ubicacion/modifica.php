<?php /** @var  gamboamartin\inmuebles\controllers\controlador_inm_prospecto $controlador controlador en ejecucion */ ?>
<?php use config\views; ?>
<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php include (new views())->ruta_templates . "head/title.php"; ?>

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <?php include (new views())->ruta_templates . "head/subtitulo.php"; ?>
                    <?php include (new views())->ruta_templates . "mensajes.php"; ?>
                    <form method="post" action="<?php echo $controlador->link_modifica_bd; ?>" class="form-additional"
                          enctype="multipart/form-data">

                        <?php echo $controlador->header_frontend->apartado_1; ?>
                        <div id="apartado_1">
                            <?php echo $controlador->inputs->com_agente_id; ?>
                            <?php echo $controlador->inputs->com_tipo_prospecto_id; ?>
                            <?php echo $controlador->inputs->com_medio_prospeccion_id; ?>
                            <?php echo $controlador->inputs->liga_red_social; ?>
                            <?php echo $controlador->inputs->nombre; ?>
                            <?php echo $controlador->inputs->apellido_paterno; ?>
                            <?php echo $controlador->inputs->apellido_materno; ?>
                            <?php echo $controlador->inputs->nss; ?>
                            <?php echo $controlador->inputs->curp; ?>
                            <?php echo $controlador->inputs->rfc; ?>
                            <?php //echo $controlador->inputs->telefono_casa; ?>
                            <?php echo $controlador->inputs->observaciones; ?>
                            <?php include (new views())->ruta_templates . 'botons/submit/modifica_bd.php'; ?>

                        </div>

                        <?php echo $controlador->header_frontend->apartado_2; ?>
                        <div id="apartado_2">
                            <?php echo $controlador->inputs->lada_com; ?>
                            <?php echo $controlador->inputs->numero_com; ?>
                            <?php echo $controlador->inputs->cel_com; ?>
                            <?php echo $controlador->inputs->correo_com; ?>
                            <?php echo $controlador->inputs->razon_social; ?>
                            <?php include (new views())->ruta_templates . 'botons/submit/modifica_bd.php'; ?>

                        </div>

                        <?php echo $controlador->header_frontend->apartado_3; ?>
                        <div id="apartado_3">
                            <?php echo $controlador->inputs->direccion->dp_pais_id; ?>
                            <?php echo $controlador->inputs->direccion->dp_estado_id; ?>
                            <?php echo $controlador->inputs->direccion->dp_municipio_id; ?>
                            <?php echo $controlador->inputs->direccion->cp; ?>
                            <?php echo $controlador->inputs->direccion->colonia; ?>
                            <?php echo $controlador->inputs->direccion->calle; ?>
                            <?php echo $controlador->inputs->direccion->texto_exterior; ?>
                            <?php echo $controlador->inputs->direccion->texto_interior; ?>
                        </div>

                        <?php include (new views())->ruta_templates . 'botons/submit/modifica_bd.php'; ?>
                    </form>
                </div>

            </div>
        </div>
    </div>
</main>


<dialog id="myModal">
    <form method="post" action="<?php echo $controlador->link_modifica_direccion; ?>" class="form-additional"
          enctype="multipart/form-data">
        <span class="close-btn" id="closeModalBtn">&times;</span>
        <h2>Modificar dirección</h2>
       <input type="hidden" name="com_direccion_id" id="com_direccion_id" value=""/>
        <?php echo $controlador->inputs->dp_pais_id; ?>
        <?php echo $controlador->inputs->dp_estado_id; ?>
        <?php echo $controlador->inputs->dp_municipio_id; ?>
        <?php echo $controlador->inputs->cp; ?>
        <?php echo $controlador->inputs->colonia; ?>
        <?php echo $controlador->inputs->calle; ?>
        <?php echo $controlador->inputs->texto_exterior; ?>
        <?php echo $controlador->inputs->texto_interior; ?>

        <div class="control-group btn-modifica">
            <div class="controls">
                <button type="submit" class="btn btn-success ">Modifica</button>
                <br>
            </div>
        </div>
    </form>
</dialog>



