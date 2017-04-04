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
 * @author Kévio Castro keviocastro@gmail.com
 */
class SchoolCalendar2017 extends Seeder
{
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

    /**
     * Cria um calendario para 2017
     * Com uma turma
     * Aulas durante todo o ano para essa turma com 5 disciplinas, onde:
     *     O professor 1 ministra aulas para as disciplinas 1 e 2.
     *     As disciplinas 3,4,5 tem são ministradas pelos professores 2,3,4 respectivamente. 
     * Cria Alunos para a turma
     * Cria Responsaveis pelos alunos
     * Cria Registros de notas dos alunos durante o ano
     * Cria Registros de falta durante o ano
     *
     *
     *  Aulas: 240 aulas criadas para cada disciplina.
     *         Todos os dias, exceto sabado e domingo
     *  
     *  NOTAS:
     *      
     *       Para o primeiro aluno criado (id = 1), e
     *       1º disciplina criada (id = 1), com nome Matématica, tem as notas: 
     *       
     *       Nota 1.1    = 4.1 
     *       Nota 1.2    = 5.6 
     *       Nota 2.1    = 5.8
     *       Nota 2.2    = 4.1 
     *       Nota 3.1    = 6
     *       Nota 3.2    = 4.3
     *       Nota 4.1    = 5.2 
     *       Nota 4.2    = 3.1
     *       Recuperação = 7.4
     *       
     *       Média = (4.1+5.6)/2 = 4.9     // 1 Bimestre. 
     *               (5.8+4.1)/2 = 4.9     // 2 Bim.
     *               (6+4.3)/2   = 5.2     // 3 Bim.
     *               (5.2+3.1)/2 = 4.2     // 4 Bim.
     *                7.4                  // Recuperação
     *               
     *       A média anual é calculada por: 
     *       ( (1 Bim + 2 Bim) * 0.4 + (3 Bim + 4 Bim) * 0.6 ) / 2 ) = MÉDIA NO ANO
     *       
     *       1 Semestre = (4.9 + 4.9)*0.6  = 5.9 
     *       2 Semestre = (5.2 + 4.2)*0.4 = 3.8 
     *       Recuperação = 7.4
     *       
     *       (5.9+3.8)/2 = 4.9 MÉDIA NO ANO
     *       
     *  FALTAS:
     *  
     * Para o 1º aluno criado (id = 1):
     * 
     *    1º Bimestre = 3
     *    2º Bimestre = 0
     *    3º Bimestre = 0
     *    4º Bimestre = 0
     *    Total no ano: 3
     *
     *
     * @return array
     */
    public static function create()
    {
        // Calendario escolar com 5 fases: 4 bimestes e 1 recuperação
        // 2 notas por fase
        // 1 nota na recuperação
        dump('Criando calendário escolar e avaliações...');
        $schoolCalendar = factory(SchoolCalendar::class)->create([
            'year' => '2017',
            'start' => '2017-02-01',
            'end' => '2017-12-08',
            'average_formula' => 
                '( ({1º Bimestre} + {2º Bimestre})*0.6 + ({3º Bimestre} + {4º Bimestre})*0.4 )/2'
        ]);
        $schoolCalendarPhase1 = factory(SchoolCalendarPhase::class)->create([
            'school_calendar_id' => $schoolCalendar->id,
            'name' => '1º Bimestre',
            'start' => '2017-02-01',
            'end' => '2017-04-15',
            'average_formula' => '({Nota 1.1} + {Nota 1.2})/2'
        ]);

        $assessments[] = factory(App\Assessment::class)->make([
                'school_calendar_phase_id' => $schoolCalendarPhase1->id,
                'name' => 'Nota 1.1', 
            ])->toArray();
        $assessments[] = factory(App\Assessment::class)->make([
                'school_calendar_phase_id' => $schoolCalendarPhase1->id,
                'name' => 'Nota 1.2', 
            ])->toArray(); 
            
        Assessment::insert($assessments);

        $schoolCalendarPhase2 = factory(SchoolCalendarPhase::class)->create([
            'school_calendar_id' => $schoolCalendar->id,
            'name' => '2º Bimestre',
            'start' => '2017-04-16',
            'end' => '2017-06-31',
            'average_formula' => '({Nota 2.1} + {Nota 2.2})/2'
        ]);

        $assessments = [
            factory(Assessment::class)->make([
                'school_calendar_phase_id' => $schoolCalendarPhase2->id,
                'name' => 'Nota 2.1', 
            ])->toArray(),
            factory(Assessment::class)->make([
                'school_calendar_phase_id' => $schoolCalendarPhase2->id,
                'name' => 'Nota 2.2', 
            ])->toArray()
        ];
        Assessment::insert($assessments);

        $schoolCalendarPhase3 = factory(SchoolCalendarPhase::class)->create([
            'school_calendar_id' => $schoolCalendar->id,
            'name' => '3º Bimestre',
            'start' => '2017-08-01',
            'end' => '2017-09-30',
            'average_formula' => '({Nota 3.1} + {Nota 3.2})/2'
        ]);
        $assessments = [
            factory(Assessment::class)->make([
                'school_calendar_phase_id' => $schoolCalendarPhase3->id,
                'name' => 'Nota 3.1', 
            ])->toArray(),
            factory(Assessment::class)->make([
                'school_calendar_phase_id' => $schoolCalendarPhase3->id,
                'name' => 'Nota 3.2', 
            ])->toArray()
        ];
        Assessment::insert($assessments);

        $schoolCalendarPhase4 = factory(SchoolCalendarPhase::class)->create([
            'school_calendar_id' => $schoolCalendar->id,
            'name' => '4º Bimestre',
            'start' => '2017-10-01',
            'end' => '2017-12-15',
            'average_formula' => '({Nota 4.1} + {Nota 4.2})/2'
        ]);
        $assessments = [
            factory(App\Assessment::class)->make([
                'school_calendar_phase_id' => $schoolCalendarPhase4->id,
                'name' => 'Nota 4.1', 
            ])->toArray(),
            factory(App\Assessment::class)->make([
                'school_calendar_phase_id' => $schoolCalendarPhase4->id,
                'name' => 'Nota 4.2', 
            ])->toArray()
        ];
        Assessment::insert($assessments);

        $schoolCalendarPhase5 = factory(SchoolCalendarPhase::class)->create([
            'school_calendar_id' => $schoolCalendar->id,
            'name' => 'Recuperação',
            'start' => '2018    -01-08',
            'end' => '2018-01-31',
            'average_formula' => '{Nota 5.1}'
        ]);
        $assessments = [
            factory(App\Assessment::class)->make([
                'school_calendar_phase_id' => $schoolCalendarPhase5->id,
                'name' => 'Recuperação', 
            ])->toArray()
        ];
        Assessment::insert($assessments);

        // 1 Turma
        // 20 Alunos
        // 1 Responsável para cada aluno
        // 4 Registros de ocorrencia para cada aluno
        dump('Criando turmas e alunos...');
        $schoolClass = factory(SchoolClass::class)->create([
                'school_calendar_id' => $schoolCalendar->id
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

        $start = Carbon::createFromFormat('Y-m-d', $schoolCalendarPhase1->start);
        $end = Carbon::createFromFormat('Y-m-d', $schoolCalendarPhase4->end);

        // Cria aulas para 5 disciplinas
        dump('Criando aulas do calendário escolar...');
        $subjects = [];

        $subject = factory(Subject::class)->create([
                'name' => 'Matématica'
            ]);
        $teacher1 = factory(Teacher::class)->create([
                'person_id' => factory(Person::class)->create([
                            'user_id' => \Config::get('laravel-auth0.user_id_role_teacher_1')
                        ])->id
            ]);
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

        $teacher2 = factory(Teacher::class)->create([
                'person_id' => factory(Person::class)->create([
                            'user_id' => \Config::get('laravel-auth0.user_id_role_teacher_2')
                        ])->id
            ]);
        $subject = factory(Subject::class)->create();
        array_push($subjects, $subject);
        LessonsFactory::createBetweenTwoDates(
            $start, 
            $end, 
            9,
            $schoolClass,
            $subject,
            $teacher2);

        $teacher3 = factory(Teacher::class)->create([
                'person_id' => factory(Person::class)->create([
                            'user_id' => \Config::get('laravel-auth0.user_id_role_teacher_3')
                        ])->id
            ]);
        $subject = factory(Subject::class)->create();
        array_push($subjects, $subject);
        LessonsFactory::createBetweenTwoDates(
            $start, 
            $end, 
            10,
            $schoolClass,
            $subject,
            $teacher3);

        $teacher4 = factory(Teacher::class)->create([
                'person_id' => factory(Person::class)->create([
                            'user_id' => \Config::get('laravel-auth0.user_id_role_teacher_4')
                        ])->id
            ]);
        $subject = factory(Subject::class)->create();
        array_push($subjects, $subject);
        LessonsFactory::createBetweenTwoDates(
            $start, 
            $end, 
            11,
            $schoolClass,
            $subject,
            $teacher4);

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
        $assessments = $schoolCalendarPhase1->assessments()
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
        dump('Registrando presenças para o 1º Bimestre...');
        AttendanceRecordsFactory::create(
            $schoolCalendarPhase1, 
            3, 
            $studentFixedData->id
        );

        dump('Registrando notas para o 1º Bimestre...');
        StudentGradesFactory::create(
            $schoolCalendarPhase1, 
            $schoolClass, 
            $subjects, 
            $fixedDataSubjects
        );

        // 2º Bimestre
        $assessments = $schoolCalendarPhase2->assessments()
            ->orderBy('id')->get();
        // 1º Aluno 
        $fixedDataSubjects[0]['assessment_id'] = $assessments[0]->id;
        $fixedDataSubjects[0]['grade'] = 5.8;

        $fixedDataSubjects[1]['assessment_id'] = $assessments[1]->id;
        $fixedDataSubjects[1]['grade'] = 4.1;
        
        dump('Registrando presenças para o 2º Bimestre...');
        AttendanceRecordsFactory::create(
            $schoolCalendarPhase2, 
            0, 
            $studentFixedData->id
        );

        dump('Registrando notas para o 2º Bimestre...');
        StudentGradesFactory::create(
            $schoolCalendarPhase2, 
            $schoolClass, 
            $subjects, 
            $fixedDataSubjects
        );

        // 3º Bimestre
        $assessments = $schoolCalendarPhase3->assessments()
            ->orderBy('id')->get();
        // 1º Aluno
        $fixedDataSubjects[0]['assessment_id'] = $assessments[0]->id;
        $fixedDataSubjects[0]['grade'] = 6;

        $fixedDataSubjects[1]['assessment_id'] = $assessments[1]->id;
        $fixedDataSubjects[1]['grade'] = 4.3;
        
        dump('Registrando presenças para o 3º Bimestre...');
        AttendanceRecordsFactory::create(
            $schoolCalendarPhase3, 
            0, 
            $studentFixedData->id
        );
        dump('Registrando notas para o 3º Bimestre...');
        StudentGradesFactory::create(
            $schoolCalendarPhase3, 
            $schoolClass, 
            $subjects, 
            $fixedDataSubjects
        );

        // 4º Bimestre
        $assessments = $schoolCalendarPhase4->assessments()
            ->orderBy('id')->get();
        //1º Aluno
        $fixedDataSubjects[0]['assessment_id'] = $assessments[0]->id;
        $fixedDataSubjects[0]['grade'] = 5.2;

        $fixedDataSubjects[1]['assessment_id'] = $assessments[1]->id;
        $fixedDataSubjects[1]['grade'] = 3.1;

        dump('Registrando presenças para o 4º Bimestre...');
        AttendanceRecordsFactory::create(
            $schoolCalendarPhase4, 
            2, 
            $studentFixedData->id
        );
        dump('Registrando notas para o 4º Bimestre...');
        StudentGradesFactory::create(
            $schoolCalendarPhase4, 
            $schoolClass, 
            $subjects, 
            $fixedDataSubjects
        );

        // Recuperação
        $assessment = $schoolCalendarPhase5->assessments()
            ->first();

        //1º Aluno
        dump('Registrando presenças para a Recuperação...');
        AttendanceRecordsFactory::create(
            $schoolCalendarPhase5, 
            0, 
            $studentFixedData->id
        );

        dump('Registrando notas para a Recuperação...');
        $fixedDataSubjects[0]['assessment_id'] = $assessment->id;
        $fixedDataSubjects[0]['grade'] = 7.4;
        StudentGradesFactory::create(
            $schoolCalendarPhase5, 
            $schoolClass, 
            $subjects, 
            $fixedDataSubjects
        );
    }
}
