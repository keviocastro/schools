<?php

use App\Assessment;
use App\Lesson;
use App\Occurence;
use App\SchoolCalendar;
use App\SchoolCalendarPhase;
use App\SchoolClass;
use App\SchoolClassStudent;
use App\SchoolClassSubject;
use App\Student;
use App\StudentGrade;
use App\StudentResponsible;
use App\Subject;
use App\Teacher;
use App\Person;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * @todo Refactore A pior classe que já escrevi na vida... :)
 */
class SchoolCalendarSeeder extends Seeder
{
    public $schoolCalendar = null;
    public $schoolCalendarPhase1 = null;
    public $schoolCalendarPhase2 = null;
    public $schoolCalendarPhase3 = null;
    public $schoolCalendarPhase4 = null;

    /**
     * Run the database seeds.
     * 
     *
     * @return void
     */
    public function run()
    {   
        self::create();
    }

    public function create()
    {
        $this->createSchoolCalendar();
        $this->createClassesWithGrade();
        $this->createClassesWithProgressSheet();
    }

    public function createSchoolCalendar()
    {
        $now = new Carbon();
        $year = $now->year;

        $startPhase1 = Carbon::createFromDate($year, 1, 1);
        $startPhase1->addDays(10);
        $endPhase1 = clone $startPhase1;
        $endPhase1->addMonths(2);

        $startPhase2 = clone $endPhase1;
        $startPhase2->addDay();
        $endPhase2 = clone $startPhase2;
        $endPhase2->addMonths(2);

        $startPhase3 = clone $endPhase2;
        $startPhase3->addDay();
        $endPhase3 = clone $startPhase3;
        $endPhase3->addMonths(2);

        $startPhase4 = clone $endPhase3;
        $startPhase4->addDay();
        $endPhase4 = clone $startPhase4;
        $endPhase4->addMonths(2);

         // Calendario escolar com 5 fases: 4 bimestes e 1 recuperação
        // 2 notas por fase
        // 1 nota na recuperação
        $schoolCalendar = factory(SchoolCalendar::class)->create([
            'year' => $year,
            'start' => $startPhase1->format('Y-m-d'),
            'end' => $endPhase4->format('Y-m-d'),
            'average_formula' => 
                    '(({1º Bimestre} + {2º Bimestre} + {3º Bimestre} + {4º Bimestre})/4)'
        ]);
        $this->schoolCalendarPhase1 = factory(SchoolCalendarPhase::class)->create([
            'school_calendar_id' => $schoolCalendar->id,
            'name' => '1º Bimestre',
            'start' => $startPhase1->format('Y-m-d'),
            'end' => $endPhase1->format('Y-m-d'),
            'average_formula' => '({Nota 1.1} + {Nota 1.2})/2'
        ]);

        $assessments[] = factory(App\Assessment::class)->make([
                'school_calendar_phase_id' => $this->schoolCalendarPhase1->id,
                'name' => 'Nota 1.1', 
            ])->toArray();
        $assessments[] = factory(App\Assessment::class)->make([
                'school_calendar_phase_id' => $this->schoolCalendarPhase1->id,
                'name' => 'Nota 1.2', 
            ])->toArray(); 
            
        Assessment::insert($assessments);

        $this->schoolCalendarPhase2 = factory(SchoolCalendarPhase::class)->create([
            'school_calendar_id' => $schoolCalendar->id,
            'name' => '2º Bimestre',
            'start' => $startPhase2->format('Y-m-d'),
            'end' => $endPhase2->format('Y-m-d'),
            'average_formula' => '({Nota 2.1} + {Nota 2.2})/2'
        ]);

        $assessments = [
            factory(Assessment::class)->make([
                'school_calendar_phase_id' => $this->schoolCalendarPhase2->id,
                'name' => 'Nota 2.1', 
            ])->toArray(),
            factory(Assessment::class)->make([
                'school_calendar_phase_id' => $this->schoolCalendarPhase2->id,
                'name' => 'Nota 2.2', 
            ])->toArray()
        ];
        Assessment::insert($assessments);

        $this->schoolCalendarPhase3 = factory(SchoolCalendarPhase::class)->create([
            'school_calendar_id' => $schoolCalendar->id,
            'name' => '3º Bimestre',
            'start' => $startPhase3->format('Y-m-d'),
            'end' => $endPhase3->format('Y-m-d'),
            'average_formula' => '({Nota 3.1} + {Nota 3.2})/2'
        ]);
        $assessments = [
            factory(Assessment::class)->make([
                'school_calendar_phase_id' => $this->schoolCalendarPhase3->id,
                'name' => 'Nota 3.1', 
            ])->toArray(),
            factory(Assessment::class)->make([
                'school_calendar_phase_id' => $this->schoolCalendarPhase3->id,
                'name' => 'Nota 3.2', 
            ])->toArray()
        ];
        Assessment::insert($assessments);

        $this->schoolCalendarPhase4 = factory(SchoolCalendarPhase::class)->create([
            'school_calendar_id' => $schoolCalendar->id,
            'name' => '4º Bimestre',
            'start' => $startPhase4->format('Y-m-d'),
            'end' => $startPhase4->format('Y-m-d'),
            'average_formula' => '({Nota 4.1} + {Nota 4.2})/2'
        ]);
        $assessments = [
            factory(App\Assessment::class)->make([
                'school_calendar_phase_id' => $this->schoolCalendarPhase4->id,
                'name' => 'Nota 4.1', 
            ])->toArray(),
            factory(App\Assessment::class)->make([
                'school_calendar_phase_id' => $this->schoolCalendarPhase4->id,
                'name' => 'Nota 4.2', 
            ])->toArray()
        ];
        Assessment::insert($assessments);
        
        $this->schoolCalendar = $schoolCalendar;
    }

