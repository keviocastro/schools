<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StudentProgressSheetControllerTest extends TestCase
{
     /**
     * @covers App\Http\Controllers\StudentProgressSheetController::index
     *
     * @return void
     */
    public function testIndex()
    {
        $studentProgressSheet = factory(App\StudentProgressSheet::class)->create();
        
        $this->get('api/student-progress-sheets?_sort=-id',
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($studentProgressSheet->toArray());
    }

    /**
     * @covers App\Http\Controllers\StudentProgressSheetController::store
     *
     * @return void
     */
    public function testStore()
    {
        $progressSheet = factory(App\ProgressSheet::class)->create();
        $itemsGroup1 = factory(App\ProgressSheetItem::class, 5)->create([
                'progress_sheet_id' => $progressSheet->id,
                'group_id' => factory(App\Group::class)->create()->id
            ]);
        $itemsGroup2 = factory(App\ProgressSheetItem::class, 5)->create([
                'progress_sheet_id' => $progressSheet->id,
                'group_id' => factory(App\Group::class)->create()->id,
            ]);

        $student = factory(App\Student::class)->create();
        $schoolClass = factory(App\SchoolClass::class)->create();
        $phases = factory(App\SchoolCalendarPhase::class,4)->create();
        factory(App\SchoolClassStudent::class)->create([
                'school_class_id' => $schoolClass->id,
                'student_id' => $student->id
            ]);

        $studentItems = array();
        foreach ($itemsGroup1 as $item) {
            array_push($studentItems, 
                factory(App\StudentProgressSheet::class)->make([
                    'student_id' => $student->id,
                    'progress_sheet_item_id' => $item->id,
                    'school_calendar_phase_id' => $phases[0]->id,
                    'school_class_id' => $schoolClass->id,
                ])->toArray()
            );
        }

        $resultItemStructure = array_keys(factory(App\StudentProgressSheet::class)->make()->toArray());

        // Store Multiple records
        $this->post('api/student-progress-sheets',
                $studentItems,
                $this->getAutHeader()
            )
            ->assertResponseStatus(201)
            ->seeJsonStructure([
                    'student_progress_sheets' => ['*' => $resultItemStructure]
                ]);

        $item = factory(App\StudentProgressSheet::class)->make([
                    'student_id' => $student->id,
                    'progress_sheet_item_id' => $item->id,
                    'school_calendar_phase_id' => $itemsGroup2[0]->id,
                    'school_class_id' => $schoolClass->id,
                ])->toArray();

         // Store Single record
        $this->post('api/student-progress-sheets',
                $item,
                $this->getAutHeader()
            )
            ->assertResponseStatus(201)
            ->seeJsonStructure([
                    'student_progress_sheet' => $resultItemStructure
                ]);
    }
}
