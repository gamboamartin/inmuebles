<?php /** @var  gamboamartin\inmuebles\controllers\controlador_inm_ubicacion $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<main class="main section-color-primary">
    <div class="container">

        <div class="row">

            <div class="col-lg-12">

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <?php include (new views())->ruta_templates."head/title.php"; ?>
                    <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                    <?php include (new views())->ruta_templates."mensajes.php"; ?>

                    <div id="pestanas">
                        <ul id=lista>
                            <li id="pestana1"><a href='javascript:cambiarPestanna(pestanas,pestana1);'>DETENIDO</a></li>
                            <li id="pestana2"><a href='javascript:cambiarPestanna(pestanas,pestana2);'>ASIGNADO</a></li>
                            <li id="pestana3"><a href='javascript:cambiarPestanna(pestanas,pestana3);'>EN AVALUO</a></li>
                            <li id="pestana4"><a href='javascript:cambiarPestanna(pestanas,pestana4);'>POR INGRESAR</a></li>
                            <li id="pestana5"><a href='javascript:cambiarPestanna(pestanas,pestana5);'>INGRESADO</a></li>
                            <li id="pestana6"><a href='javascript:cambiarPestanna(pestanas,pestana6);'>AUTORIZADO</a></li>
                            <li id="pestana7"><a href='javascript:cambiarPestanna(pestanas,pestana7);'>POR FIRMAR</a></li>
                            <li id="pestana8"><a href='javascript:cambiarPestanna(pestanas,pestana8);'>ESCRITURADO</a></li>
                            <li id="pestana9"><a href='javascript:cambiarPestanna(pestanas,pestana9);'>COTEJADO</a></li>
                        </ul>
                    </div>
                    <body onload="javascript:cambiarPestanna(pestanas,pestana1);">
                    <div id="contenidopestanas">
                        <div class="conten" id="cpestana1">
                            <form method="post" action="<?php echo $controlador->link_rel_ubi_comp_alta_bd; ?>" class="form-additional">

                                <?php echo $controlador->inputs->inm_ubicacion_id; ?>
                                <?php echo $controlador->inputs->precio_operacion; ?>

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

                                <?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>
                            </form>
                        </div>
                        <div class="conten" id="cpestana2">
                            <form method="post" action="<?php echo $controlador->link_inm_rel_cliente_valuador_alta_bd; ?>"
                                  class="form-additional">
                                <?php echo $controlador->inputs->com_cliente_id; ?>
                                <?php echo $controlador->inputs->inm_valuador_id; ?>
                                <?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>
                            </form>
                        </div>
                        <div class="conten" id="cpestana3">
                            <form method="post" action="<?php echo $controlador->link_asigna_avaluo_bd; ?>"
                                  class="form-additional" enctype="multipart/form-data">
                                <?php echo $controlador->inputs->com_cliente_id; ?>
                                <?php echo $controlador->inputs->mts_terrenos; ?>
                                <?php echo $controlador->inputs->mts_construidos; ?>
                                <?php echo $controlador->inputs->valor_avaluo; ?>
                                <?php echo $controlador->inputs->documento; ?>

                                <?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>
                            </form>
                        </div>
                        <div class="conten" id="cpestana4">
                            <?php echo $controlador->inputs->com_cliente_id; ?>

                            <a role="button" title="Solicitud Infonavit" href="index.php?seccion=inm_comprador&amp;accion=solicitud_infonavit&amp;registro_id=55&amp;session_id=5514223136&amp;adm_menu_id=45" class="btn btn-warning " style="margin-left: 2px; margin-bottom: 2px; ">Solicitud Infonavit</a>                        </div>
                        <div class="conten" id="cpestana5">
                            <form method="post" action="<?php echo $controlador->link_asigna_avaluo_bd; ?>"
                                  class="form-additional" enctype="multipart/form-data">

                                <?php echo $controlador->inputs->documento_sic; ?>
                                <?php echo $controlador->inputs->documento_constancia_credito; ?>

                                <?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>
                            </form>                        </div>
                        <div class="conten" id="cpestana6">
                            <form method="post" action="<?php echo $controlador->link_asigna_firma_bd; ?>"
                                  class="form-additional" enctype="multipart/form-data">
                                <?php echo $controlador->inputs->documento_anexos; ?>
                                <?php echo $controlador->inputs->documento_instruccion_credito; ?>
                                <?php echo $controlador->inputs->documento_notificacion_descuento; ?>
                                <?php echo $controlador->inputs->documento_isr_notaria; ?>
                                <?php echo $controlador->inputs->isr; ?>
                                <?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>
                            </form>                        </div>
                        <div class="conten" id="cpestana7">
                            <form method="post" action="<?php echo $controlador->link_asigna_escritura_bd; ?>"
                                  class="form-additional" enctype="multipart/form-data">
                                <?php echo $controlador->inputs->documento_validacion_poder; ?>
                                <?php echo $controlador->inputs->documento_acuse_patron; ?>
                                <?php echo $controlador->inputs->documento_escrituras; ?>
                                <?php echo $controlador->inputs->numero_escritura; ?>
                                <?php echo $controlador->inputs->fecha; ?>

                                <?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>
                            </form>                        </div>
                        <div class="conten" id="cpestana8">
                            Contenido de la pestaña 2
                        </div>
                        <div class="conten" id="cpestana9">
                            Contenido de la pestaña 1
                        </div>

                </div>
            </div>
        </div>


</main>


















