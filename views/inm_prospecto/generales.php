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
                    <br>
                    <label>CURP: </label> <?php echo $controlador->registro->inm_prospecto->inm_prospecto_curp; ?>
                    <label>RFC: </label> <?php echo $controlador->registro->inm_prospecto->inm_prospecto_rfc; ?>
                    <br>
                    <label>OCUPACION: </label> <?php echo $controlador->registro->inm_prospecto->inm_ocupacion_descripcion; ?>
                    <label>TELEFONO CELULAR: </label> <?php echo $controlador->registro->inm_prospecto->inm_prospecto_cel_com; ?>
                    <br>
                    <label>TELEFONO CASA: </label> <?php echo $controlador->registro->inm_prospecto->inm_prospecto_telefono_casa; ?>
                    <label>EMAIL: </label> <?php echo $controlador->registro->inm_prospecto->inm_prospecto_correo_com; ?>
                    <br>
                    <label>EMPRESA: </label> <?php echo $controlador->registro->inm_prospecto->inm_prospecto_nombre_empresa_patron; ?>
                    <br>
                    <label>REGISTRO PATRONAL: </label> <?php echo $controlador->registro->inm_prospecto->inm_prospecto_nrp_nep; ?>
                    <label>TELEFONO: </label> <?php echo $controlador->registro->inm_prospecto->inm_prospecto_telefono_empresa; ?>
                    <br>
                    <label>CORREO ELECTRONICS: </label> <?php echo $controlador->registro->inm_prospecto->inm_prospecto_correo_empresa; ?>
                </div>

                <div  class="col-md-12">
                    <h4>CONYUGE::</h4>
                    <label>NOMBRE: </label> <?php echo $controlador->registro->inm_conyuge->inm_conyuge_nombre_completo; ?>

                </div>

            </div>

        </div>
    </div>
    <br>

</main>