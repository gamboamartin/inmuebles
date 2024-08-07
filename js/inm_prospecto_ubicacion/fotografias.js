const registro_id = getParameterByName('registro_id');
let session_id = getParameterByName('session_id');

var modal = document.getElementById("myModal");
var closeBtn = document.getElementById("closeModalBtn");
var getValue = $(this).attr("data-target");

$(document).on("click", "img[class='imagen']", function (event) {
    alert('hola');
    event.preventDefault();
    var url = $(this).attr("href");

    var loaderOverlay = $('<div class="loader-overlay"><div class="loader"></div></div>');
    $('body').append(loaderOverlay);

    $.ajax({
        url: url,
        type: 'GET',
        success: function (data) {
            var tempDiv = $("<div>").html(data);
            var viewContent = tempDiv.find(".view");

            $("#myModal .content").html(viewContent);
            modal.showModal();
            loaderOverlay.remove();
        },
        error: function () {
            $("#myModal .content").html("<p>Error al cargar el contenido.</p>");
            modal.showModal();
            loaderOverlay.remove();
        }
    });
});

closeBtn.onclick = function () {
    $("#myModal .content").empty();
    modal.close();
}

modal.addEventListener('click', function (event) {
    if (event.target === modal) {
        $("#myModal .content").empty();
        modal.close();
    }
});

$(document).on("click", "#elimina a", function (event) {
    alert('hola');
});

let doc_documento_id = -1;
let doc_tipo_documento_id = -1;
let alto = 0;
let alto_contenedor = 0;
let alto_base = 0;

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