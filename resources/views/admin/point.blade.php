@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <span class="text-bold" style="font-size: 23px">
                {{ __('Quản lý điểm sinh viên') }}
            </span>
        </div>
        <div class="card-bordy p-3">
            <div class="h-space">
                <div class="form-group w-20">
                    <label>
                        {{ __('Choose') }} {{__("Students") }}
                    </label>
                    <select class="custom-select select2 select_department" name="Symbols">
                        <option value="">
                            {{ __('Choose') }} {{__("Students") }}
                        </option>
                        @foreach ($students as $item)
                            <option value="{{ $item->id_student }}">
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-info w-10 filter">{{ __('Filter') }}</button>
            </div>
            @if (Session::has('message'))
                 <div class="alert alert-success">{{ Session::get('message') }}</div>
            @endif
            @if (Session::has('delete'))
                 <div class="alert alert-danger">{{ Session::get('delete') }}</div>
            @endif
            <table class="table table-striped table-hover table-bordered" id="tableStudent" width="100%">
            </table>
        </div>
    </div>

    <div class="modal fade" id="test" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{__('Add')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form" action="{{ route('update_point') }}" method="get">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" name="id" class="form-control" id="id" required>
                            <label for="name">Tên sinh viên:</label>
                            <input type="text" name="name" class="form-control" id="name" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Toán:</label>
                            <input type="number" name="maths" class="form-control" id="maths" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Văn Học:</label>
                            <input type="number" name="literature" class="form-control" id="literature" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Lý:</label>
                            <input type="number" name="physical" class="form-control" id="physical" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Hóa:</label>
                            <input type="number" name="chemistry" class="form-control" id="chemistry" required>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                            <button type="submit" id="bt" class="btn btn-primary">Lưu Lại</button>
                            
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
    $(".select2").select2({});
    $(document).on('click', 'button.btn.btn-primary.update', function() {
            var id = $(this).val();
            var route1 = `${window.location.origin}/api/update_point/`+ id ;
            $.ajax({
                url: route1,
                type: 'get',
                success: function(response) {
                    $("#name").val(response.data[0].name);
                    $("#id").val(response.data[0].id_student);
                    $("#maths").val(response.data[0].maths);
                    $("#literature").val(response.data[0].literature);
                    $("#physical").val(response.data[0].physical);
                    $("#chemistry").val(response.data[0].chemistry);
                    $("#bt").html('Lưu lại');
                },
                error: function(xhr, status, error) {}
            });

        });

        var route = `${window.location.origin}/api/point/`;
        const table = $('#tableStudent').DataTable({
            scrollX: true,
            rowsGroup: [0],
            aaSorting: [],
            language: {
                lengthMenu: `{{ __('Number of records') }} _MENU_`,
                info: `{{ __('Showing') }} _START_ {{ __('to') }} _END_ {{ __('of') }} _TOTAL_ {{ __('entries') }}`,
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
                    d.name = $('.select_department').val()
                }
            },
            columns: [{
                    data: 'name',
                    defaultContent: '',
                    title: `{{ __('Name')}}`,
                    render: function(data) {
                        return data
                    }
                },
                {
                    data: 'maths',
                    defaultContent: '',
                    title: `{{ __('Toán') }}`,
                    render: function(data) {
                        return data
                    }
                }, 
                {
                    data: 'literature',
                    defaultContent: '',
                    title: `{{ __('Văn Học') }}`,
                    render: function(data) {
                        return data
                    }
                }, 
                 {
                    data: 'chemistry',
                    defaultContent: '',
                    title: `{{ __('Hóa') }}`,
                    render: function(data) {
                        return data
                    }
                },
                {
                    data: 'physical',
                    defaultContent: '',
                    title: `{{ __('Lý') }}`,
                    render: function(data) {
                        return data
                    }
                },
                {
                    data: 'id',
                    defaultContent: '',
                    title: `{{ __('Actions') }}`,
                    render: function(data, type, row) {
                        const routeDelete = `{{ route('admin.delete', '') }}/${data}`;
                        const sendMailUrl = `{{ route('admin.sendMail', '') }}/${data}`;
                        return `                       
                            <button type="button" class="btn btn-primary update" value='${data}'
                                data-toggle="modal" data-target="#test">
                                {{ __('Update') }}
                            </button>
                            <a href="${sendMailUrl}" type="button" class="btn btn-info">{{ __('Send Mail') }}</a>
                        `;
                    }
                }


            ]
        })
        $('.filter').on('click', function() {
            table.ajax.reload()
        })

        // $(document).ready(function() {
        //     $('#test').on('hidden.bs.modal', function() {
        //         $('#form').attr('action', '/admin/create_department/');
        //         $('#form').attr('method', 'post');
        //         $("#password").show();
        //         $('#form')[0].reset();
        //     });
        // });
</script>   
@endpush
