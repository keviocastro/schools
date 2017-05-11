<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\StudentProgressSheet;

class StudentProgressSheetControllerTest extends TestCase
{
     /**
     * @covers App\Http\Controllers\StudentProgressSheetController::index
     *
     * @return void
     */
    public function testIndex()
    {
        $studentProgressSheet = factory(StudentProgressSheet::class)->create();
        
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
    
    /**
     * @covers App\Http\Controllers\StudentProgressSheetController::show
     *
     * @return void
     */
    public function testShow()
    {
        $progressSheetItem = factory(App\ProgressSheetItem::class)->create();
        $studentProgressSheet = factory(StudentProgressSheet::class)->create(["progress_sheet_item_id" => $progressSheetItem->id]);

        $structure = [
            'student_progress_sheet' => [
                "id" => $studentProgressSheet->id,
                "option_identifier" => $studentProgressSheet->option_identifier,
                "progress_sheet_item" => $progressSheetItem->toArray(),
                "progress_sheet_item_id" => $progressSheetItem->id,
                "school_calendar_phase_id" => $studentProgressSheet->school_calendar_phase_id,
                "school_class_id" => $studentProgressSheet->school_class_id,
                "student_id" => $studentProgressSheet->student_id
            ]
        ];

        $this->get("api/student-progress-sheets/{$studentProgressSheet->id}".
            "?_with=progressSheetItem",
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJsonEquals($structure);
    }

    /**
     * @covers App\Http\Controllers\StudentProgressSheetController::update
     *
     * @return void
     */
    public function testUpdate()
    {
        $studentProgressSheet = factory(StudentProgressSheet::class)->create();
        $studentProgressSheet_changed = factory(StudentProgressSheet::class)->make();

        $json = [
            "student_progress_sheet" => [
                "id" => $studentProgressSheet->id,
                "option_identifier" => $studentProgressSheet_changed->option_identifier,
                "progress_sheet_item_id" => $studentProgressSheet_changed->progress_sheet_item_id,
                "school_class_id" => $studentProgressSheet_changed->school_class_id,
                "school_calendar_phase_id" => $studentProgressSheet_changed->school_calendar_phase_id,
                "student_id" => $studentProgressSheet_changed->student_id
            ]
        ];

        $this->put("api/student-progress-sheets/{$studentProgressSheet->id}",
            $studentProgressSheet_changed->toArray(),
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJsonEquals($json);
    }
}
