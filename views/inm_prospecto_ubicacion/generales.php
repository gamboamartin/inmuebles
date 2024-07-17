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
                    <hr>
                    <h4>ACREDITADO:</h4>
                    <hr>
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
                    <label>SINDICATO: </label> <?php echo $controlador->registro->inm_prospecto->inm_sindicato_descripcion; ?>
                    <br>
                    <label>CORREO ELECTRONICO: </label> <?php echo $controlador->registro->inm_prospecto->inm_prospecto_correo_empresa; ?>
                </div>

                <div  class="col-md-12">
                    <hr>
                    <h4>CONYUGE:</h4>
                    <hr>
                    <label>NOMBRE: </label> <?php echo $controlador->registro->inm_conyuge->inm_conyuge_nombre_completo; ?>
                    <br>
                    <label>LUGAR Y FECHA DE NACIMIENTO: </label> <?php echo $controlador->registro->inm_conyuge->inm_conyuge_lugar_fecha_nac; ?>
                    <br>
                    <label>EDAD: </label> <?php echo $controlador->registro->inm_conyuge->inm_conyuge_edad; ?>
                    <label>ESTADO CIVIL: </label> <?php echo $controlador->registro->inm_conyuge->inm_conyuge_estado_civil; ?>
                    <label>NACIONALIDAD: </label> <?php echo $controlador->registro->inm_conyuge->inm_nacionalidad_descripcion; ?>
                    <br>
                    <label>CURP: </label> <?php echo $controlador->registro->inm_conyuge->inm_conyuge_curp; ?>
                    <label>RFC: </label> <?php echo $controlador->registro->inm_conyuge->inm_conyuge_rfc; ?>
                    <br>
                    <label>OCUPACION: </label> <?php echo $controlador->registro->inm_conyuge->inm_ocupacion_descripcion; ?>
                    <label>TELEFONO CASA: </label> <?php echo $controlador->registro->inm_conyuge->inm_conyuge_telefono_casa; ?>
                    <br>
                    <label>TELEFONO CELULAR: </label> <?php echo $controlador->registro->inm_conyuge->inm_conyuge_telefono_celular; ?>
                </div>

                <div  class="col-md-12">
                    <hr>
                    <h4>BENEFICIARIOS:</h4>
                    <hr>
                    <label>NOTA: </label>
                    Solo pueden designar como beneficiario a padres, hijos y/o esposa (o). En caso de unión
                    libre no se puede designar al cónyuge como beneficiario. Cuando el crédito es conyugal deben de
                    designar ambos a uno o dos beneficiarios.

                </div>

                <?php foreach ($controlador->registro->inm_tipo_beneficiarios as $inm_tipo_beneficiario){ ?>
                <div  class="col-md-12">
                    <hr>
                    <h4>BENEFICIARIOS <?php echo $inm_tipo_beneficiario->inm_tipo_beneficiario_descripcion; ?>:</h4>
                    <hr>
                    <?php foreach ($inm_tipo_beneficiario->inm_beneficiarios as $inm_beneficiario){ ?>
                    <label>NOMBRE: </label> <?php echo $inm_beneficiario->inm_beneficiario_nombre_completo; ?>
                    <label>PARENTESCO: </label> <?php echo $inm_beneficiario->inm_parentesco_descripcion; ?>
                        <br>
                    <?php } ?>

                </div>
                <?php } ?>

                <div  class="col-md-12">
                    <hr>
                    <h4>REFERENCIAS:</h4>
                    <hr>
                        <?php foreach ($controlador->registro->inm_referencias as $inm_referencia){ ?>
                        <label>NOMBRE: </label> <?php echo $inm_referencia->inm_referencia_prospecto_nombre_completo; ?>
                        <br>
                        <label>TELEFONO: </label> <?php echo $inm_referencia->inm_referencia_prospecto_telefono; ?>
                        <label>PARENTESCO: </label> <?php echo $inm_referencia->inm_parentesco_descripcion; ?>
                            <br>
                    <?php } ?>
                </div>



            </div>

        </div>
    </div>
    <br>

</main>