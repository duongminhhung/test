$('.select2').select2()
import t from "../lang"
import moment from "moment";
var route = `${window.location.origin}/api/settings/agv`;
var route_show = `${window.location.origin}/setting/setting-agv/show`;
var route_his = `${window.location.origin}/api/settings/agv/history`;

// console.log(route);
const table = $('.table-agv').DataTable({
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
            d.ip = $('.ip').val()
            d.mac = $('.mac').val()
            d.name = $('.name').val()
        }
    },
    columns: [
        { data: 'Name', defaultContent: '', title: t('Name') },
        { data: 'MAC', defaultContent: '', title: t('MAC') },
        {
            data: 'Type',
            defaultContent: '',
            title: t('Type'),
            render: function(data) {
                if (data == 1) return t('NAV');
                if (data == 2) return t('SHIV TYPE');
                if (data == 3) return t('AMR');
            }
        },
        {
            data: 'Active',
            defaultContent: '',
            title: t('Status'),
            render: function(data) {
                if (data == 2) return t('Enable');
                if (data == 1) return t('Disable');
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
                let bt = ``;
                if (role_edit) {
                    bt = bt + `
                        <a href="` + route_show + `?ID=` + data + `" class="btn btn-success" style="width: 80px">
                     ` + t('Edit') + `
                    </a>`;
                }
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
    // console.log(table.page.info())
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
            title: t('MAC'),
            render: function(data) {
                var datas = JSON.parse(data)
                return datas.MAC
            }
        },
        {
            data: 'Data_Table',
            defaultContent: '',
            title: t('Type'),
            render: function(data) {
                var datas = JSON.parse(data)
                if (datas.Type == 1) {
                    return "NAV"
                }
                if (datas.Type == 2) {
                    return "SHIV TYPE"
                }
                if (datas.Type == 3) {
                    return "AMR"
                }
            }
        },
        {
            data: 'Data_Table',
            defaultContent: '',
            title: t('Type'),
            render: function(data) {
                var datas = JSON.parse(data)
                if (datas.Active == 2) {
                    return t("ENABLE")
                }
                if (datas.Active == 1) {
                    return t("DISABLE")
                }
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
    $('#modalTableHistory').modal();
    tablehis.ajax.reload()
});