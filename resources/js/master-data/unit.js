$('.select2').select2()
import t from "../lang"
var route = `${window.location.origin}/api/settings/unit`;
var route_show = `${window.location.origin}/setting/setting-unit/show`;
var route_his = `${window.location.origin}/api/settings/unit/history`;
var route_return = `${window.location.origin}/setting/setting-unit/return`;
const table = $('.table-unit').DataTable({
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
        {
            data: 'Type',
            title: t('Type'),
            render: function(data) {
                if (data == 1) {
                    return t('Unit');
                } else {
                    return t('Parking');
                }
            }
        },
        { data: 'Note', defaultContent: '', title: t('Note') },
        { data: 'user_created.username', defaultContent: '', title: t('User Created') },
        { data: 'Time_Created', defaultContent: '', title: t('Time Created') },
        { data: 'user_updated.username', defaultContent: '', title: t('User Updated') },
        { data: 'Time_Updated', defaultContent: '', title: t('Time Updated') },
        {
            data: 'ID',
            title: t('Action'),
            render: function(data) {
                console.log(route)
                return `<a href="` + route_show + `?ID=` + data + `" class="btn btn-success" style="width: 80px">
           ` + t('Edit') + `
            </a>
            
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
            d.unitid = $('#idUnit').val()
        }
    },
    columns: [
        { data: 'ID', defaultContent: '', title: 'ID' },
        { data: 'Name', defaultContent: '', title: t('Name') },
        { data: 'Symbols', defaultContent: '', title: t('Symbols') },
        {
            data: 'Type',
            title: t('Type'),
            render: function(data) {
                if (data == 1) {
                    return t('Unit');
                } else {
                    return t('Parking');
                }
            }
        },
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