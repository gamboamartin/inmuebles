const registro_id = getParameterByName('registro_id');

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