<?php

namespace App\Http\Controllers;

use App\Lesson;
use App\Subject;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Teacher;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{

    /**
     * Busca as turmas do professor
     *
     * @param  Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function schoolClasses(Request $request, $teacher_id)
    {
        $query = Lesson::
            join('school_classes', 'lessons.school_class_id', '=', 'school_classes.id')
            ->groupBy('lessons.subject_id', 'lessons.school_class_id')
            ->where('lessons.teacher_id', '=', $teacher_id);

        $result = $this->parseMultiple($query);

        $result->transform(function($item, $key){
            return ['schoolClass' => $item->schoolClass, 'subject' => $item->subject];
        });

        return $result->toArray();
    }
}
