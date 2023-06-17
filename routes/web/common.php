<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SingerController;
use App\Http\Middleware\Check;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\Checklecturers;
use App\Http\Middleware\CheckPoint;

Route::get('/change-language/{language}',[HomeController::class, 'change_language'])->name('home.changeLanguage');

Route::get('/', function () {
    return view('welcome');
});
Route::middleware([CheckAdmin::class])->group(function () {
    Route::get('/home/', [HomeController::class, 'index'])->name('home')->middleware('auth');
    Route::get('/admin/student', [HomeController::class, 'student'])->name('admin.student')->middleware('auth');
    Route::post('/admin/create', [HomeController::class, 'create'])->name('admin.create')->middleware('auth');
    Route::get('/admin/delete/{id}', [HomeController::class, 'delete'])->name('admin.delete')->middleware('auth');
    Route::get('/admin/update/{id}', [HomeController::class, 'update'])->name('admin.update')->middleware('auth');
    Route::get('/admin/view/{id}', [HomeController::class, 'viewMail'])->name('admin.viewMail')->middleware('auth');
    Route::get('/admin/send-mail/{id}', [HomeController::class, 'sendMail'])->name('admin.sendMail')->middleware('auth');
    Route::get('/admin/department', [HomeController::class, 'department'])->name('admin.department')->middleware('auth');
    Route::post('/admin/create_department', [HomeController::class, 'create_department'])->name('admin.create_department')->middleware('auth');
    Route::get('/admin/delete_department/{id}', [HomeController::class, 'delete_department'])->name('admin.delete_department')->middleware('auth');
    Route::get('/admin/edit_department/{id}', [HomeController::class, 'edit_department'])->name('admin.edit_department')->middleware('auth');
    Route::get('/admin/lecturers', [HomeController::class, 'lecturers'])->name('admin.lecturers');
    Route::post('/admin/addlecturers', [HomeController::class, 'addlecturers'])->name('admin.addlecturers');
    Route::get('/home/', [HomeController::class, 'index'])->name('home')->middleware('auth');
    Route::get('/student/point', [HomeController::class, 'point'])->name('point')->middleware('auth');
    Route::get('/student/update_point', [HomeController::class, 'update_point'])->name('update_point')->middleware('auth');
    Route::get('/dashboard', function () {
        return view('dashboard');
})->middleware(['auth'])->name('dashboard');


    
});
Route::middleware([Check::class])->group(function () {
    Route::get('/student/', [HomeController::class, 'view_student'])->name('view_student')->middleware('auth');;
});

Route::middleware([CheckPoint::class])->group(function () {
    Route::get('/student/point', [HomeController::class, 'point'])->name('point')->middleware('auth');
    Route::get('/student/update_point', [HomeController::class, 'update_point'])->name('update_point')->middleware('auth');
    Route::get('/admin/send-mail/{id}', [HomeController::class, 'sendMail'])->name('admin.sendMail')->middleware('auth');

});

Route::middleware([Checklecturers::class])->group(function () {
    Route::get('/lecturers/', [HomeController::class, 'view_lecturers'])->name('lecturers')->middleware('auth');
});



