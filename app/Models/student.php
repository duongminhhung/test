<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class student extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_student';
    public $timestamps = false;
    protected $fillable = [
        'department', 'scores', 'id_student',
    ];
    protected $table = 'student';

    public function lecturers()
    {
        return $this->belongsToMany(lecturers::class, 'LecturerStudent', 'id_student', 'id_lecturer');
    }
}
