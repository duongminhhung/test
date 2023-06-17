@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="row" style="margin: 10px 20px 0 355px">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{$students}}</h3>
                        <p>{{__('Students')}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="{{route('admin.student')}}" class="small-box-footer">{{__('Student Management')}}<i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{$department}}<sup style="font-size: 20px"></sup></h3>
                        <p>{{__('Department')}}</p>

                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{route('admin.department')}}" class="small-box-footer">{{__('Department manager')}}<i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{$lecturers}}</h3>

                        <p>{{__('Lecturer')}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="{{route('admin.lecturers')}}" class="small-box-footer">{{__('Instructor Management')}}<i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
    @endsection
    @push('scripts')
        <script>
            
        </script>
    @endpush
