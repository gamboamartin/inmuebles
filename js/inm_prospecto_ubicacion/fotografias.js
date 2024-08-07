const registro_id = getParameterByName('registro_id');
let session_id = getParameterByName('session_id');

var modal = document.getElementById("myModal");
var closeBtn = document.getElementById("closeModalBtn");
var getValue = $(this).attr("data-target");

$(".imagen").on("click", function() {
    $('.imagen_modal').attr("src", $(this).attr("src"));
    $("#myModal").fadeIn();
});

$(".close-button").on("click", function() {
    $("#myModal").fadeOut();
});

$(window).on("click", function(event) {
    if ($(event.target).is("#myModal")) {
        $("#myModal").fadeOut();
    }
});

let doc_documento_id = -1;
let doc_tipo_documento_id = -1;
let alto = 0;

$(".elimina_img").on("click", function() {
    let inm_doc_prospecto_ubicacion_id = $(this).data('inm_doc_prospecto_ubicacion_id');

    $.ajax({
        type: "POST",
        data: {id:inm_doc_prospecto_ubicacion_id},
        url: 'index.php?seccion=inm_doc_prospecto_ubicacion&accion=elimina_bd&ws=1&registro_id='+inm_doc_prospecto_ubicacion_id+'&session_id='+session_id,
        success: function(data_r) {
            console.log(data_r);
        },
        error: function() {
            alert("No se ha podido obtener la información");
        }
    });
    $(this).closest(".contenedor_img").remove();

});

$( ".contenedor_img" ).draggable({
    start: function( event, ui ) {
        doc_documento_id = $(this).data('doc_documento_id');
        alto = $( ".contenedor_img" ).height();
    },
    revert: "invalid"
});

$(".contorno").droppable({
    over: function( evento, ui ) {
        doc_tipo_documento_id = $(this).data('doc_tipo_documento_id');
        $(this).addClass('bg-info');
        $(this).removeClass('bg-light');
    },
    out: function( evento, ui ) {
        doc_tipo_documento_id = $(this).data('doc_tipo_documento_id');
        $(this).addClass('bg-light');
        $(this).removeClass('bg-info');
    },
    drop: function( evento, ui ) {
        doc_tipo_documento_id = $(this).data('doc_tipo_documento_id');

        var xPos = 0; // Posición X (desde la esquina superior izquierda)
        var yPos = 0; // Posición Y (desde la esquina superior izquierda)

        ui.draggable.css({
            top: yPos + "px",
            left: xPos + "px",
        }).appendTo($(this));

        $.ajax({
            type: "POST",
            data: {doc_tipo_documento_id:doc_tipo_documento_id},
            url: 'index.php?seccion=doc_documento&accion=modifica_bd&ws=1&registro_id='+doc_documento_id+'&session_id='+session_id,
            success: function(data_r) {

                console.log(data_r);
            },
            error: function() {
                alert("No se ha podido obtener la información");
            }
        });
    }
});