<?php /** @var  gamboamartin\inmuebles\controllers\controlador_inm_comprador $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<main class="main section-color-primary">
    <div class="container">

        <div class="row">

            <div class="col-lg-12">

                <div class="widget" >

                    <?php include (new views())->ruta_templates."head/title.php"; ?>
                    <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                    <?php include (new views())->ruta_templates."mensajes.php"; ?>

                </div>
                <div  class="col-md-12">
                    <h4>ACREDITADO:</h4>
                    <label>NOMBRE: </label> <?php echo $controlador->registro->inm_prospecto->inm_prospecto_nombre_completo; ?>
                    <br>
                    <label>LUGAR Y FECHA DE NACIMIENTO: </label> <?php echo $controlador->registro->inm_prospecto->inm_prospecto_lugar_fecha_nac; ?>
                    <br>
                    <label>EDAD: </label> <?php echo $controlador->registro->inm_prospecto->inm_prospecto_edad; ?> AÑOS
                    <label>ESTADO CIVIL: </label> <?php echo $controlador->registro->inm_prospecto->inm_estado_civil_descripcion; ?>
                    <label>NACIONALIDAD: </label> <?php echo $controlador->registro->inm_prospecto->inm_nacionalidad_descripcion; ?>
                </div>
            </div>

        </div>
    </div>
    <br>

</main>