<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\ProgressSheet;

class ProgressSheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->parseMultiple(new ProgressSheet, ['name','options']);

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
                // '{attribute}' => '{validation}',
            ]);
        $progressSheetController = ProgressSheet::create($request->all());

        return $this->response->created("/progress-sheets/{$progressSheetController->id}", $progressSheetController);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $_attach = explode(',', $request->input('_attach'));

        $result = $this->apiHandler
            ->parseSingle(New ProgressSheet, $id)
            ->getResultOrFail();

        if (in_array('groups',$_attach)) {
            $result->groups = $result->groups()->toArray();
        }

        return $result;

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
            // 'attribute' => 'rule',
            ]);

        $progressSheetController = ProgressSheet::findOrFail($id);
        $progressSheetController->update($request->all());

        return $progressSheetController;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $progressSheet = ProgressSheet::findOrFail($id);
        $progressSheet->delete();

        return $this->response->noContent();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexItems($id)
    {   
        $itemsQuery = ProgressSheet::findOrFail($id)->items();   
        $result = $this->parseMultiple($itemsQuery);

        return $result;
    }
}
