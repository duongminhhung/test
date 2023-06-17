$('.select2').select2()
import t from "../lang"
var route = `${window.location.origin}/api/settings/product`;
var route_show = `${window.location.origin}/setting/setting-product/show`;
var route_bom = `${window.location.origin}/setting/setting-product/add-or-update`;
var route_his = `${window.location.origin}/api/settings/product/history`;
var route_return = `${window.location.origin}/setting/setting-product/return`;
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
   
    lengthMenu: [10, 20, 25, 50],
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
        { data: 'group_product.Symbols', defaultContent: '', title: t('Group') + ' ' + t('Product') },
        { data: 'user_created.username', defaultContent: '', title: t('User Created') },
        { data: 'Time_Created', defaultContent: '', title: t('Time Created') },
        { data: 'user_updated.username', defaultContent: '', title: t('User Updated') },
        { data: 'Time_Updated', defaultContent: '', title: t('Time Updated') },
        {
            data: { id: 'ID', status: 'Status' },
            title: t('Action'),
            render: function(data) {
                let bt = ``;
                if (role_edit) {
                    bt = bt + `
                    <button  class="btn btn-success btn-edit" style="width: 80px">
                     ` + t('Edit') + `
                    </button>`;
                }
                if (role_delete) {
                    if (!data.running) {
                        bt = bt + `<button id="del-` + data.ID + `" class="btn btn-danger btn-delete" style="width: 80px">
                        ` + t('Delete') + `
                        </button>
                        `;
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
    if (check_file) 
    {
        $('.btn-submit-file').click();
    } 
    else 
    {
        $('.error-file').show();
    }
});

const tablehis = $('.table-his').DataTable({
    scrollX: true,
    searching: false,
    ordering: false,
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
    dom: 'rt<"bottom"flp><"clear">',
    lengthMenu: [10, 20, 30, 40, 50],
    ajax: {
        url: route_his,
        dataSrc: 'data',
        data: d => {
            delete d.columns
            delete d.order
            delete d.search
            d.page = (d.start / d.length) + 1
        }
    },
    columns: [
        { data: 'ID', defaultContent: '', title: 'ID' },
        {
            data: 'Data_Table',
            defaultContent: '',
            title: t('Symbols'),
            render: function(data) {
                var datas = JSON.parse(data)
                return datas.Product_BOM_ID
            }
        },
        {
            data: 'Data_Table',
            defaultContent: '',
            title: t('Mold'),
            render: function(data) {
                var datas = JSON.parse(data)
                return datas.Mold_ID ? (datas.Mold_ID + `|` + datas.Cavity + `|` + datas.Cycle_Time) : ``
            }
        },
        {
            data: 'Data_Table',
            defaultContent: '',
            title: t('Materials'),
            render: function(data) {
                var datas = JSON.parse(data)
                return datas.Materials_ID ? (datas.Materials_ID + `|` + datas.Quantity_Material) : ``
            }
        },
        {
            data: 'Action_Name',
            defaultContent: '',
            title: t('Action Name'),
            render: function(data) {
                return t(data)
            }
        },
        {
            data: 'Data_Table',
            defaultContent: '',
            title: t('User Updated'),
            render: function(data) {
                var datas = JSON.parse(data)
                return datas.User_Updated_Name

            }
        },
        {
            data: 'Data_Table',
            defaultContent: '',
            title: t('Time Updated'),
            render: function(data) {
                var datas = JSON.parse(data)
                var timecreated = moment(datas.Time_Updated, 'MMMM Do YYYY h:mm:ss a').format('YYYY-MM-DD HH:mm:ss')
                return timecreated
            }
        },
    ]
})

$(document).on('click', '.btn-history', function() {
    let id = $(this).attr('id');
    let name = $(this).parent().parent().children('td').first().text();
    
    $('#modalTableHistory').modal();
    tablehis.ajax.reload()
    $('#nameDel').text(name);
});