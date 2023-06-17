<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lecturers extends Model
{
    use HasFactory;
    protected $table = 'lecturers';
    protected $fillable = [
        'name_lecture', 'department'
    ];

    public function student()
    {
        return $this->belongsToMany(student::class, 'LecturerStudent', 'id_lecturer', 'id_student');
    }
    
}
