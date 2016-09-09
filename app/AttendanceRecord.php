<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lesson_id', 'student_id', 'presence'
    ];
}
