var modal = document.getElementById("myModal");
var closeBtn = document.getElementById("closeModalBtn");

$("td a[title='Vista Previa']").click(function (event) {
    event.preventDefault();
    let registro_id = getParameterByName('registro_id');
    let absolute = getAbsolutePath();
    let path = get_url("inm_doc_prospecto", "vista_previa", {registro_id: registro_id});
    var url =  absolute + path.replace(/&ws=1/, '');

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
    modal.close();
}
modal.addEventListener('click', function (event) {
    if (event.target === modal) {
        modal.close();
    }
});


