var modal = document.getElementById("myModal");
var closeBtn = document.getElementById("closeModalBtn");

$("td a[title='Vista Previa']").click(function(event) {
    event.preventDefault();
    var url = "http://localhost/inmuebles/index.php?seccion=inm_doc_prospecto&accion=vista_previa&registro_id=1&session_id=2341191583&adm_menu_id=45";

    $.ajax({
        url: url,
        type: 'GET',
        success: function(data) {
            var tempDiv = $("<div>").html(data);
            var viewContent = tempDiv.find(".view");
            viewContent.find(".top-title").remove();
            viewContent.find(".widget-header").remove();

            $("#myModal .content").html(viewContent);
        },
        error: function() {
            $("#myModal .content").html("<p>Error al cargar el contenido.</p>");
        }
    });
    modal.showModal();
});

closeBtn.onclick = function() {
    modal.close();
}
modal.addEventListener('click', function(event) {
    if (event.target === modal) {
        modal.close();
    }
});


