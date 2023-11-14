let url = getAbsolutePath();
let registro_id = getParameterByName('registro_id');
let session_id = getParameterByName('session_id');

let sl_inm_plazo_credito_sc_id = $("#inm_plazo_credito_sc_id");


let nombre_ct = $("#nombre");
let apellido_paterno_ct = $("#apellido_paterno");
let apellido_materno_ct = $("#apellido_materno");
let lada_com_ct = $("#lada_com");
let numero_com_ct = $("#numero_com");
let cel_com_ct = $("#cel_com_ct");
let correo_com_ct = $("#correo_com");
let razon_social_ct = $("#razon_social");
let sub_cuenta_ct = $("#sub_cuenta");
let monto_final_ct = $("#monto_final");
let descuento_ct = $("#descuento");
let puntos_ct = $("#puntos");

let conyuge_nombre_ct = $(".conyuge_nombre");
let conyuge_apellido_materno_ct = $(".conyuge_apellido_materno");
let conyuge_apellido_paterno_ct = $(".conyuge_apellido_paterno");
let conyuge_curp_ct = $(".conyuge_curp");
let conyuge_rfc_ct = $(".conyuge_rfc");
let beneficiario_nombre_ct = $(".beneficiario_nombre");
let beneficiario_apellido_paterno_ct = $(".beneficiario_apellido_paterno");
let beneficiario_apellido_materno_ct = $(".beneficiario_apellido_materno");

let referencia_nombre_ct = $(".referencia_nombre");
let referencia_apellido_paterno_ct = $(".referencia_apellido_paterno");
let referencia_apellido_materno_ct = $(".referencia_apellido_materno");
let referencia_lada_ct = $(".referencia_lada");
let referencia_numero_ct = $(".referencia_numero");
let referencia_celular_ct = $(".referencia_celular");
let referencia_numero_dom_ct = $(".referencia_numero_dom");

referencia_nombre_ct.change(function() {
    limpia_txt($(this));
});
referencia_apellido_paterno_ct.change(function() {
    limpia_txt($(this));
});
referencia_apellido_materno_ct.change(function() {
    limpia_txt($(this));
});
referencia_lada_ct.change(function() {
    limpia_txt($(this));
});
referencia_numero_ct.change(function() {
    limpia_txt($(this));
});
referencia_celular_ct.change(function() {
    limpia_txt($(this));
});
referencia_numero_dom_ct.change(function() {
    limpia_txt($(this));
});


let chk_es_segundo_credito = $(".es_segundo_credito");


let sl_dp_pais_id = $("#dp_pais_id");
let sl_dp_estado_id = $("#dp_estado_id");
let sl_conyuge_dp_estado_id = $("#conyuge_dp_estado_id");
let sl_conyuge_dp_municipio_id = $("#conyuge_dp_municipio_id");
let sl_dp_municipio_id = $("#dp_municipio_id");
let sl_dp_cp_id = $("#dp_cp_id");
let sl_dp_colonia_postal_id = $("#dp_colonia_postal_id");
let sl_dp_calle_pertenece_id = $("#dp_calle_pertenece_id");
let sl_dp_estado_nacimiento_id = $("#dp_estado_nacimiento_id");
let sl_dp_municipio_nacimiento_id = $("#dp_municipio_nacimiento_id");



let sl_referencia_dp_estado_id = $("#referencia_dp_estado_id");
let sl_referencia_dp_municipio_id = $("#referencia_dp_municipio_id");
let sl_referencia_dp_cp_id = $("#referencia_dp_cp_id");
let sl_referencia_dp_colonia_postal_id = $("#referencia_dp_colonia_postal_id");
let sl_referencia_dp_calle_pertenece_id = $("#referencia_dp_calle_pertenece_id");


let nombre = '';
let apellido_paterno = '';
let apellido_materno = '';
let razon_social = '';

nombre = nombre_ct.val();
apellido_paterno = apellido_paterno_ct.val();
apellido_materno = apellido_materno_ct.val();

beneficiario_nombre_ct.change(function() {
    limpia_txt($(this));
});
beneficiario_apellido_paterno_ct.change(function() {
    limpia_txt($(this));
});
beneficiario_apellido_materno_ct.change(function() {
    limpia_txt($(this));
});
conyuge_nombre_ct.change(function() {
    limpia_txt($(this));
});
conyuge_apellido_paterno_ct.change(function() {
    limpia_txt($(this));
});

conyuge_curp_ct.change(function() {
    limpia_txt($(this));
});
conyuge_rfc_ct.change(function() {
    limpia_txt($(this));
});
conyuge_apellido_materno_ct.change(function() {
    limpia_txt($(this));
});


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

sub_cuenta_ct.change(function() {
    limpia_txt($(this));
    limpia_number($(this));
});
monto_final_ct.change(function() {
    limpia_txt($(this));
    limpia_number($(this));
});
descuento_ct.change(function() {
    limpia_txt($(this));
    limpia_number($(this));
});
puntos_ct.change(function() {
    limpia_txt($(this));
    limpia_number($(this));
});

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


    value = value.replace('$','');
    value = value.replace('$','');
    value = value.replace('$','');
    value = value.replace('$','');
    value = value.replace('$','');
    value = value.replace('$','');
    value = value.replace('$','');


    value = value.replace(',','');
    value = value.replace(',','');
    value = value.replace(',','');
    value = value.replace(',','');
    value = value.replace(',','');
    value = value.replace(',','');
    value = value.replace(',','');




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

