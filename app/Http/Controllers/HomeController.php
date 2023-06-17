<?php

namespace App\Http\Controllers;

use App\Models\department;
use App\Models\lecturers;
use App\Models\student;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Session;
use Mail;
use App\Mail\DemoMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail as FacadesMail;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    private $im;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /**
     * Show the application welcome.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function welcome()
    {
        return view('welcome');
    }
    public function index(Request $request)
    {   
        $lecturers = User::where('level', 1)->count();
        $students = User::where('level', 2)->count();
        $department = count(department::all());
        
        // dd(count($department));
        // dd($students);
        return view('dashboard',compact('lecturers', 'students','department'));
    }
    public function student(Request $request){
        $students = DB::table('users')
            ->select('*')
            ->join('student', 'student.id_student', '=', 'users.id')
            ->join('department', 'department.id', '=', 'student.department')
            ->get();
            $department = department::all();
        return view('admin.student',compact('students','department'));
    }
    public function create(Request $request){
        $validatedData  = $request->validate([
            'username' => 'required|unique:users,username',
            'email' => 'required|unique:users,email',
        ]);
        // dd($request->all());        
        $name = $request->get('name');
        $username = $request->get('username');
        $email = $request->get('email');
        $password = Hash::make($request->get('password'));
        $level = 2;
        $department = $request->get('department');
        $id =  DB::table('users')->insertGetId([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'level' => $level,
        ]);
        $product = new student();
        $product->department = $department;
        $product->id_student = $id;
        $product->scores = '';
        $product->save();

         DB::table('point')->insertGetId([
            'id_student' => $id,
            'maths' => 'Chưa có điểm',
            'literature' => 'Chưa có điểm',
            'chemistry' => 'Chưa có điểm',
            'physical' => 'Chưa có điểm',
        ]);
        return redirect()->route('admin.student');
    }
    public function delete($id){
        User::where('id',$id)->delete();
        student::where('id_student',$id)->delete();
        lecturers::where('id_lecturers',$id)->delete();
        DB::table('point')->where('id_student', $id)->delete();
        Session::flash('delete', "Xóa thành công");
        return redirect()->route('admin.student');
    }
    public function update($id,Request $request){
        
        
        $user = User::where("id", $id)->update([
            "name" => $request->name,
            "username" => $request->username,
            "email" => $request->email,
        ]);
        
        if($request->department == "Chọn Khoa"){
            $student = student::where("id_student", $id)->update([
                "scores" => $request->grade,
            ]);
        }
        Session::flash('message', "Cập nhật thành công");
        return Redirect::back();

    }

    public function change_language($language)
    {
        Session::put('language', $language);
        return redirect()->back();
    }
    public function viewMail($id){
        $students = DB::table('users')
        ->select('*')
        ->join('student', 'student.id_student', '=', 'users.id')
        ->join('department', 'department.id', '=', 'student.department')
        ->get();
        // dd($students);
        $department = department::all();
        // dd($department);
    return view('admin.mail',compact('students','department'));
    }
    public function sendMail($id){
        $student = User::find($id);
        // dd($student);
        $students = DB::table('point')
        ->select('*')
        ->join('users', 'users.id', '=', 'point.id_student')
        ->where('users.id', '=', $id)
        ->get();
        // dd($students);
            $department = department::all();
        $mailData = [
            'title' =>'Điểm số của sinh viên',
            'body' => '',
            'students'=> $students,
            'department'=> $department,
        ];
        Mail::to($students[0]->email)->send(new DemoMail($mailData));
        return Redirect::back();
    }
    public function department(){
        $department = department::all();
        // dd($department);
        return view('admin.department',compact('department'));
    }
    public function create_department(Request $request){
        DB::table('department')->insert([
            'department' => $request->get('department'),
        ]);
        return redirect()->route('admin.department');
    }
    public function delete_department($id){
        department::where('id',$id)->delete();

        Session::flash('delete', "Xóa thành công");
        return redirect()->route('admin.department');
    }
    public function edit_department($id,Request $request){
        department::where("id", $id)->update([
            "department" => $request->department,
        ]);
        Session::flash('message', "Cập nhật thành công");
        return redirect()->route('admin.department');
    }
    public function lecturers(){
        $lecturers = DB::table('users')
        ->select('*')
        ->join('lecturers', 'lecturers.id_lecturers', '=', 'users.id')
        ->get();
        // dd($lecturers);
        $department = department::all();
        return view('admin.lecturers',compact('lecturers','department'));
    }
    public function addlecturers(Request $request){
        $validatedData  = $request->validate([
            'username' => 'required|unique:users,username',
            'email' => 'required|unique:users,email',
        ]);
        // dd($request->all());        
        $name = $request->get('name');
        $username = $request->get('username');
        $email = $request->get('email');
        $password = Hash::make($request->get('password'));
        $level = 1;
        $department = $request->get('department');
        $id =  DB::table('users')->insertGetId([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'level' => $level,
        ]);
        $id =  DB::table('lecturers')->insert([
            'id_lecturers' => $id,
            'department' => $department,
        ]);
        return Redirect::back();
    }
    public function view_student(){
        
        $students = DB::table('users')
            ->select('*')
            ->join('student', 'student.id_student', '=', 'users.id')
            ->join('department', 'department.id', '=', 'student.department')
            // ->joint('point','point.id_student','=','users.id')
            ->where('student.id_student','=', Auth::user()->id)
            ->get();

            $point = DB::table('point')
            ->select('*')
            ->where('id_student','=', Auth::user()->id)
            ->get();
        return view('student.index',compact('students','point'));
    }
    public function view_lecturers(){
        $students = DB::table('users')
            ->select('*')
            ->join('student', 'student.id_student', '=', 'users.id')
            ->join('department', 'department.id', '=', 'student.department')
            ->get();
            $department = department::all();
        return view('lecturers.index',compact('students','department'));
    }
    public function point(){
        // $point = DB::table('point')->get();
        $students = DB::table('point')
        ->select('*')
        ->join('users', 'users.id', '=', 'point.id_student')
        // ->join('department', 'department.id', '=', 'student.department')
        ->get();

        // dd($students);
        return view('admin.point',compact('students'));
    }
    public function update_point(Request $request){
        $user = User::where("id", $request->id)->update([
            "name" => $request->name,
        ]);
        DB::table('point')
        ->where('id_student', $request->id)  // find your user by their email
        ->limit(1)  // optional - to ensure only one record is updated.
        ->update(array(
            'maths' => $request->maths,
            'literature' => $request->literature,
            'chemistry' => $request->chemistry,
            'physical' => $request->physical,
      )); 
      return Redirect::back();
    }


}
