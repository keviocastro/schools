<?php

use Tests\TestCase;
use App\StudentProgressSheet;

class StudentProgressSheetControllerTest extends TestCase
{

    /**
     * Cria na base de dados uma ficha de avaliação completa
     *
     * @return void
     */
    public function createProgressSheet(){

        // Ficar ficha de avaliação com itens
        $this->progressSheet = factory(App\ProgressSheet::class)->create();
        $this->itemsGroup1 = factory(App\ProgressSheetItem::class, 3)->create([
                'progress_sheet_id' => $this->progressSheet->id,
                'group_id' => factory(App\Group::class)->create()->id
            ]);
        $this->itemsGroup2 = factory(App\ProgressSheetItem::class, 2)->create([
                'progress_sheet_id' => $this->progressSheet->id,
                'group_id' => factory(App\Group::class)->create()->id,
            ]);

        $this->student = factory(App\Student::class)->create();
        $this->schoolClass = factory(App\SchoolClass::class)->create();
        factory(App\SchoolClassStudent::class)->create([
                'student_id' => $this->student->id,
                'school_class_id' => $this->schoolClass->id,
            ]);
        $this->phases = factory(App\SchoolCalendarPhase::class, 4)->create();
    }

    /**
     * Criar resultados do aluno para todos os itens de avaliação da ficha, 
     * em todas as fases do ano
     * 
     * @return void
     */
    public function createStudentProgreSheet(){

        $this->createProgressSheet();

        $options = $this->progressSheet->options;
        
        foreach ($this->progressSheet->items as $item) {

            foreach ($this->phases as $phase) {

                factory(App\StudentProgressSheet::class)->create([
                    'student_id' => $this->student->id,
                    'progress_sheet_item_id' => $item->id,
                    'school_calendar_phase_id' => $phase->id,
                    'school_class_id' => $this->schoolClass->id,
                    'option_identifier' => $options[rand(0, count($options) -1)]['identifier'],
                ])->toArray();
            }

        }


    }


     /**
     * @covers App\Http\Controllers\StudentProgressSheetController::index
     *
     * @return void
     */
    public function testIndex()
    {
        $studentProgressSheet = factory(App\StudentProgressSheet::class)->create();
        $studentProgressSheet->load('progressSheetItem', 'schoolCalendarPhase', 'student', 'schoolClass');

        $this->get('api/student-progress-sheets?_sort=-id'.
                '&_with=progressSheetItem,schoolCalendarPhase,schoolClass,student',
        	$this->getAutHeader())
        	->assertStatus(200)
        	->assertJsonFragment($studentProgressSheet->toArray());
    }

