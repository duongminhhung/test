@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <span class="text-bold" style="font-size: 23px">
                {{ __('Student Management') }}
            </span>
        </div>
        <div class="card-bordy p-3">
            <div class="h-space">
                <div class="form-group w-20">
                    <label>{{ __('Choose a student') }}</label>
                    <select class="custom-select select2 username select_student" name="Symbols">
                        <option value="">
                            {{ __('Choose a student') }}
                        </option>
                        @foreach ($students as $item)
                            <option value="{{ $item->id_student }}">
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-info w-10 filter">{{ __('Filter') }}</button>
                @if (Auth::user()->level == 9999)
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#test">
                        {{__('Add')}} {{__('Students')}}
                    </button>
                @endif
                <a href="{{route('point')}}" type="submit" class="btn btn-success w-10 ">{{ __('Điểm số') }}</a>


            </div>
            @if (Session::has('message'))
                <div class="alert alert-info">{{ Session::get('message') }}</div>
            @endif
            <table class="table table-striped table-hover table-bordered" id="tableStudent" width="100%">

            </table>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="test" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Add')}} {{__('Students')}}</h5>
                    
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form" action="{{ route('admin.create') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">{{__('First And Last Name')}}:</label>
                            <input type="text" name="name" class="form-control" id="name"
                                placeholder="{{__('First And Last Name')}}" required>
                        </div>
                        <div class="form-group">
                            <label for="name">{{__('User Name')}}</label>
                            <input type="text" class="form-control" id="username" name="username"
                                placeholder="{{__('User Name')}}" required>
                        </div>
                        <div class="form-group">
                            <label for="name">{{__('Password')}}</label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="{{__('Password')}}"required>
                        </div>
                        <div class="form-group">
                            <label for="email">{{__('Email')}}:</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Email"required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Department') }}</label>
                            <select class="custom-select select2 username" name="department">
                                <option>
                                    {{ __('Department') }}
                                </option>
                                @foreach ($department as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->department }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                            <button type="submit" id="bt" class="btn btn-primary">{{__('Add')}} {{__('Students')}}</button>
                        </div>
                </form>
            </div>
        </div>
    </div>

    </div>
@endsection
@push('scripts')
    <script>
        $(".select2").select2({});
        $(document).on('click', 'button.btn.btn-primary.update', function() {
            var id = $(this).val();
            // console.log(id);
            var route = `${window.location.origin}/api/update/` + id;
            $('#form').attr('action', '/admin/update/' + id);
            $('#form').attr('method', 'GET');

            $.ajax({
                url: route,
                type: 'get',
                success: function(response) {
                    $("#name").val(response.name);
                    $("#username").val(response.username);
                    $("#password").hide();
                    $("#password").val(response.password);
                    $("#email").val(response.email);
                    $("#grade").val(response.scores);
                    $("#bt").html('Lưu lại');
                },
                error: function(xhr, status, error) {}
            });

        });
        var route = `${window.location.origin}/api/get-student`;
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
                    d.name = $('.select_student').val()
                }
            },
            columns: [{
                    data: 'id_student',
                    defaultContent: '',
                    title: `{{ __('Symbols') }} {{__("Students")}}`,
                    render: function(data) {
                        return data
                    }
                },
                {
                    data: 'name',
                    defaultContent: '',
                    title: `{{ __('Name') }} {{__("Students")}}`,

                    render: function(data) {
                        return data
                    }
                }, 
                {
                    data: 'email',
                    defaultContent: '',
                    title: `{{ __('Email') }}`,
                    render: function(data) {
                        return data
                    }
                }, 
                {
                    data: 'department',
                    defaultContent: '',
                    title: `{{ __('Department') }}`,
                    render: function(data) {
                        return data
                    }
                }, 
                {
                    data: 'id_student',
                    defaultContent: '',
                    title: `{{ __('Actions') }}`,
                    render: function(data, type, row) {
                        const sendMailUrl = `{{ route('admin.sendMail', '') }}/${data}`;
                        const routeDelete = `{{ route('admin.delete', '') }}/${data}`;
                        return `                       
                        <a href="${sendMailUrl}" type="button" class="btn btn-info">{{ __('Send Mail') }}</a>
                                <button type="button" class="btn btn-primary update" value='${data}'
                                    data-toggle="modal" data-target="#test">
                                    {{ __('Update') }}
                                </button>
                                <a href="${routeDelete}" type="button" class="btn btn-danger">{{ __('Delete') }}</a>
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
                $('#form').attr('action', '/admin/create/');
                $('#form').attr('method', 'post');
                $("#password").show();
                $('#form')[0].reset();
            });
        });
        // } );
    </script>
@endpush
