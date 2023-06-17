$('.select2').select2()
import t from "../lang"
var route = `${window.location.origin}/api/settings/machine`;
var route_show = `${window.location.origin}/setting/setting-machine/show`;
var route_his = `${window.location.origin}/api/settings/machine/history`;
var route_return = `${window.location.origin}/setting/setting-machine/return`;
console.log(route);

const table = $('.table-machine').DataTable({
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
    dom: 'rt<"bottom"flp><"clear">',
    processing: true,
    serverSide: true,
    ordering: false,
    searching: false,
    dom: 'rt<"bottom"flp><"clear">',
    lengthMenu: [10, 15, 20, 25, 50],
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
        // { data: 'ID', defaultContent: '', title: 'ID' },
        // { data: 'Name', defaultContent: '', title: t('Name') },
        { data: 'Symbols', defaultContent: '', title: t('Symbols') },
        { data: 'Model', defaultContent: '', title: t('Model') },
        { data: 'Group_Machine', defaultContent: '', title: t('Group') },
        { data: 'Manufacturer', defaultContent: '', title: t('Manufacturer') },
        { data: 'MAC', defaultContent: '', title: t('MAC IOT') },
        { data: 'Note', defaultContent: '', title: t('Note') },
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
                        <a href="` + route_show + `?ID=` + data.ID + `" class="btn btn-success" style="width: 80px">
                     ` + t('Edit') + `
                    </a>`;
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
$('.filter').on('click', () => {
    table.ajax.reload()
})
$(document).on('click', '.btn-delete', function() {
    let id = $(this).attr('id');
    let name = $(this).parent().parent().children('td').first().text();
    $('#modalRequestDel').modal();
    $('#nameDel').text(name);
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


$(document).on('click', '.btn-history', function() {
    $('#modalTableHistory').modal();
    tablehis.ajax.reload()
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
            d.machineid = $('#idUnit').val()
        }
    },
    columns: [
        { data: 'ID_Main', defaultContent: '', title: 'ID' },
        {
            data: 'Data_Table',
            defaultContent: '',
            title: t('Symbols'),
            render: function(data) {
                var datas = JSON.parse(data)
                return datas.Symbols
            }
        },
        {
            data: 'Data_Table',
            defaultContent: '',
            title: t('MAC'),
            render: function(data) {
                var datas = JSON.parse(data)
                return datas.MAC
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
                return datas.User_Updated
            }
        },
        {
            data: 'Data_Table',
            defaultContent: '',
            title: t('Time Updated'),
            render: function(data) {
                var datas = JSON.parse(data)
                var timeupdated = moment(datas.Time_Updated, 'MMMM Do YYYY h:mm:ss a').format('YYYY-MM-DD HH:mm:ss')
                return timeupdated
            }
        },
    ]
})