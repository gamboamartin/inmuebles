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
                            <li id="pestana10"><a href='javascript:cambiarPestanna(pestanas,pestana10);'>COTEJO APROBADO</a></li>
                        </ul>
                    </div>
                    <body onload="javascript:cambiarPestanna(pestanas,pestana1);">
                    <div id="contenidopestanas">
                        <div id="cpestana1">
                            Contenido de la pestaña 1
                        </div>
                        <div id="cpestana2">
                            Contenido de la pestaña 2
                        </div>
                        <div id="cpestana3">
                            Contenido de la pestaña 1
                        </div>
                        <div id="cpestana4">
                            Contenido de la pestaña 2
                        </div>
                        <div id="cpestana5">
                            Contenido de la pestaña 1
                        </div>
                        <div id="cpestana6">
                            Contenido de la pestaña 2
                        </div>
                        <div id="cpestana7">
                            Contenido de la pestaña 1
                        </div>
                        <div id="cpestana8">
                            Contenido de la pestaña 2
                        </div>
                        <div id="cpestana9">
                            Contenido de la pestaña 1
                        </div>
                        <div id="cpestana10">
                            Contenido de la pestaña 2
                        </div>


                </div>
            </div>
        </div>


</main>


















