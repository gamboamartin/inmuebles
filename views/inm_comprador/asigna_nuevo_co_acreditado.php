<?php /** @var  gamboamartin\inmuebles\controllers\controlador_inm_comprador $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<?php echo $controlador->inputs->registro_id; ?>
<main class="main section-color-primary">
    <div class="container">

        <div class="row">

            <div class="col-lg-12">

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">

                    <form method="post" action="<?php echo $controlador->link_asigna_nuevo_co_acreditado_bd; ?>"
                          class="form-additional">

                    <?php include (new views())->ruta_templates."head/title.php"; ?>
                    <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                    <?php include (new views())->ruta_templates."mensajes.php"; ?>


                    <?php echo $controlador->inputs->com_tipo_cliente_id; ?>
                    <?php echo $controlador->inputs->nss; ?>
                    <?php echo $controlador->inputs->curp; ?>
                    <?php echo $controlador->inputs->rfc; ?>
                    <?php echo $controlador->inputs->apellido_paterno; ?>
                    <?php echo $controlador->inputs->apellido_materno; ?>
                    <?php echo $controlador->inputs->nombre; ?>
                    <?php echo $controlador->inputs->inm_comprador_id; ?>
                    <?php echo $controlador->inputs->inm_comprador_id; ?>
                    <?php echo $controlador->inputs->seccion_retorno; ?>
                    <?php echo $controlador->inputs->btn_action_next; ?>
                    <?php echo $controlador->inputs->id_retorno; ?>

                        <div class="col-md-12">
                            <hr>
                            <h4>6. DATOS DE IDENTIFICACIÓN QUE SERÁN VALIDADOS (OBLIGATORIOS EN CRÉDITO CONYUGAL, FAMILIAR O CORRESIDENCIAL)</h4>
                            <hr>
                        </div>

                        <?php echo $controlador->inputs->inm_co_acreditado->nss; ?>
                        <?php echo $controlador->inputs->inm_co_acreditado->curp; ?>
                        <?php echo $controlador->inputs->inm_co_acreditado->rfc; ?>
                        <?php echo $controlador->inputs->inm_co_acreditado->apellido_paterno; ?>
                        <?php echo $controlador->inputs->inm_co_acreditado->apellido_materno; ?>
                        <?php echo $controlador->inputs->inm_co_acreditado->nombre; ?>
                        <?php echo $controlador->inputs->inm_co_acreditado->lada; ?>
                        <?php echo $controlador->inputs->inm_co_acreditado->numero; ?>
                        <?php echo $controlador->inputs->inm_co_acreditado->celular; ?>

                        <div class="control-group col-sm-6">
                            <label class="control-label" for="genero">Genero</label>
                            <label class="form-check-label chk">
                                <input type="radio" name="genero" value="M" class="form-check-input" id="genero"
                                       title="Genero" checked>
                                M
                            </label>
                            <label class="form-check-label chk">
                                <input type="radio" name="genero" value="F" class="form-check-input" id="genero"
                                       title="Genero">
                                F
                            </label>
                        </div>

                        <?php echo $controlador->inputs->inm_co_acreditado->correo; ?>

                        <div class="col-md-12">
                            <hr>
                            <h4>7. DATOS DE LA EMPRESA O PATRÓN CO ACREDITADO</h4>
                            <hr>
                        </div>

                        <?php echo $controlador->inputs->inm_co_acreditado->nombre_empresa_patron; ?>
                        <?php echo $controlador->inputs->inm_co_acreditado->nrp; ?>
                        <?php echo $controlador->inputs->inm_co_acreditado->lada_nep; ?>
                        <?php echo $controlador->inputs->inm_co_acreditado->numero_nep; ?>
                        <?php echo $controlador->inputs->inm_co_acreditado->extension_nep; ?>


                    <?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>
                    </form>
                    <?php echo $controlador->buttons['modifica']; ?>
                </div>

            </div>


            <div class="container">
                <div class="row">
                    <div class="col-md-12">

                        <div class="widget widget-box box-container widget-mylistings">
                            <div class="widget-header" style="display: flex;justify-content: space-between;align-items: center;">
                                <h2>Co Acreditados</h2>
                            </div>

                            <div class="table table-responsive">
                                <table class='table table-striped data-partida'>
                                    <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>NSS</th>
                                        <th>CURP</th>
                                        <th>Nombre</th>
                                        <th>Apellido Paterno</th>
                                        <th>Apellido Materno</th>
                                    <tr>
                                    </thead>
                                    <tbody>
                                    <?php    foreach ($controlador->inm_co_acreditados as $inm_co_acreditado){ ?>
                                    <tr>
                                        <td><?php echo $inm_co_acreditado['inm_co_acreditado_id'] ?></td>
                                        <td><?php echo $inm_co_acreditado['inm_co_acreditado_nss'] ?></td>
                                        <td><?php echo $inm_co_acreditado['inm_co_acreditado_curp'] ?></td>
                                        <td><?php echo $inm_co_acreditado['inm_co_acreditado_nombre'] ?></td>
                                        <td><?php echo $inm_co_acreditado['inm_co_acreditado_apellido_paterno'] ?></td>
                                        <td><?php echo $inm_co_acreditado['inm_co_acreditado_apellido_materno'] ?></td>
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


















