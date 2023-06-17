$('.select2').select2()
import t from "../lang"
var route = `${window.location.origin}/api/settings/account/role`;
var route_show = `${window.location.origin}/setting/setting-account/role/show`;

// console.log(route);
const table = $('#tableRole').DataTable({
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
    dom: 'rt<"bottom"flp><"clear">',
    serverSide: true,
    ordering: false,
    searching: false,
    lengthMenu: [10, 15, 20, 25, 50],
    ajax: {
        url: route,
        dataSrc: 'data',
        data: d => {
            delete d.columns
            delete d.order
            delete d.search
            d.page = (d.start / d.length) + 1
            // d.username = $('.username').val()
        }
    },
    columns: [
        { data: 'role', defaultContent: '', title: t('Role') },
        { data: 'description', defaultContent: '', title: t('Description') },
        { data: 'Note', defaultContent: '', title: t('Note') },
        { data: 'time_created', defaultContent: '', title: t('Time Created')},
        { data: 'time_updated', defaultContent: '', title: t('Time Updated')},
        {
            data:'id',
            title: t('Action'),
            defaultContent: '',
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



