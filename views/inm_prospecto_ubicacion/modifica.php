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
                            <?php echo $controlador->inputs->dp_pais_id; ?>
                            <?php echo $controlador->inputs->dp_estado_id; ?>
                            <?php echo $controlador->inputs->dp_municipio_id; ?>
                            <?php echo $controlador->inputs->cp; ?>
                            <?php echo $controlador->inputs->colonia; ?>
                            <?php echo $controlador->inputs->calle; ?>
                            <?php echo $controlador->inputs->numero_exterior; ?>
                            <?php echo $controlador->inputs->numero_interior; ?>

                            <?php echo $controlador->inputs->inm_estado_vivienda_id; ?>
                            <?php echo $controlador->inputs->fecha_otorgamiento_credito; ?>
                            <?php echo $controlador->inputs->inm_prototipo_id; ?>
                            <?php echo $controlador->inputs->inm_complemento_id; ?>
                            <?php echo $controlador->inputs->manzana; ?>
                            <?php echo $controlador->inputs->lote; ?>
                            <?php echo $controlador->inputs->nivel; ?>
                            <?php echo $controlador->inputs->recamaras; ?>
                            <?php echo $controlador->inputs->metros_terreno; ?>
                            <?php echo $controlador->inputs->metros_construccion; ?>

                            <?php include (new views())->ruta_templates . 'botons/submit/modifica_bd.php'; ?>
                        </div>

                        <?php echo $controlador->header_frontend->apartado_4; ?>
                        <div id="apartado_4">
                            <?php echo $controlador->inputs->adeudo_hipoteca; ?>
                            <?php echo $controlador->inputs->cuenta_predial; ?>
                            <?php echo $controlador->inputs->adeudo_predial; ?>
                            <?php echo $controlador->inputs->cuenta_agua; ?>
                            <?php echo $controlador->inputs->adeudo_agua; ?>
                            <?php echo $controlador->inputs->adeudo_luz; ?>
                            <?php echo $controlador->inputs->monto_devolucion; ?>
                            <?php include (new views())->ruta_templates . 'botons/submit/modifica_bd.php'; ?>

                        </div>
                        <?php echo $controlador->header_frontend->apartado_5; ?>
                        <div id="apartado_5">
                            <?php echo $controlador->inputs->conyuge->nombre; ?>
                            <?php echo $controlador->inputs->conyuge->apellido_paterno; ?>
                            <?php echo $controlador->inputs->conyuge->apellido_materno; ?>
                            <?php echo $controlador->inputs->conyuge->dp_estado_id; ?>
                            <?php echo $controlador->inputs->conyuge->dp_municipio_id; ?>
                            <?php echo $controlador->inputs->conyuge->fecha_nacimiento; ?>
                            <?php echo $controlador->inputs->conyuge->inm_nacionalidad_id; ?>
                            <?php echo $controlador->inputs->conyuge->curp; ?>
                            <?php echo $controlador->inputs->conyuge->rfc; ?>
                            <?php echo $controlador->inputs->conyuge->inm_ocupacion_id; ?>
                            <?php echo $controlador->inputs->conyuge->telefono_casa; ?>
                            <?php echo $controlador->inputs->conyuge->telefono_celular; ?>
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



