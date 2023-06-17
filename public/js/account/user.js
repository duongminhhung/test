$('.select2').select2()
var route = `${window.location.origin}/api/settings/account`;
var route_show = `${window.location.origin}/account/user/show`;
var route_his = `${window.location.origin}/api/settings/account/history`;
const table = $('#tableUser').DataTable({
    scrollX: true,
    aaSorting: [],
    language: {
        lengthMenu: 'Number of records _MENU_',
        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
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
        { data: 'name', defaultContent: '', title: 'Name' },
        { data: 'username', defaultContent: '', title: 'User Name' },
        { data: 'email', defaultContent: '', title: 'Email' },
        { data: 'created_at', defaultContent: '', title: 'Time Created' },
        { data: 'updated_at', defaultContent: '', title: 'Time Updated' },
        {
            data:'id',
            title: 'Action',
            defaultContent: '',
            render: function(data) {
                if(id_user == data || lvl_user == 9999)
                {
                    let a = ``;
                    if(id_user == data)
                    {
                        a = a + `<a href="` + route_show + `?id=` + data + `" class="btn btn-success" style=" width: 80px">
                                ` + 'Edit' + `
                            </a>`
                    }
                    if(lvl_user == 9999)
                    {
                        a = a + `<a href="` + route_show + `?id=` + data + `" class="btn btn-success" style=" width: 80px">
                                ` + 'Edit' + `
                            </a>
                            <button id="del-` + data + `" class="btn btn-danger btn-delete" style="width: 80px">
                            ` + 'Delete' + `
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
$(document).on('click', '.btn-history', function() {
    $('#modalTableHistory').modal();
    tablehis.ajax.reload()
});

