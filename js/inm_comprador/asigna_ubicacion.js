let url = getAbsolutePath();
let registro_id = getParameterByName('registro_id');
let session_id = getParameterByName('session_id');

let inm_ubicacion_id_sl = $("#inm_ubicacion_id");
let precio_operacion_sl = $("#precio_operacion");


inm_ubicacion_id_sl.change(function(){
    let inm_ubicacion_precio = $(this).find(':selected').data('inm_ubicacion_precio')
    precio_operacion_sl.val(inm_ubicacion_precio);

});










