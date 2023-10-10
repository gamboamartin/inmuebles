let url = getAbsolutePath();
let registro_id = getParameterByName('registro_id');
let session_id = getParameterByName('session_id');

let sl_dp_pais_id = $("#dp_pais_id");
let sl_dp_estado_id = $("#dp_estado_id");
let sl_dp_estado_nacimiento_id = $("#dp_estado_nacimiento_id");
let sl_dp_municipio_id = $("#dp_municipio_id");
let sl_dp_municipio_nacimiento_id = $("#dp_municipio_nacimiento_id");
let sl_dp_cp_id = $("#dp_cp_id");
let sl_dp_colonia_postal_id = $("#dp_colonia_postal_id");
let sl_inm_plazo_credito_sc_id = $("#inm_plazo_credito_sc_id");
let sl_inm_tipo_discapacidad_id = $("#inm_tipo_discapacidad_id");
let sl_inm_persona_discapacidad_id = $("#inm_persona_discapacidad_id");

let in_descuento_pension_alimenticia_dh = $("#descuento_pension_alimenticia_dh");
let in_descuento_pension_alimenticia_fc = $("#descuento_pension_alimenticia_fc");
let in_monto_credito_solicitado_dh = $("#monto_credito_solicitado_dh");
let in_monto_ahorro_voluntario = $("#monto_ahorro_voluntario");

let chk_es_segundo_credito = $(".es_segundo_credito");
let chk_con_discapacidad = $(".con_discapacidad");

let nombre_empresa_patron_ct = $("#nombre_empresa_patron");
let nrp_nep_ct = $("#nrp_nep");
let curp_ct = $("#curp");
let rfc_ct = $("#rfc");
let apellido_paterno_ct = $("#apellido_paterno");
let apellido_materno_ct = $("#apellido_materno");
let nombre_ct = $("#nombre");
let numero_exterior_ct = $("#numero_exterior");
let numero_interior_ct = $("#numero_interior");
let lada_com_ct = $("#lada_com");
let lada_nep_ct = $("#lada_nep");
let numero_nep_ct = $("#numero_nep");
let extension_nep_ct = $("#extension_nep");
let nss_ct = $("#nss");
let numero_com_ct = $("#numero_com");
let cel_com_ct = $("#cel_com");
let correo_com_ct = $("#correo_com");


let apartado_1 = $("#apartado_1");
let apartado_2 = $("#apartado_2");
let apartado_3 = $("#apartado_3");
let apartado_4 = $("#apartado_4");
let apartado_5 = $("#apartado_5");
let apartado_13 = $("#apartado_13");
let apartado_14 = $("#apartado_14");

let collapse_a1 = $("#collapse_a1");
let collapse_a2 = $("#collapse_a2");
let collapse_a3 = $("#collapse_a3");
let collapse_a4 = $("#collapse_a4");
let collapse_a5 = $("#collapse_a5");
let collapse_a13 = $("#collapse_a13");
let collapse_a14 = $("#collapse_a14");



let dp_pais_id = -1;
let dp_estado_id = -1;
let dp_municipio_id = -1;
let dp_cp_id = -1;
let dp_colonia_postal_id = -1;


in_descuento_pension_alimenticia_dh.change(function() {
    let value = $(this).val().trim();
    $(this).val(value);

});

in_descuento_pension_alimenticia_fc.change(function() {
    let value = $(this).val().trim();
    $(this).val(value);

});

in_monto_credito_solicitado_dh.change(function() {
    let value = $(this).val().trim();
    $(this).val(value);

});

in_monto_ahorro_voluntario.change(function() {
    let value = $(this).val().trim();
    $(this).val(value);

});

lada_nep_ct.change(function() {
    let value = $(this).val().trim();
    value = value.replace(' ','');
    value = value.replace(' ','');
    value = value.replace(' ','');
    $(this).val(value);

});

numero_nep_ct.change(function() {
    let value = $(this).val().trim();
    value = value.replace(' ','');
    value = value.replace(' ','');
    value = value.replace(' ','');
    $(this).val(value);

});