    /**
     * @covers App\Http\Controllers\StudentProgressSheetController::index
     *
     * @return void
     */
    public function testIndexParamGroupBy()
    {
        $this->createStudentProgreSheet();

        $items = App\StudentProgressSheet::where([
            ['school_class_id', '=', $this->schoolClass->id],
            ['student_id', '=', $this->student->id],
        ])->get()->toArray();

        $itemsGrouped = collect($items)->groupBy('school_calendar_phase_id');

        // Existing group
        $this->get('api/student-progress-sheets'.
            "?student_id={$this->student->id}".
                "&school_class_id={$this->schoolClass->id}".
            '&_group_by=school_calendar_phase_id',
            $this->getAutHeader())
            ->assertStatus(200);

        // Not existing group
        $this->get('api/student-progress-sheets'.
            "?student_id={$this->student->id}".
                "&school_class_id={$this->schoolClass->id}".
            '&_group_by=not_exist_group',
            $this->getAutHeader())
            ->assertStatus(422);
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
            ->assertStatus(201)
            ->assertJsonFragmentStructure([
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
            ->assertStatus(201)
            ->assertJsonFragmentStructure([
                    'student_progress_sheet' => $resultItemStructure
                ]);
    }

    /**
     * @covers App\Http\Controllers\StudentProgressSheetController::store
     *
     * Teste de condição:
     *  O aluno pode ter somente um resposta de um item de avaliação, em uma fase do ano, para uma turma.
     * Isso significa que um requisição com a combinação de parametros progress_sheet_item_id, school_calendar_phase_id,
     * school_class_id e student_id, já existirem, um novo registro não será criado e sim atualizado.
     *
     * @link https://github.com/keviocastro/schools/issues/4
     * 
     * @return void
     */
    public function testStoreConditionUniqueAnswer()
    {
        $this->createProgressSheet();

        $option_identifier = $this->progressSheet->options[0]['identifier'];
        $option_identifier_changed = $this->progressSheet->options[1]['identifier'];
        
        $item = factory(App\StudentProgressSheet::class)->create([
            'student_id' => $this->student->id,
            'progress_sheet_item_id' => $this->itemsGroup1[0]->id,
            'school_calendar_phase_id' => $this->phases[0]->id,
            'school_class_id' => $this->schoolClass->id,
            'option_identifier' => $option_identifier,
        ])->toArray();

        $item['option_identifier'] = $option_identifier_changed;
        $item['appliedAction'] = 'updated';

        // Item já existe para o estudante na turma, então o registro é atualiza e retorna com status de resposta 200.
        $this->post('api/student-progress-sheets',
                $item,
                $this->getAutHeader()
            )
            ->assertStatus(201)
            ->assertJsonFragmentEquals([
                    'student_progress_sheet' => $item
                ]);

        $item = factory(App\StudentProgressSheet::class)->make([
            'student_id' => $this->student->id,
            'progress_sheet_item_id' => $this->itemsGroup1[0]->id,
            'school_calendar_phase_id' => $this->phases[1]->id, // Em outra fase do ano o aluno não tem registro de resposta.
            'school_class_id' => $this->schoolClass->id,
        ])->toArray();

        $lastId = \App\StudentProgressSheet::orderBy('id', 'desc')->first()->id;
        $item['appliedAction'] = 'created';
        $item['id'] = $lastId + 1;

        // Item náo existe para o estudante então ele é criado
        $this->post('api/student-progress-sheets',
            $item,
            $this->getAutHeader()
            )
            ->assertStatus(201)
            ->assertJsonFragmentEquals(['student_progress_sheet' => $item]);
    }

     /**
     * @covers App\Http\Controllers\StudentProgressSheetController::store
     *
     * Teste de condição:
     *  Só pode ser registrado o resultado de um aluno para um item de avaliação se o aluno estiver
     *  matriculado na turma.
     *
     * @link https://github.com/keviocastro/schools/issues/4
     * 
     * @return void
     */
    public function testStoreConditionClassStudent()
    {
        $this->createProgressSheet();

        $item = factory(App\StudentProgressSheet::class)->make([
            'student_id' => $this->student->id,
            'progress_sheet_item_id' => $this->itemsGroup1[0]->id,
            'school_calendar_phase_id' => $this->phases[1]->id,
            'school_class_id' => factory(App\SchoolClass::class)->create()->id,
        ])->toArray();

        // Náo é aluno
        $this->post('api/student-progress-sheets',
            $item,
            $this->getAutHeader())
            ->assertStatus(409)
            ->assertJsonFragment([
                'message' => "The student is not in the school class id ({$item['school_class_id']}).",
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
            ->assertStatus(200)
            ->assertJsonFragmentEquals($structure);
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
            ->assertStatus(200)
            ->assertJsonFragmentEquals($json);
    }

    /**
     * @covers App\Http\Controllers\StudentProgressSheetController::destroy
     *
     * @return void
     */
    public function testDestroy()
    {
        $studentProgressSheet = factory(StudentProgressSheet::class)->create();

        $this->delete("api/student-progress-sheets/{$studentProgressSheet->id}",
            [],
            $this->getAutHeader())
            ->assertStatus(204)
            ->seeIsSoftDeletedInDatabase('student_progress_sheets', ['id' => $studentProgressSheet->id]);
    }
}
