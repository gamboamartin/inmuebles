<?php /** @var  gamboamartin\inmuebles\controllers\controlador_inm_ubicacion $controlador  controlador en ejecucion */ ?>
<div class="table table-responsive">
    <table class='table table-striped data-partida'>
        <thead>
        <tr>
            <th>Id</th>
            <th>Tipo Concepto</th>
            <th>Concepto</th>
            <th>Monto</th>
            <th>Fecha</th>
            <th>Ref</th>
            <th>Descripcion</th>
            <th>Acciones</th>
        <tr>
        </thead>
        <tbody>
        <?php    foreach ($controlador->inm_costos as $inm_costo){ ?>
        <tr>
            <td><?php echo $inm_costo['inm_costo_id'] ?></td>
            <td><?php echo $inm_costo['inm_tipo_concepto_descripcion'] ?></td>
            <td><?php echo $inm_costo['inm_concepto_descripcion'] ?></td>
            <td><?php echo $inm_costo['inm_costo_monto'] ?></td>
            <td><?php echo $inm_costo['inm_costo_fecha'] ?></td>
            <td><?php echo $inm_costo['inm_costo_referencia'] ?></td>
            <td><?php echo $inm_costo['inm_costo_descripcion'] ?></td>
            <td>
                <?php foreach ($inm_costo['acciones'] as $accion){ ?>
                <?php echo $accion ?>
                <?php } ?>
            </td>
        <tr>
            <?php }  ?>
        </tbody>
        <thead>
        <tr>
            <th colspan="8">Total: <?php echo $controlador->costo; ?></th>
        </tr>
        </thead>
    </table>
</div>