function dp_asigna_municipios(dp_estado_id = '',dp_municipio_id = '', selector = "#dp_municipio_id"){

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


function dp_asigna_calles_pertenece(dp_colonia_postal_id = '',dp_calle_pertenece_id = '',selector = "#dp_calle_pertenece_id"){

    let sl_dp_calle_pertenece_id = $(selector);

    let url = "index.php?seccion=dp_calle_pertenece&ws=1&accion=get_calle_pertenece&dp_colonia_postal_id="+dp_colonia_postal_id+"&session_id="+session_id;
    $.ajax({
        type: 'GET',
        url: url,
    }).done(function( data ) {  // Función que se ejecuta si todo ha ido bien
        console.log(data);
        sl_dp_calle_pertenece_id.empty();
        integra_new_option(selector,'Seleccione una calle','-1');
        $.each(data.registros, function( index, dp_calle_pertenece ) {
            integra_new_option(selector,dp_calle_pertenece.dp_calle_descripcion,dp_calle_pertenece.dp_calle_pertenece_id);
        });
        sl_dp_calle_pertenece_id.val(dp_calle_pertenece_id);
        sl_dp_calle_pertenece_id.selectpicker('refresh');
    }).fail(function (jqXHR, textStatus, errorThrown){ // Función que se ejecuta si algo ha ido mal
        alert('Error al ejecutar');
    });
}
function dp_asigna_colonias_postales(dp_cp_id = '',dp_colonia_postal_id = '',selector = "#dp_colonia_postal_id"){

    let sl_dp_colonia_postal_id = $(selector);

    let url = "index.php?seccion=dp_colonia_postal&ws=1&accion=get_colonia_postal&dp_cp_id="+dp_cp_id+"&session_id="+session_id;
    $.ajax({
        type: 'GET',
        url: url,
    }).done(function( data ) {  // Función que se ejecuta si todo ha ido bien
        console.log(data);
        sl_dp_colonia_postal_id.empty();
        integra_new_option(selector,'Seleccione una colonia','-1');
        $.each(data.registros, function( index, dp_colonia_postal ) {
            integra_new_option(selector,dp_colonia_postal.dp_colonia_descripcion,dp_colonia_postal.dp_colonia_postal_id);
        });
        sl_dp_colonia_postal_id.val(dp_colonia_postal_id);
        sl_dp_colonia_postal_id.selectpicker('refresh');
    }).fail(function (jqXHR, textStatus, errorThrown){ // Función que se ejecuta si algo ha ido mal
        alert('Error al ejecutar');
    });
}
function dp_asigna_cps(dp_municipio_id = '',dp_cp_id = '', selector = "#dp_cp_id"){

    let sl_dp_cp_id = $(selector);

    let url = "index.php?seccion=dp_cp&ws=1&accion=get_cp&dp_municipio_id="+dp_municipio_id+"&session_id="+session_id;
    $.ajax({
        type: 'GET',
        url: url,
    }).done(function( data ) {  // Función que se ejecuta si todo ha ido bien
        console.log(data);
        sl_dp_cp_id.empty();
        integra_new_option(selector,'Seleccione un cp','-1');
        $.each(data.registros, function( index, dp_cp ) {
            integra_new_option(selector,dp_cp.dp_cp_descripcion,dp_cp.dp_cp_id);
        });
        sl_dp_cp_id.val(dp_cp_id);
        sl_dp_cp_id.selectpicker('refresh');
    }).fail(function (jqXHR, textStatus, errorThrown){ // Función que se ejecuta si algo ha ido mal
        alert('Error al ejecutar');
    });
}

sl_referencia_dp_estado_id.change(function(){
    let referencia_dp_estado_id = $(this).val();
    dp_asigna_municipios(referencia_dp_estado_id,'','#referencia_dp_municipio_id');
});

sl_referencia_dp_municipio_id.change(function(){
    let referencia_dp_municipio_id = $(this).val();
    dp_asigna_cps(referencia_dp_municipio_id,'','#referencia_dp_cp_id');
});

sl_referencia_dp_cp_id.change(function(){
    let referencia_dp_cp_id = $(this).val();
    dp_asigna_colonias_postales(referencia_dp_cp_id,'','#referencia_dp_colonia_postal_id');
});

sl_referencia_dp_colonia_postal_id.change(function(){
    let referencia_dp_colonia_postal_id = $(this).val();
    dp_asigna_calles_pertenece(referencia_dp_colonia_postal_id,'','#referencia_dp_calle_pertenece_id');
});


sl_dp_pais_id.change(function(){
    dp_pais_id = $(this).val();
    dp_asigna_estados(dp_pais_id);
});

sl_dp_estado_id.change(function(){
    dp_estado_id = $(this).val();
    dp_asigna_municipios(dp_estado_id);
});

sl_conyuge_dp_estado_id.change(function(){
    conyuge_dp_estado_id = $(this).val();
    dp_asigna_municipios(conyuge_dp_estado_id,'','#conyuge_dp_municipio_id');
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

sl_dp_estado_nacimiento_id.change(function(){
    let dp_municipio_nacimiento_id = sl_dp_estado_nacimiento_id.val();
    dp_asigna_municipios(dp_municipio_nacimiento_id,'','#dp_municipio_nacimiento_id');
});