extension_nep_ct.change(function() {
    let value = $(this).val().trim();
    value = value.replace(' ','');
    value = value.replace(' ','');
    value = value.replace(' ','');
    $(this).val(value);

});

nss_ct.change(function() {
    let value = $(this).val().trim();
    value = value.replace(' ','');
    value = value.replace(' ','');
    value = value.replace(' ','');
    $(this).val(value);

});

curp_ct.change(function() {
    let value = $(this).val().trim();
    value = value.replace(' ','');
    value = value.replace(' ','');
    value = value.replace(' ','');
    $(this).val(value);

});


rfc_ct.change(function() {
    let value = $(this).val().trim();
    value = value.replace(' ','');
    value = value.replace(' ','');
    value = value.replace(' ','');
    $(this).val(value);

});

apellido_paterno_ct.change(function() {
    let value = $(this).val().trim();
    $(this).val(value);

});

apellido_materno_ct.change(function() {
    let value = $(this).val().trim();
    $(this).val(value);

});

nombre_ct.change(function() {
    let value = $(this).val().trim();
    $(this).val(value);

});

numero_exterior_ct.change(function() {
    let value = $(this).val().trim();
    $(this).val(value);

});

numero_interior_ct.change(function() {
    let value = $(this).val().trim();
    $(this).val(value);

});

lada_com_ct.change(function() {
    let value = $(this).val().trim();
    value = value.replace(' ','');
    value = value.replace(' ','');
    value = value.replace(' ','');
    $(this).val(value);

});

numero_com_ct.change(function() {
    let value = $(this).val().trim();
    value = value.replace(' ','');
    value = value.replace(' ','');
    value = value.replace(' ','');
    $(this).val(value);

});

cel_com_ct.change(function() {
    let value = $(this).val().trim();
    value = value.replace(' ','');
    value = value.replace(' ','');
    value = value.replace(' ','');
    $(this).val(value);

});

correo_com_ct.change(function() {
    let value = $(this).val().trim();
    value = value.replace(' ','');
    value = value.toLowerCase();
    $(this).val(value);

});

let btn = $(".btn-success");

apartado_1.hide();
apartado_2.hide();
apartado_3.hide();
apartado_4.hide();
apartado_5.hide();
apartado_13.hide();
apartado_14.hide();
collapse_a1.click(function() {
    apartado_1.toggle();

});
collapse_a2.click(function() {
    apartado_2.toggle();

});
collapse_a3.click(function() {
    apartado_3.toggle();

});
collapse_a4.click(function() {
    apartado_4.toggle();

});
collapse_a5.click(function() {
    apartado_5.toggle();

});
collapse_a13.click(function() {
    apartado_13.toggle();

});

collapse_a14.click(function() {
    apartado_14.toggle();

});

let todo_aculto = true;


$("#collapse_all").click(function() {
    if(todo_aculto){
        apartado_1.show();
        apartado_2.show();
        apartado_3.show();
        apartado_4.show();
        apartado_5.show();
        apartado_13.show();
        apartado_14.show();
        todo_aculto = false;
    }
    else{
        apartado_1.hide();
        apartado_2.hide();
        apartado_3.hide();
        apartado_4.hide();
        apartado_5.hide();
        apartado_13.hide();
        apartado_14.hide();
        todo_aculto = true;
    }


});

btn.click(function() {
    apartado_1.show();
    apartado_2.show();
    apartado_3.show();
    apartado_4.show();
    apartado_5.show();
    apartado_13.show();
    apartado_14.show();

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
    nombre_empresa_patron = nombre_empresa_patron.toUpperCase().trim();
    nombre_empresa_patron_ct.val(nombre_empresa_patron);

});

rfc_ct.change(function(){

    let rfc = $(this).val();
    rfc = rfc.toUpperCase()
    rfc_ct.val(rfc);

});

nrp_nep_ct.change(function(){

    let nrp_nep = $(this).val();
    nrp_nep = nrp_nep.toUpperCase().trim().replace(' ','');
    nrp_nep = nrp_nep.replace(' ','');

    nrp_nep_ct.val(nrp_nep);

});

