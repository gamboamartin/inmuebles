let url = getAbsolutePath();
let registro_id = getParameterByName('registro_id');
let session_id = getParameterByName('session_id');

$(document).ready(function(){

    var table = $('.datatable').DataTable();

    table.on('draw', function() {
        var data = table.rows();
        data.every(function(rowIdx, tableLoop, rowLoop) {
            var res = this.data();

            if(res.pr_etapa_descripcion === 'ALTA'){
                $(this.node()).addClass('highlighted-row');
            }
        });
    });



});





