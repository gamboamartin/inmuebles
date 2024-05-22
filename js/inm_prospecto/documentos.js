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
    modal.close();
}
modal.addEventListener('click', function (event) {
    if (event.target === modal) {
        modal.close();
    }
});


