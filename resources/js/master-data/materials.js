$('.select2').select2()
import t from "../lang"
var route = `${window.location.origin}/api/settings/materials`;
var route_show = `${window.location.origin}/setting/setting-materials/show`;
var route_his = `${window.location.origin}/api/settings/materials/history`;
var route_return = `${window.location.origin}/setting/setting-materials/return`;
console.log(route);
const table = $('.table-mat').DataTable({
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
        { data: 'ID', defaultContent: '', title: 'ID' },
        { data: 'Name', defaultContent: '', title: t('Name') },
        { data: 'Symbols', defaultContent: '', title: t('Symbols') },
        { data: 'Note', defaultContent: '', title: t('Note') },
        { data: 'user_created.username', defaultContent: '', title: t('User Created') },
        { data: 'Time_Created', defaultContent: '', title: t('Time Created') },
        { data: 'user_updated.username', defaultContent: '', title: t('User Updated') },
        { data: 'Time_Updated', defaultContent: '', title: t('Time Updated') },
        {
            data: 'ID',
            title: t('Action'),
            render: function(data) {
                return `<a href="` + route_show + `?ID=` + data + `" class="btn btn-success" style="width: 80px">
                ` + t('Edit') + `
                    </a>
                    <button id="history-` + data + `" class="btn btn-warning btn-history" style="width: 80px">
                    ` + t('History') + `
                    </button>
                    <button id="del-` + data + `" class="btn btn-danger btn-delete" style="width: 80px">
                    ` + t('Delete') + `
                    </button>
                    `;
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
$(document).on('click', '.btn-history', function() {
    let id = $(this).attr('id');
    let name = $(this).parent().parent().children('td').first().text();

    $('#modalTableHistory').modal();
    $('#idUnit').val(id.split('-')[1]);
    tablehis.ajax.reload()
    $('#nameDel').text(name);
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
    lengthMenu: [10, 20, 30, 40, 50],
    ajax: {
        url: route_his,
        dataSrc: 'data',
        data: d => {
            delete d.columns
            delete d.order
            delete d.search
            d.page = (d.start / d.length) + 1
            d.materialid = $('#idUnit').val()
        }
    },
    columns: [
        { data: 'ID', defaultContent: '', title: 'ID' },
        { data: 'Name', defaultContent: '', title: t('Name') },
        { data: 'Symbols', defaultContent: '', title: t('Symbols') },
        {
            data: 'Status',
            title: t('Status'),
            render: function(data) {
                if (data == 1) {
                    return t('Update');
                } else if (data == 2) {
                    return t('Delete');
                } else {
                    return t('Return');
                }
            }
        },
        { data: 'Note', defaultContent: '', title: t('Note') },
        { data: 'user_created.username', defaultContent: '', title: t('User Created') },
        { data: 'Time_Created', defaultContent: '', title: t('Time Created') },
    ]
})