    public function createClassesWithGrade()
    {
        
        dump('Criando turma com avaliação por nota e por diciplina...');
        $schoolClass = factory(SchoolClass::class)->create([
                'school_calendar_id' => $this->schoolCalendar->id,
                'evaluation_type' =>  \App\EvaluationTypeRepository::GRADE_PHASE
            ]);
        $students = factory(Student::class, 20)->create()
            ->each(function($student) use ($schoolClass){
                factory(StudentResponsible::class)->create([
                        'student_id' => $student->id 
                    ]);
                factory(SchoolClassStudent::class)->create([
                        'student_id' => $student->id,
                        'school_class_id' => $schoolClass->id
                    ]);
                if (rand(0,1)) {
                    factory(Occurence::class, 4)->create([
                            'about_person_id' => $student->id
                        ]);
                }
            });

        // Estudante que terão dados de faltas e notas
        // pré-definidos para ser utilizados em testes 
        $studentFixedData = $students[0];

        $start = Carbon::createFromFormat('Y-m-d', $this->schoolCalendarPhase1->start);
        $end = Carbon::createFromFormat('Y-m-d', $this->schoolCalendarPhase4->end);

        // Cria aulas para 5 disciplinas
        // dump('Criando aulas do calendário escolar...');
        $subjects = [];

        $subject = factory(Subject::class)->create();
        $teacher1 = LessonsFactory::createTeacherIfNotExists(Config::get('laravel-auth0.user_id_role_teacher_1'));
        $subjectFixedData = $subject;
        array_push($subjects, $subject);
        LessonsFactory::createBetweenTwoDates(
            $start,
            $end,
            7,
            $schoolClass,
            $subject,
            $teacher1);

        $subject = factory(Subject::class)->create();
        $subjectFixedData2 = $subject;
        array_push($subjects, $subject);
        LessonsFactory::createBetweenTwoDates(
            $start, 
            $end, 
            8,
            $schoolClass,
            $subject,
            $teacher1);

        $subject = factory(Subject::class)->create();
        array_push($subjects, $subject);
        LessonsFactory::createBetweenTwoDates(
            $start, 
            $end, 
            7,
            $schoolClass,
            $subject,
            $teacher1);

        $subject = factory(Subject::class)->create();
        array_push($subjects, $subject);
        LessonsFactory::createBetweenTwoDates(
            $start, 
            $end, 
            10,
            $schoolClass,
            $subject,
            $teacher1);

        $subject = factory(Subject::class)->create();
        array_push($subjects, $subject);
        LessonsFactory::createBetweenTwoDates(
            $start, 
            $end, 
            11,
            $schoolClass,
            $subject,
            $teacher1);

        // Definindo disciplinas da turma
        $schoolClassSubjects = [];
        foreach ($subjects as $key => $subject) {
            array_push($schoolClassSubjects, [
                    'school_class_id' => $schoolClass->id,
                    'subject_id' => $subject->id
                ]); 
        }

        SchoolClassSubject::insert($schoolClassSubjects);

        // 1º Bimeste
        $assessments = $this->schoolCalendarPhase1->assessments()
            ->orderBy('id')->get();
        
        $fixedDataSubjects = [
                // 1º Aluno
                [
                    'subject_id' =>  $subjectFixedData->id,
                    'grade' => 4.1,
                    'student_id' => $studentFixedData->id,
                    'assessment_id' => $assessments[0]->id
                ],
                [
                    'subject_id' => $subjectFixedData->id,
                    'grade' => 5.6,
                    'student_id' => $studentFixedData->id,
                    'assessment_id' => $assessments[1]->id
                ]
            ];

        dump('Criando turma com avaliação por nota e por diciplina: presenças do 1º bimeste.');
        AttendanceRecordsFactory::create(
            $this->schoolCalendarPhase1, 
            3, 
            $studentFixedData->id,
            $schoolClass->id
        );

        dump('Criando turma com avaliação por nota e por diciplina: notas do 1º bimeste.');
        StudentGradesFactory::create(
            $this->schoolCalendarPhase1, 
            $schoolClass, 
            $subjects, 
            $fixedDataSubjects
        );

        // 2º Bimestre
        $assessments = $this->schoolCalendarPhase2->assessments()
            ->orderBy('id')->get();
        // 1º Aluno 
        $fixedDataSubjects[0]['assessment_id'] = $assessments[0]->id;
        $fixedDataSubjects[0]['grade'] = 5.8;

        $fixedDataSubjects[1]['assessment_id'] = $assessments[1]->id;
        $fixedDataSubjects[1]['grade'] = 4.1;
        
        dump('Criando turma com avaliação por nota e por diciplina: presenças do 2º bimeste.');
        AttendanceRecordsFactory::create(
            $this->schoolCalendarPhase2, 
            0, 
            $studentFixedData->id,
            $schoolClass->id
        );

        dump('Criando turma com avaliação por nota e por diciplina: notas do 2º bimeste.');
        StudentGradesFactory::create(
            $this->schoolCalendarPhase2, 
            $schoolClass, 
            $subjects, 
            $fixedDataSubjects
        );

        // 3º Bimestre
        $assessments = $this->schoolCalendarPhase3->assessments()
            ->orderBy('id')->get();
        // 1º Aluno
        $fixedDataSubjects[0]['assessment_id'] = $assessments[0]->id;
        $fixedDataSubjects[0]['grade'] = 6;

        $fixedDataSubjects[1]['assessment_id'] = $assessments[1]->id;
        $fixedDataSubjects[1]['grade'] = 4.3;
        
        dump('Criando turma com avaliação por nota e por diciplina: presenças do 3º bimeste.');
        AttendanceRecordsFactory::create(
            $this->schoolCalendarPhase3, 
            0, 
            $studentFixedData->id,
            $schoolClass->id
        );
        dump('Criando turma com avaliação por nota e por diciplina: notas do 3º bimeste.');
        StudentGradesFactory::create(
            $this->schoolCalendarPhase3, 
            $schoolClass, 
            $subjects, 
            $fixedDataSubjects
        );

        // 4º Bimestre
        $assessments = $this->schoolCalendarPhase4->assessments()
            ->orderBy('id')->get();
        //1º Aluno
        $fixedDataSubjects[0]['assessment_id'] = $assessments[0]->id;
        $fixedDataSubjects[0]['grade'] = 5.2;

        $fixedDataSubjects[1]['assessment_id'] = $assessments[1]->id;
        $fixedDataSubjects[1]['grade'] = 3.1;

        dump('Criando turma com avaliação por nota e por diciplina: presenças do 4º bimeste.');
        AttendanceRecordsFactory::create(
            $this->schoolCalendarPhase4, 
            2, 
            $studentFixedData->id,
            $schoolClass->id
        );

        dump('Criando turma com avaliação por nota e por diciplina: notas do 4º bimeste.');
        StudentGradesFactory::create(
            $this->schoolCalendarPhase4, 
            $schoolClass, 
            $subjects, 
            $fixedDataSubjects
        );
    }

