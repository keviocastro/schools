<?php

use Tests\TestCase;

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
        	->assertResponseStatus(200)
        	->seeJson($studentProgressSheet->toArray());
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
            ->assertResponseStatus(200);

        // Not existing group
        $this->get('api/student-progress-sheets'.
            "?student_id={$this->student->id}".
                "&school_class_id={$this->schoolClass->id}".
            '&_group_by=not_exist_group',
            $this->getAutHeader())
            ->assertResponseStatus(422);
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
     * @covers App\Http\Controllers\StudentProgressSheetController::store
     *
     * Teste de condição:
     *  O aluno pode ter somente um resposta de um item de avaliação, em uma fase do ano, para uma turma.
     * Isso significa que um requisição com a combinação de parametros progress_sheet_item_id, school_calendar_phase_id,
     * school_class_id e student_id, já existirem, um novo registro não será criado e sim atualizado.
     *
     * @see https://github.com/keviocastro/schools/issues/4
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
            ->assertResponseStatus(201)
            ->seeJsonEquals([
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
            ->assertResponseStatus(201)
            ->seeJsonEquals(['student_progress_sheet' => $item]);
    }
}
