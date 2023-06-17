$('.select2').select2()
import t from "../lang"
var route = `${window.location.origin}/api/settings/shift`;
var route_show = `${window.location.origin}/setting/setting-shift/show`;
var route_his = `${window.location.origin}/api/settings/shift/history`;
var route_return = `${window.location.origin}/setting/setting-shift/return`;
console.log(route);
const table = $('.table-shift').DataTable({
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
    dom: 'rt<"bottom"flp><"clear">',
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
            d.name = $('.name').val()
        }
    },
    columns: [
        // { data: 'ID', defaultContent: '', title: 'ID' },
        { data: 'Name', defaultContent: '', title: t('Name') },
        // { data: 'Symbols', defaultContent: '', title: t('Symbols') },
        { data: 'Start_Time', defaultContent: '', title: t('Start Time') },
        { data: 'End_Time', defaultContent: '', title: t('End Time') },
        { data: 'Note', defaultContent: '', title: t('Note') },
        { data: 'user_created.username', defaultContent: '', title: t('User Created') },
        { data: 'Time_Created', defaultContent: '', title: t('Time Created') },
        { data: 'user_updated.username', defaultContent: '', title: t('User Updated') },
        { data: 'Time_Updated', defaultContent: '', title: t('Time Updated') },
        {
            data: 'ID',
            title: t('Action'),
            render: function(data) {
                let bt = ``;
                if (role_delete) {
                    bt = bt + `<button id="del-` + data + `" class="btn btn-danger btn-delete" style="width: 80px">
                    ` + t('Delete') + `
                    </button>
                    `;
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
    lengthMenu: [10, 20, 30, 40, 50],
    ajax: {
        url: route_his,
        dataSrc: 'data',
        data: d => {
            delete d.columns
            delete d.order
            delete d.search
            d.page = (d.start / d.length) + 1
            d.shiftid = $('#idUnit').val()
        }
    },
    columns: [
        { data: 'ID_Main', defaultContent: '', title: 'ID' },
        {
            data: 'Data_Table',
            defaultContent: '',
            title: t('Name'),
            render: function(data) {
                var datas = JSON.parse(data)
                return datas.Name
            }
        },
        {
            data: 'Data_Table',
            defaultContent: '',
            title: t('Start Time'),
            render: function(data) {
                var datas = JSON.parse(data)
                return datas.Start_Time
            }
        },
        {
            data: 'Data_Table',
            defaultContent: '',
            title: t('End Time'),
            render: function(data) {
                var datas = JSON.parse(data)
                return datas.End_Time
            }
        },
        {
            data: 'Data_Table',
            defaultContent: '',
            title: t('Note'),
            render: function(data) {
                var datas = JSON.parse(data)
                return datas.Note

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