<?php /** @var  gamboamartin\inmuebles\controllers\controlador_inm_ubicacion $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<main class="main section-color-primary">
    <div class="container">

        <div class="row">

            <div class="col-lg-12">

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">

                    <div id="pestanas">
                        <ul id=lista>
                            <li id="pestana1"><a href='javascript:cambiarPestanna(pestanas,pestana1);'>HTML</a></li>
                            <li id="pestana2"><a href='javascript:cambiarPestanna(pestanas,pestana2);'>CSS</a></li>
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
                    </div>
                </div>
            </div>
        </div>


</main>


















