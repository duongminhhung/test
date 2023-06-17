$('.select2').select2()
import t from "../lang"
var route = `${window.location.origin}/api/settings/account`;
var route_show = `${window.location.origin}/account/user/show`;
var route_his = `${window.location.origin}/api/settings/account/history`;

console.log(route);
const table = $('#tableUser').DataTable({
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
            d.username = $('.username').val()
            d.id_user = id_user
            d.lvl_user = lvl_user
        }
    },
    columns: [
        { data: 'name', defaultContent: '', title: t('Name') },
        { data: 'username', defaultContent: '', title: t('User Name') },
        { data: 'email', defaultContent: '', title: t('Email') },
        { data: 'created_at', defaultContent: '', title: t('Time Created') },
        { data: 'updated_at', defaultContent: '', title: t('Time Updated') },
        {
            data:'id',
            title: t('Action'),
            defaultContent: '',
            render: function(data) {
                if(id_user == data || lvl_user == 9999)
                {
                    let a = ``;
                    if(id_user == data)
                    {
                        a = a + `<a href="` + route_show + `?id=` + data + `" class="btn btn-success" style=" width: 80px">
                                ` + t('Edit') + `
                            </a>`
                    }
                    if(lvl_user == 9999)
                    {
                        a = a + `<a href="` + route_show + `?id=` + data + `" class="btn btn-success" style=" width: 80px">
                                ` + t('Edit') + `
                            </a>
                            <button id="del-` + data + `" class="btn btn-danger btn-delete" style="width: 80px">
                            ` + t('Delete') + `
                            </button>
                            `
                    }
                    return a
                }
                else
                {
                    return  ``
                }
                
                    
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
        // { data: 'ID_Main', defaultContent: '', title: 'ID' },
        { data: 'Data_Table', defaultContent: '', title: t('Name'),render:function(data){
            var datas = JSON.parse(data)
            return datas.name
        } },
        { data: 'Data_Table', defaultContent: '', title: t('User Name'),render:function(data){
            var datas = JSON.parse(data)
            return datas.username
        } },
        { data: 'Data_Table', defaultContent: '', title: t('Email'),render:function(data){
            var datas = JSON.parse(data)
            return datas.email
        } },
        { data: 'Data_Table', defaultContent: '', title: t('Role'),render:function(data){
            var datas = JSON.parse(data)
            return datas.role
        } },
        { data: 'Data_Table', defaultContent: '', title: t('Description'),render:function(data){
            var datas = JSON.parse(data)
            return datas.description
        } },
        { data: 'Action_Name', defaultContent: '', title: t('Action Name'),render:function(data){
            return t(data)
        } },
        { data: 'Data_Table', defaultContent: '', title: t('Time Updated'),render: function(data)
        {
            var datas = JSON.parse(data)
            var timeupdated =  moment(datas.updated_at, 'MMMM Do YYYY h:mm:ss a').format('YYYY-MM-DD HH:mm:ss')
            return timeupdated
        }},
    ]
})
$(document).on('click', '.btn-history', function() {
    $('#modalTableHistory').modal();
    tablehis.ajax.reload()
});

