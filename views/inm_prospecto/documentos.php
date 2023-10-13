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
    <div class="container">
        <div class="row">
            <div class="col-lg-12 table-responsive">
                <table class="table table-striped">
                    <thead>

                         <th>Tipo de Documento</th>
                         <th>Descarga</th>
                         <th>Vista Previa</th>
                         <th>ZIP</th>
                         <th>Elimina</th>

                    </thead>
                    <tbody>
                    <?php
                    foreach ($controlador->inm_conf_docs_prospecto as $doc_tipo_documento){ ?>

                    <tr>
                        <td><?php echo $doc_tipo_documento['doc_tipo_documento_descripcion']; ?></td>
                        <td><?php echo $doc_tipo_documento['descarga']; ?></td>
                        <td><?php echo $doc_tipo_documento['vista_previa']; ?></td>
                        <td><?php echo $doc_tipo_documento['descarga_zip']; ?></td>
                        <td><?php echo $doc_tipo_documento['elimina_bd']; ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>