curp_ct.change(function(){

    let curp = $(this).val();
    curp = curp.toUpperCase()
    curp_ct.val(curp);

});

sl_inm_plazo_credito_sc_id.prop('disabled',true);

chk_es_segundo_credito.change(function(){
    let es_segundo_credito = $(this).val();
    if(es_segundo_credito === 'SI'){
        sl_inm_plazo_credito_sc_id.prop('disabled',false);
    }
    else{
        sl_inm_plazo_credito_sc_id.val(7);
        sl_inm_plazo_credito_sc_id.prop('disabled',true);
    }
    sl_inm_plazo_credito_sc_id.selectpicker('refresh');
});

chk_con_discapacidad.change(function(){
    let con_discapacidad = $(this).val();
    if(con_discapacidad === 'SI'){
        sl_inm_tipo_discapacidad_id.prop('disabled',false);
        sl_inm_persona_discapacidad_id.prop('disabled',false);
    }
    else{
        sl_inm_tipo_discapacidad_id.val(5);
        sl_inm_tipo_discapacidad_id.prop('disabled',true);

        sl_inm_persona_discapacidad_id.val(6);
        sl_inm_persona_discapacidad_id.prop('disabled',true);
    }
    sl_inm_tipo_discapacidad_id.selectpicker('refresh');
    sl_inm_persona_discapacidad_id.selectpicker('refresh');
});
sl_dp_pais_id.change(function(){
    dp_pais_id = $(this).val();
    dp_asigna_estados(dp_pais_id);
});

sl_dp_estado_id.change(function(){
    dp_estado_id = $(this).val();
    dp_asigna_municipios(dp_estado_id);
});

sl_dp_estado_nacimiento_id.change(function(){
    let dp_estado_nacimiento_id = $(this).val();
    dp_asigna_municipios(dp_estado_nacimiento_id,'','#dp_municipio_nacimiento_id');
});

sl_dp_municipio_id.change(function(){
    dp_municipio_id = sl_dp_municipio_id.val();
    dp_asigna_cps(dp_municipio_id);
});

sl_dp_cp_id.change(function(){
    dp_cp_id = sl_dp_cp_id.val();
    dp_asigna_colonias_postales(dp_cp_id);
});

sl_dp_colonia_postal_id.change(function(){
    dp_colonia_postal_id = sl_dp_colonia_postal_id.val();
    dp_asigna_calles_pertenece(dp_colonia_postal_id);
});

