let url = getAbsolutePath();
let registro_id = getParameterByName('registro_id');
let session_id = getParameterByName('session_id');


let curp_ct = $("#curp");
let rfc_ct = $("#rfc");
let apellido_paterno_ct = $("#apellido_paterno");
let apellido_materno_ct = $("#apellido_materno");
let nombre_ct = $("#nombre");
let nrp_ct = $("#nrp_nep");

let nombre_empresa_patron_ct = $("#nombre_empresa_patron");


curp_ct.change(function(){

    let curp = $(this).val();
    curp = curp.toUpperCase()
    curp_ct.val(curp);

});

rfc_ct.change(function(){

    let rfc = $(this).val();
    rfc = rfc.toUpperCase()
    rfc_ct.val(rfc);

});

apellido_paterno_ct.change(function(){

    let apellido_paterno = $(this).val();
    apellido_paterno = apellido_paterno.toUpperCase()
    apellido_paterno_ct.val(apellido_paterno);

});

nombre_ct.change(function(){

    let nombre = $(this).val();
    nombre = nombre.toUpperCase()
    nombre_ct.val(nombre);

});

apellido_materno_ct.change(function(){

    let apellido_materno = $(this).val();
    apellido_materno = apellido_materno.toUpperCase()
    apellido_materno_ct.val(apellido_materno);

});
nombre_empresa_patron_ct.change(function(){

    let nombre_empresa_patron = $(this).val();
    nombre_empresa_patron = nombre_empresa_patron.toUpperCase()
    nombre_empresa_patron_ct.val(nombre_empresa_patron);

});

nrp_ct.change(function(){

    let nrp = $(this).val();
    nrp = nrp.toUpperCase()
    nrp_ct.val(nrp);

});

