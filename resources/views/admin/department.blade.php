@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <span class="text-bold" style="font-size: 23px">
                {{ __('Department manager') }}
            </span>
        </div>
        <div class="card-bordy p-3">
            <div class="h-space">
                <div class="form-group w-20">
                    <label>
                        {{ __('Choose') }} {{__("Department") }}
                    </label>
                    <select class="custom-select select2 select_department" name="Symbols">
                        <option value="">
                            {{ __('Choose') }} {{__("Department") }}
                        </option>
                        @foreach ($department as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->department }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-info w-10 filter">{{ __('Filter') }}</button>
                @if (Auth::user()->level == 9999)
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#test">
                        {{__('Add')}} {{__("Department")}}
                    </button>
                @endif
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
                <form id="form" action="{{ route('admin.create_department') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Tên Phòng Ban:</label>
                            <input type="text" name="department" class="form-control" id="department"
                                placeholder="Nhập họ và tên" required>
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
            // Lấy giá trị từ thuộc tính value của button
            var route = `${window.location.origin}/api/update_department/` + id;
            $('#form').attr('action', '/admin/edit_department/' + id);
            $('#form').attr('method', 'GET');

            $.ajax({
                url: route,
                type: 'get',
                success: function(response) {
                    $("#department").val(response.department);
                },
                error: function(xhr, status, error) {}
            });

        });

        var route = `${window.location.origin}/api/get-department`;
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
                    data: 'id',
                    defaultContent: '',
                    title: `{{ __('Symbols') }} {{__('Department')}}`,
                    render: function(data) {
                        return data
                    }
                },
                {
                    data: 'department',
                    defaultContent: '',
                    title: `{{ __('Name') }} {{__('Department')}}`,
                    render: function(data) {
                        return data
                    }
                }, {
                    data: 'id',
                    defaultContent: '',
                    title: `{{ __('Actions') }}`,
                    render: function(data, type, row) {
                        return `
                        <button id="updatetable" type="button" class="btn btn-primary update" value='{{ $item->id }}' data-toggle="modal" data-target="#test">
                            {{ __('Update') }}
                        </button>
                        <a href="{{ route('admin.delete_department', $item->id) }}" type="button" class="btn btn-danger">{{ __('Delete') }}</a>
                        `;
                    }
                }

            ]
        })
        $('.filter').on('click', function() {
            table.ajax.reload()
        })





        $(document).ready(function() {
            $('#test').on('hidden.bs.modal', function() {
                $('#form').attr('action', '/admin/create_department/');
                $('#form').attr('method', 'post');
                $("#password").show();
                $('#form')[0].reset();
            });
        });
    </script>
@endpush
