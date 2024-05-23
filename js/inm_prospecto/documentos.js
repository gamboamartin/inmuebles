const columns_tipos_documentos = [
    {
        title: "Tipo documento",
        data: "doc_tipo_documento_descripcion"
    },
    {
        title: "Descarga",
        data: "descarga"
    },
    {
        title: "Vista previa",
        data: "vista_previa"
    },
    {
        title: "ZIP",
        data: "descarga_zip"
    },
    {
        title: "Elimina",
        data: "elimina_bd"
    }
];

const table_tipos_documentos = table('inm_prospecto', columns_tipos_documentos, [], [], function () {}, true,
    "tipos_documentos",{registro_id: getParameterByName('registro_id')});







var modal = document.getElementById("myModal");
var closeBtn = document.getElementById("closeModalBtn");

$("td a[title='Vista Previa']").click(function (event) {
    event.preventDefault();
    var url = $(this).attr("href");
    $.ajax({
        url: url,
        type: 'GET',
        success: function (data) {
            var tempDiv = $("<div>").html(data);
            var viewContent = tempDiv.find(".view");

            $("#myModal .content").html(viewContent);
        },
        error: function () {
            $("#myModal .content").html("<p>Error al cargar el contenido.</p>");
        }
    });
    modal.showModal();
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




