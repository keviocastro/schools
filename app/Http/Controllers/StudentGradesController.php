<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StudentGrades;
use App\Http\Requests;

class StudentGradesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->apiHandler->parseMultiple(new StudentGrades);
        
        return $result->getBuilder()->paginate();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validationForStoreAction($request, [
            'grade' => 'required|numeric|max:10|min:0',
            'student_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'assessment_id' => 'required|numeric',
            'owner_person_id' => 'required|numeric',
        ]);
        
        $studentGrades = StudentGrades::create($request->all());

        return $this->response->created("/studentGrades/{$studentGrades->id}", $studentGrades);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = $this->apiHandler->parseSingle(new StudentGrades,$id);
        return $result->getResult();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validationForUpdateAction($request, [
            'grade' => 'required|numeric|max:10|min:0',
            'student_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'assessment_id' => 'required|numeric',
            'owner_person_id' => 'required|numeric',
        ]);

        $studentGrades = StudentGrades::findOrFail($id);
        $studentGrades->update($request->all());

        return $studentGrades;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
