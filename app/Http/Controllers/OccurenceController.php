<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Occurence;
use Illuminate\Http\Request;

class OccurenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->parseMultiple(new Occurence,['comment']);
        
        return $result;
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
            'level_id' => 'required|numeric|exists:levels,id',
            'comment' => 'required|string',
            'about_person_id' => 'required|numeric|exists:people,id',
        ], '', true);
        
        $occurence = Occurence::create($request->all());

        return $this->response->created("/occurence/{$occurence->id}", $occurence);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = $this->apiHandler->parseSingle(new Occurence,$id);
        return $result->getResultOrFail();
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
            'level_id' => 'required|numeric|exists:levels,id',
            'comment' => 'required|string',
            'about_person_id' => 'required|numeric|exists:people,id',
        ]);

        $occurence = Occurence::findOrFail($id);
        $occurence->update($request->all());

        return $occurence;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $occurence = Occurence::findOrFail($id);
        $occurence->delete();

        return $this->response->noContent();
    }
}
