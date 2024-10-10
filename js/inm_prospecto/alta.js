let url = getAbsolutePath();
let registro_id = getParameterByName('registro_id');
let session_id = getParameterByName('session_id');

let com_medio_prospeccion_id_sl = $("#com_medio_prospeccion_id");
let liga_red_social = $("#liga_red_social");

com_medio_prospeccion_id_sl.change(function(){
    com_medio_prospeccion_id = $(this).val();

    let selected = $(this).find('option:selected');
    let es_red_social = selected.data('com_medio_prospeccion_es_red_social');

    if(es_red_social === 'activo'){
        liga_red_social.prop('disabled', false);
        liga_red_social.prop('required', true);
    }else {
        liga_red_social.val("");
        liga_red_social.prop('disabled', true);
        liga_red_social.prop('required', false);
        nombre_ct.prop('required', true);
        apellido_paterno_ct.prop('required', true);
        razon_social_ct.prop('required', true);
    }
});

let nombre_ct = $("#nombre");
let apellido_paterno_ct = $("#apellido_paterno");
let apellido_materno_ct = $("#apellido_materno");
let lada_com_ct = $("#lada_com");
let numero_com_ct = $("#numero_com");
let cel_com_ct = $("#cel_com");
let correo_com_ct = $("#correo_com");
let razon_social_ct = $("#razon_social");

let nombre = '';
let apellido_paterno = '';
let apellido_materno = '';
let razon_social = '';

liga_red_social.change(function (){
    nombre_ct.prop('required', false);
    apellido_paterno_ct.prop('required', false);
    razon_social_ct.prop('required', false);
});

document.getElementById("form_inm_prospecto_alta").addEventListener("submit", valida_registro);

function valida_registro(event) {
    event.preventDefault();

    const form = document.getElementById("form_inm_prospecto_alta");

    let url = "index.php?seccion=inm_prospecto&ws=1&accion=valida_prioridad&liga_red_social="+liga_red_social.val()+"&lada_com="+lada_com_ct.val()+"&numero_com="+numero_com_ct.val()+"&cel_com="+cel_com_ct.val()+"&correo_com="+correo_com_ct.val()+"&session_id="+session_id;
    $.ajax({
        type: 'GET',
        url: url,
    }).done(function( data ) {  // Función que se ejecuta si todo ha ido bien
        if(data.resultado_completo){
            alert('No hay datos de contacto');
        }else{
            form.submit();
        }
    }).fail(function (jqXHR, textStatus, errorThrown){ // Función que se ejecuta si algo ha ido mal
        alert('Error al ejecutar');
    });
}
nombre_ct.change(function() {
    limpia_txt($(this));
    nombre = $(this).val().trim();
    razon_social = nombre+' '+apellido_paterno+' '+apellido_materno;
    razon_social_ct.val(razon_social.trim());

});
apellido_paterno_ct.change(function() {
    limpia_txt($(this));
    apellido_paterno = $(this).val().trim();
    razon_social = nombre+' '+apellido_paterno+' '+apellido_materno;
    razon_social_ct.val(razon_social.trim());
});
apellido_materno_ct.change(function() {
    limpia_txt($(this));
    apellido_materno = $(this).val().trim();
    razon_social = nombre+' '+apellido_paterno+' '+apellido_materno;
    razon_social_ct.val(razon_social.trim());
});
lada_com_ct.change(function() {
    limpia_txt($(this));
    limpia_number($(this));
});
numero_com_ct.change(function() {
    limpia_txt($(this));
    limpia_number($(this));
});
cel_com_ct.change(function() {
    limpia_txt($(this));
    limpia_number($(this));
});
correo_com_ct.change(function() {
    limpia_txt($(this));
    limpia_email($(this));
});
razon_social_ct.change(function() {
    limpia_txt($(this));
});

function limpia_txt(container){
    let value = container.val().trim();
    value = value.toUpperCase();
    value = value.replace('  ',' ');
    value = value.replace('  ',' ');
    value = value.replace('  ',' ');
    value = value.replace('  ',' ');
    container.val(value);
}
function limpia_number(container){
    let value = container.val().trim();
    value = value.toUpperCase();
    value = value.replace(' ','');
    value = value.replace(' ','');
    value = value.replace(' ','');
    value = value.replace(' ','');
    value = value.replace(' ','');
    value = value.replace(' ','');
    container.val(value);
}

function limpia_email(container){
    let value = container.val().trim();
    value = value.toLowerCase();
    value = value.replace(' ','');
    value = value.replace(' ','');
    value = value.replace(' ','');
    value = value.replace(' ','');
    value = value.replace(' ','');
    value = value.replace(' ','');
    container.val(value);
}












