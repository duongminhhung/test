<?php

use App\Http\Controllers\Api\Account\AccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiSystemController;
use App\Models\department;
use App\Models\lecturers;
use Illuminate\Http\Request;


Route::get('export-stock',[ApiSystemController::class, 'export_stock']);
Route::get('create-command-export-materials',[ApiSystemController::class, 'create_command_export_materials']);
Route::get('select-materials',[ApiSystemController::class, 'select_material']);
Route::get('get-data-materials-location',[ApiSystemController::class, 'get_data_materials_location']);
Route::get('get_materials_function',[ApiSystemController::class, 'get_materials_function']);

Route::get('chart-api',[ApiSystemController::class, 'chart_api']);
Route::get('chart-api-2',[ApiSystemController::class, 'chart_api_2']);

Route::get('read-file-mishin-shiage',[ApiSystemController::class, 'read_file_mishin_shiage']);

use App\Models\Singer;
use App\Models\student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

Route::get('/update/{id}', function (Request $request) {
    $id = $request->id;
    // dd($id);
    $user = User::find($id);
    $student = student::where('id_student',$id)->get();
    if(count($student) ==0){
        $lecturers = lecturers::where('id_lecturers',$id)->get();
        $department = department::find($lecturers[0]->department);
        return response()->json([
            'name'   => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'password' => $user->password,
            'department'=> $department,
        ]);
        
    }else{
    $department = department::find($student[0]->department);
    return response()->json([
        'name'   => $user->name,
        'username' => $user->username,
        'email' => $user->email,
        'password' => $user->password,
        'department'=> $department,
        'scores'=>$student[0]->scores
    ]);
    }
  
});


Route::get('/update_department/{id}', function (Request $request) {
    $id = $request->id;
    $department = department::find($id);
    // dd($department);
    return response()->json([
        'department'   => $department->department,
    ]);
});
Route::get('/get-department/', function (Request $request) {
    // dd($request->length);
    $name = $request->name;
    if($name == null){
        $department = DB::table('department')
       ->paginate($request->length);
    }else{
        $department = DB::table('department')
    ->where('id', '=', $name)
    ->paginate($request->length);
    }
 
    return response()->json([
        'data'   => $department->toArray()['data'],
    ]);
});

Route::get('/get-student/', function (Request $request) {
    $students = DB::table('users')
    ->select('*')
    ->join('student', 'student.id_student', '=', 'users.id')
    ->join('department', 'department.id', '=', 'student.department')
    ->get();

    $name = $request->Symbols;
    // dd($name);
    if($name == null){
        $students = DB::table('users')
    ->select('*')
    ->join('student', 'student.id_student', '=', 'users.id')
    ->join('department', 'department.id', '=', 'student.department')
    ->paginate($request->length);
    }else{
        $students = DB::table('users')
        ->select('*')
        ->join('student', 'student.id_student', '=', 'users.id')
        ->join('department', 'department.id', '=', 'student.department')
        ->where('users.id', '=', $name)
        ->paginate($request->length);
    }
    $department = department::all();
    return response()->json([
        'data'   => $students->toArray()['data'],
        'department' => $department
    ]);
});
Route::get('/get-lecturers/', function (Request $request) {
    $students = DB::table('users')
    ->select('*')
    ->join('student', 'student.id_student', '=', 'users.id')
    ->join('department', 'department.id', '=', 'student.department')
    ->get();

    $name = $request->name;
    // dd($name);
    if($name == null){
    $lecturers = DB::table('users')
    ->select('*')
    ->join('lecturers', 'lecturers.id_lecturers', '=', 'users.id')
    ->join('department', 'department.id', '=', 'lecturers.department')
    ->paginate($request->length);
    // dd($lecturers->items());
    }else{
        $lecturers = DB::table('users')
        ->select('*')
        ->join('lecturers', 'lecturers.id_lecturers', '=', 'users.id')
        ->join('department', 'department.id', '=', 'lecturers.department')
        ->where('users.id', '=', $name)
        ->paginate($request->length);
    }
    $department = department::all();
    return response()->json([
        'data'   => $lecturers->items(),
        'department' => $department
    ]);
});


Route::get('/point/', function (Request $request) {
    // dd($request->all());
    $students = '';
    if($request->name == null){
        $students = DB::table('point')
        ->select('*')
        ->join('users', 'users.id', '=', 'point.id_student')
        // ->join('department', 'department.id', '=', 'student.department')
        ->paginate($request->length);
        return response()->json([
            'data'   => $students->toArray()['data']
        ]);
    }else{
        $students = DB::table('point')
        ->select('*')
        ->join('users', 'users.id', '=', 'point.id_student')
        ->where('users.id', '=', $request->name)
        ->paginate(10);
       return response()->json([
        'data'   => $students->items(),
        ]);
    }
   
});


Route::get('update_point/{id}',function (Request $request){
    $id = $request->id;
    $students = DB::table('point')
    ->select('*')
    ->join('users', 'users.id', '=', 'point.id_student')
    ->where('users.id', '=', $id)
    ->get();

    return response()->json([
        'data'   => $students->toArray(),
    ]);
});