function dp_asigna_calles_pertenece(dp_colonia_postal_id = '',dp_calle_pertenece_id = ''){

    let sl_dp_calle_pertenece_id = $("#dp_calle_pertenece_id");

    let url = "index.php?seccion=dp_calle_pertenece&ws=1&accion=get_calle_pertenece&dp_colonia_postal_id="+dp_colonia_postal_id+"&session_id="+session_id;
    $.ajax({
        type: 'GET',
        url: url,
    }).done(function( data ) {  // Función que se ejecuta si todo ha ido bien
        console.log(data);
        sl_dp_calle_pertenece_id.empty();
        integra_new_option("#dp_calle_pertenece_id",'Seleccione una calle','-1');
        $.each(data.registros, function( index, dp_calle_pertenece ) {
            integra_new_option("#dp_calle_pertenece_id",dp_calle_pertenece.dp_calle_descripcion,dp_calle_pertenece.dp_calle_pertenece_id);
        });
        sl_dp_calle_pertenece_id.val(dp_calle_pertenece_id);
        sl_dp_calle_pertenece_id.selectpicker('refresh');
    }).fail(function (jqXHR, textStatus, errorThrown){ // Función que se ejecuta si algo ha ido mal
        alert('Error al ejecutar');
    });
}
function dp_asigna_colonias_postales(dp_cp_id = '',dp_colonia_postal_id = ''){

    let sl_dp_colonia_postal_id = $("#dp_colonia_postal_id");

    let url = "index.php?seccion=dp_colonia_postal&ws=1&accion=get_colonia_postal&dp_cp_id="+dp_cp_id+"&session_id="+session_id;
    $.ajax({
        type: 'GET',
        url: url,
    }).done(function( data ) {  // Función que se ejecuta si todo ha ido bien
        console.log(data);
        sl_dp_colonia_postal_id.empty();
        integra_new_option("#dp_colonia_postal_id",'Seleccione una colonia','-1');
        $.each(data.registros, function( index, dp_colonia_postal ) {
            integra_new_option("#dp_colonia_postal_id",dp_colonia_postal.dp_colonia_descripcion,dp_colonia_postal.dp_colonia_postal_id);
        });
        sl_dp_colonia_postal_id.val(dp_colonia_postal_id);
        sl_dp_colonia_postal_id.selectpicker('refresh');
    }).fail(function (jqXHR, textStatus, errorThrown){ // Función que se ejecuta si algo ha ido mal
        alert('Error al ejecutar');
    });
}
function dp_asigna_cps(dp_municipio_id = '',dp_cp_id = ''){

    let sl_dp_cp_id = $("#dp_cp_id");

    let url = "index.php?seccion=dp_cp&ws=1&accion=get_cp&dp_municipio_id="+dp_municipio_id+"&session_id="+session_id;
    $.ajax({
        type: 'GET',
        url: url,
    }).done(function( data ) {  // Función que se ejecuta si todo ha ido bien
        console.log(data);
        sl_dp_cp_id.empty();
        integra_new_option("#dp_cp_id",'Seleccione un cp','-1');
        $.each(data.registros, function( index, dp_cp ) {
            integra_new_option("#dp_cp_id",dp_cp.dp_cp_descripcion,dp_cp.dp_cp_id);
        });
        sl_dp_cp_id.val(dp_cp_id);
        sl_dp_cp_id.selectpicker('refresh');
    }).fail(function (jqXHR, textStatus, errorThrown){ // Función que se ejecuta si algo ha ido mal
        alert('Error al ejecutar');
    });
}

function dp_asigna_estados(dp_pais_id = '',dp_estado_id = ''){

    let sl_dp_estado_id = $("#dp_estado_id");

    let url = "index.php?seccion=dp_estado&ws=1&accion=get_estado&dp_pais_id="+dp_pais_id+"&session_id="+session_id;

    $.ajax({
        type: 'GET',
        url: url,
    }).done(function( data ) {  // Función que se ejecuta si todo ha ido bien
        console.log(data);
        sl_dp_estado_id.empty();
        integra_new_option("#dp_estado_id",'Seleccione un estado','-1');

        $.each(data.registros, function( index, dp_estado ) {
            integra_new_option("#dp_estado_id",dp_estado.dp_estado_descripcion,dp_estado.dp_estado_id);
        });
        sl_dp_estado_id.val(dp_estado_id);
        sl_dp_estado_id.selectpicker('refresh');
    }).fail(function (jqXHR, textStatus, errorThrown){ // Función que se ejecuta si algo ha ido mal
        alert('Error al ejecutar');
        console.log("The following error occured: "+ textStatus +" "+ errorThrown);
    });

}

function dp_asigna_municipios(dp_estado_id = '',dp_municipio_id = '', selector = '#dp_municipio_id'){

    let sl_dp_municipio_id = $(selector);

    let url = "index.php?seccion=dp_municipio&ws=1&accion=get_municipio&dp_estado_id="+dp_estado_id+"&session_id="+session_id;

    $.ajax({
        type: 'GET',
        url: url,
    }).done(function( data ) {  // Función que se ejecuta si todo ha ido bien
        console.log(data);
        sl_dp_municipio_id.empty();

        integra_new_option(selector,'Seleccione un municipio','-1');

        $.each(data.registros, function( index, dp_municipio ) {
            integra_new_option(selector,dp_municipio.dp_municipio_descripcion,dp_municipio.dp_municipio_id);
        });
        sl_dp_municipio_id.val(dp_municipio_id);
        sl_dp_municipio_id.selectpicker('refresh');
    }).fail(function (jqXHR, textStatus, errorThrown){ // Función que se ejecuta si algo ha ido mal
        alert('Error al ejecutar');
        console.log("The following error occured: "+ textStatus +" "+ errorThrown);
    });

}









