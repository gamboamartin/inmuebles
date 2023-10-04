let url = getAbsolutePath();
let registro_id = getParameterByName('registro_id');
let session_id = getParameterByName('session_id');



let nombre_ct = $("#nombre");
let apellido_paterno_ct = $("#apellido_paterno");
let apellido_materno_ct = $("#apellido_materno");
let lada_com_ct = $("#lada_com");
let numero_com_ct = $("#numero_com");
let cel_com_ct = $("#cel_com_ct");
let correo_com_ct = $("#correo_com");
let razon_social_ct = $("#razon_social");

nombre_ct.change(function() {
    limpia_txt($(this));
});
apellido_paterno_ct.change(function() {
    limpia_txt($(this));
});
apellido_materno_ct.change(function() {
    limpia_txt($(this));
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












