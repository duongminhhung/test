$('.select2').select2()
import t from "../lang"
var route = `${window.location.origin}/api/settings/holiday`;
var route_show = `${window.location.origin}/setting/setting-holiday/show`;
console.log(route);
const table = $('table').DataTable({
    scrollX: true,
    // infoCallback: ( settings, start, end, max, total, pre ) => {
    //     console.log({settings, start, end, max, total, pre})
    // },
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
    lengthMenu: [10, 15, 20, 25, 50],
    ajax: {
        url: route,
        dataSrc: 'data',
        data: d => {
            d.page = (d.start / d.length) + 1
            d.symbols = $('.symbols').val()
            d.name = $('.name').val()
        }
    },
    columns: [
        { data: 'ID', defaultContent: '', title: 'ID' },
        { data: 'Name', defaultContent: '', title: t('Name') },
        { data: 'Start', defaultContent: '', title: t('Start') },
        { data: 'End', defaultContent: '', title: t('End') },
        { data: 'Note', defaultContent: '', title: t('Note') },
        { data: 'user_created.username', defaultContent: '', title: t('User Created') },
        { data: 'Time_Created', defaultContent: '', title: t('Time Created') },
        { data: 'user_updated.username', defaultContent: '', title: t('User Updated') },
        { data: 'Time_Updated', defaultContent: '', title: t('Time Updated') },
        {
            data: 'ID',
            title: t('Action'),
            render: function(data) {
                return `
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