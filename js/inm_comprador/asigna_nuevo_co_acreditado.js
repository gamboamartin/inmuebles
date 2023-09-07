let url = getAbsolutePath();
let registro_id = getParameterByName('registro_id');
let session_id = getParameterByName('session_id');


let nombre_empresa_patron_ct = $("#nombre_empresa_patron");
let nrp_nep_ct = $("#nrp_nep");
let curp_ct = $(".inm_co_acreditado_curp");
let rfc_ct = $(".inm_co_acreditado_rfc");
let apellido_paterno_ct = $(".inm_co_acreditado_apellido_paterno");
let apellido_materno_ct = $(".inm_co_acreditado_apellido_materno");
let nombre_ct = $(".inm_co_acreditado_nombre");


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

rfc_ct.change(function(){

    let rfc = $(this).val();
    rfc = rfc.toUpperCase()
    rfc_ct.val(rfc);

});

nrp_nep_ct.change(function(){

    let nrp_nep = $(this).val();
    nrp_nep = nrp_nep.toUpperCase()
    nrp_nep_ct.val(nrp_nep);

});

curp_ct.change(function(){
    let curp = $(this).val();
    curp = curp.toUpperCase()
    curp_ct.val(curp);

});