    public function createClassesWithProgressSheet()
    {
        dump('Criando turma com avaliação por ficha descritiva');
        $progressSheet = factory(App\ProgressSheet::class)->create();
        $schoolClass = factory(SchoolClass::class)->create([
                'grade_id' => factory(App\Grade::class)->create(['name' => '1º Ano Infantíl'])->id,
                'school_calendar_id' => $this->schoolCalendar->id,
                'evaluation_type' =>  \App\EvaluationTypeRepository::PROGRESS_SHEET_PHASE,
                'progress_sheet_id' => function() use ($progressSheet){
                    
                    $group1 = factory(App\Group::class)->create();
                    factory(App\ProgressSheetItem::class, 10)->create([
                        'progress_sheet_id' => $progressSheet->id,
                        'group_id' =>  $group1->id
                    ]);
                    $group2 = factory(App\Group::class)->create();
                    factory(App\ProgressSheetItem::class, 7)->create([
                        'progress_sheet_id' => $progressSheet->id,
                        'group_id' =>  $group2->id
                    ]);
                    $group3 = factory(App\Group::class)->create();
                    factory(App\ProgressSheetItem::class, 4)->create([
                        'progress_sheet_id' => $progressSheet->id,
                        'group_id' =>  $group3->id
                    ]);
                    return $progressSheet->id;
                }
            ]);

        

        dump('Criando turma com avaliação por ficha descritiva: estudantes');
        $students = factory(Student::class, 15)->create()
            ->each(function($student) use ($schoolClass){
                factory(StudentResponsible::class)->create([
                        'student_id' => $student->id 
                    ]);
                factory(SchoolClassStudent::class)->create([
                        'student_id' => $student->id,
                        'school_class_id' => $schoolClass->id
                    ]);
                if (rand(0,1)) {
                    factory(Occurence::class, 4)->create([
                            'about_person_id' => $student->id
                        ]);
                }
            });


        $studentFixedData = $students[0];

        dump('Criando turma com avaliação por ficha descritiva: aulas');
        $start = Carbon::createFromFormat('Y-m-d', $this->schoolCalendarPhase1->start);
        $end = Carbon::createFromFormat('Y-m-d', $this->schoolCalendarPhase4->end);

        $teacher1 = LessonsFactory::createTeacherIfNotExists(Config::get('laravel-auth0.user_id_role_teacher_1'));


        LessonsFactory::createBetweenTwoDates(
            $start,
            $end,
            7,
            $schoolClass,
            false,
            $teacher1,
            240);

        dump('Criando turma com avaliação por ficha descritiva: avaliações dos alunos');
        $studentsProgressSheets = [];
        foreach ( $progressSheet->items as $item) {

            foreach ($schoolClass->students as $student) {

                foreach ($this->schoolCalendar->phases as $phase) {

                    $option_identifier = $progressSheet
                        ->options[rand(0,count($progressSheet->options)-1)]
                        ['identifier'];

                    array_push($studentsProgressSheets,
                        factory(App\StudentProgressSheet::class)->make([
                            'option_identifier' => $option_identifier,
                            'progress_sheet_item_id' => $item->id,
                            'student_id' => $student->id,
                            'school_calendar_phase_id' => $phase->id,
                            'school_class_id' => $schoolClass->id
                        ])->toArray()
                    );
                }
            }
        }

        App\StudentProgressSheet::insert($studentsProgressSheets); 

        dump('Criando turma com avaliação por ficha descritiva: presenças e faltas');
        AttendanceRecordsFactory::create(
            $this->schoolCalendarPhase1, 
            3,
            $studentFixedData->id,
            $schoolClass->id
        );

        AttendanceRecordsFactory::create(
            $this->schoolCalendarPhase2, 
            2,
            $studentFixedData->id,
            $schoolClass->id
        );

        AttendanceRecordsFactory::create(
            $this->schoolCalendarPhase3, 
            1,
            $studentFixedData->id,
            $schoolClass->id
        );

        AttendanceRecordsFactory::create(
            $this->schoolCalendarPhase4, 
            4,
            $studentFixedData->id,
            $schoolClass->id
        );
    }
}
