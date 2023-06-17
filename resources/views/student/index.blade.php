@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <span class="text-bold" style="font-size: 23px">
                {{ __('Sinh Viên') }} {{Auth::user()->name}}
            </span>
        </div>
        <div class="card-bordy p-3">
            @if (Session::has('message'))
                 <div class="alert alert-success">{{ Session::get('message') }}</div>
            @endif
            @if (Session::has('delete'))
                 <div class="alert alert-danger">{{ Session::get('delete') }}</div>
            @endif
            <table class="table table-striped table-hover table-bordered" id="tableStudent" width="100%"
            style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 8px; border: 1px solid #ddd; background-color: #f2f2f2; text-align: left;">
                        {{ __('Mã sinh viên') }}</th>
                    <th style="padding: 8px; border: 1px solid #ddd; background-color: #f2f2f2; text-align: left;">
                        {{ __('Name') }}</th>
                    <th style="padding: 8px; border: 1px solid #ddd; background-color: #f2f2f2; text-align: left;">
                        {{ __('Email') }}</th>
                    <th style="padding: 8px; border: 1px solid #ddd; background-color: #f2f2f2; text-align: left;">
                        {{ __('Khoa') }}</th>
                    <th style="padding: 8px; border: 1px solid #ddd; background-color: #f2f2f2; text-align: left;">
                        {{ __('Điểm kết thúc môn') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $item)
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">{{ $item->id_student }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">{{ $item->name }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">{{ $item->email }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">{{ $item->department }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">
                            <button type="button" class="btn btn-primary update" value='${data}'
                                    data-toggle="modal" data-target="#test">
                                    {{ __('Xem điểm số') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
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
                <form>
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Tên sinh viên:</label>
                            <input type="text" name="name" class="form-control" id="name" value="{{$students[0]->name}}" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="name">Toán:</label>
                            <input type="number" name="maths" class="form-control" id="maths" value="{{$point[0]->maths}}" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="name">Văn Học:</label>
                            <input type="number" name="literature" class="form-control" value="{{$point[0]->literature}}" id="literature"readonly required>
                        </div>
                        <div class="form-group">
                            <label for="name">Lý:</label>
                            <input type="number" name="physical" class="form-control" value="{{$point[0]->physical}}" id="physical"readonly required>
                        </div>
                        <div class="form-group">
                            <label for="name">Hóa:</label>
                            <input type="number" name="chemistry" class="form-control" value="{{$point[0]->chemistry}}" id="chemistry"readonly required>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        
    </script>
@endpush
