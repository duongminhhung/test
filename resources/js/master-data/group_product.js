$('.select2').select2()
import t from "../lang"
var route = `${window.location.origin}/api/settings/group-product`;
console.log(route);
console.log(role_edit);
console.log(role_delete);
const table = $('.table-product').DataTable({
    scrollX: true,
    aaSorting: [],
    language: {
        lengthMenu: t('Number of records _MENU_'),
        info: t('Showing _START_ to _END_ of _TOTAL_ entries'),
        paginate: {
            previous: '‹',
            next: '›'
        },
    },
    processing: true,
    serverSide: true,
    searching: false,
    ordering: false,
   
    lengthMenu: [5, 10, 20, 25, 50],
    ajax: {
        url: route,
        dataSrc: 'data',
        data: d => {
            delete d.columns
            delete d.order
            delete d.search
            d.page = (d.start / d.length) + 1
            d.symbols = $('.symbols').val()
            d.name = $('.name').val()
        }
    },
    columns: [
        { data: 'ID', defaultContent: '', title: 'ID' },
        { data: 'Name', defaultContent: '', title: t('Name') + ' ' + t('Product') },
        { data: 'Symbols', defaultContent: '', title: t('Symbols') + ' ' + t('Product') },
        // { data: 'product', defaultContent: '', title: t('Product') ,  render: function(data) {
        //     let p = '';
        //     jQuery.each(data, function(k, v) {
        //        p += `<p>`+v.Symbols+`,</p>`
        //     })
        //     return p;
        // }},
        { data: 'user_created.username', defaultContent: '', title: t('User Created') },
        { data: 'Time_Created', defaultContent: '', title: t('Time Created') },
        { data: 'user_updated.username', defaultContent: '', title: t('User Updated') },
        { data: 'Time_Updated', defaultContent: '', title: t('Time Updated') },
        {
            data: { id: 'ID', status: 'Status' },
            title: t('Action'),
            render: function(data) {
                let bt = ``;
                if (role_delete) {
                    if (!data.running) {
                        bt = bt + `<button id="del-` + data.ID + `" class="btn btn-danger btn-delete" style="width: 80px">
                        ` + t('Delete') + `
                        </button>`;
                    }
                }
                return bt;
            }
        }
    ]
})
$('table').on('page.dt', function() {
    console.log(table.page.info())
})
var filter = $('.filter').on('click', () => {

    table.ajax.reload()
})
var load = function() {

    table.ajax.reload()
}
$(document).on('click', '.btn-delete', function() {
    let id = $(this).attr('id');
    let name = $(this).parent().parent().children('td').first().text();    
    var currentRow = $(this).closest("tr");
    var col1 = currentRow.find("td:eq(0)").text();
    $('#modalRequestDel').modal();
    $('#nameDel').text(t('Product') + ' : ' + col1);
    $('#idDel').val(id.split('-')[1]);
});


$('.btn-import').on('click', function() {
    $('#modalImport').modal();
    $('#importFile').val('');
    $('.input-text').text(__input.file);
    $('.error-file').hide();
    $('.btn-save-file').prop('disabled', false);
    $('#product_id').val('');
});
let check_file = false;
$('#importFile').on('change', function() {
    let val = $(this).val();
    let name = val.split('\\').pop();
    let typeFile = name.split('.').pop().toLowerCase();
    $('.input-text').text(name);
    $('.error-file').hide();

    if (typeFile != 'xlsx' && typeFile != 'xls' && typeFile != 'txt') {
        $('.error-file').show();
        $('.btn-save-file').prop('disabled', true);
    } else {
        $('.btn-save-file').prop('disabled', false);
        check_file = true;
    }
});
$('.btn-save-file').on('click', function() {
    $('.error-file').hide();

    if (check_file) {
        $('.btn-submit-file').click();
    } else {
        $('.error-file').show();
    }
});