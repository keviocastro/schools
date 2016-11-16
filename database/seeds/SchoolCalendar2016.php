<?php

use App\Assessment;
use App\SchoolClassSubject;
use App\Lesson;
use App\Occurence;
use App\SchoolCalendar;
use App\SchoolCalendarPhase;
use App\SchoolClass;
use App\SchoolClassStudent;
use App\Student;
use App\StudentGrade;
use App\StudentResponsible;
use App\Subject;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * @author Kévio Castro keviocastro@gmail.com
 */
class SchoolCalendar2016 extends Seeder
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
     * Cria um calendario para 2016
     * Com uma turma
     * Aulas durante todo o ano para essa turma
     * Alunos
     * Responsaveis pelos alunos
     * Registros de notas durante o ano
     * Registros de falta durante o ano
     * 
     * @return array
     */
    public static function create()
    {
        // Calendario escolar com 4 bimesres
        // 3 notas por bimestre para compor a nota do bimestre.
        $schoolCalendar = factory(SchoolCalendar::class)->create([
            'year' => '2016',
            'start' => '2016-01-20',
            'end' => '2016-12-16',
        ]);
        $schoolCalendarPhase1 = factory(SchoolCalendarPhase::class)->create([
            'school_calendar_id' => $schoolCalendar->id,
            'name' => '1º Bimestre',
            'start' => '2016-01-16',
            'end' => '2016-04-15'
        ]);
        $assessments = [
            [
                'school_calendar_phase_id' => $schoolCalendarPhase1->id,
                'name' => 'Nota 1.1', 
            ],
            [
                'school_calendar_phase_id' => $schoolCalendarPhase1->id,
                'name' => 'Nota 1.2', 
            ]
        ];
        Assessment::insert($assessments);

        $schoolCalendarPhase2 = factory(SchoolCalendarPhase::class)->create([
            'school_calendar_id' => $schoolCalendar->id,
            'name' => '2º Bimestre',
            'start' => '2016-04-16',
            'end' => '2016-06-30'
        ]);
        $assessments = [
            [
                'school_calendar_phase_id' => $schoolCalendarPhase2->id,
                'name' => 'Nota 2.1', 
            ],
            [
                'school_calendar_phase_id' => $schoolCalendarPhase2->id,
                'name' => 'Nota 2.2', 
            ]
        ];
        Assessment::insert($assessments);

        $schoolCalendarPhase3 = factory(SchoolCalendarPhase::class)->create([
            'school_calendar_id' => $schoolCalendar->id,
            'name' => '3º Bimestre',
            'start' => '2016-08-01',
            'end' => '2016-09-30'
        ]);
        $assessments = [
            [
                'school_calendar_phase_id' => $schoolCalendarPhase3->id,
                'name' => 'Nota 3.1', 
            ],
            [
                'school_calendar_phase_id' => $schoolCalendarPhase3->id,
                'name' => 'Nota 3.2', 
            ]
        ];
        Assessment::insert($assessments);

        $schoolCalendarPhase4 = factory(SchoolCalendarPhase::class)->create([
            'school_calendar_id' => $schoolCalendar->id,
            'name' => '4º Bimestre',
            'start' => '2016-10-01',
            'end' => '2016-12-16'
        ]);
        $assessments = [
            [
                'school_calendar_phase_id' => $schoolCalendarPhase4->id,
                'name' => 'Nota 4.1', 
            ],
            [
                'school_calendar_phase_id' => $schoolCalendarPhase4->id,
                'name' => 'Nota 4.2', 
            ]
        ];
        Assessment::insert($assessments);

        // 1 Turma
        // 20 Alunos
        // 1 Responsável para cada aluno
        // 4 Registros de ocorrencia para cada aluno
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
        $subjects = [];

        $subject = factory(Subject::class)->create();
        $subjectFixedData = $subject;
        array_push($subjects, $subject);
        self::createLessonsBetweenTwoDates(
            $start, 
            $end, 
            7,
            $schoolClass,
            $subject);


        $subject = factory(Subject::class)->create();
        $subjectFixedData2 = $subject;
        array_push($subjects, $subject);
        self::createLessonsBetweenTwoDates(
            $start, 
            $end, 
            8,
            $schoolClass,
            $subject);

        $subject = factory(Subject::class)->create();
        array_push($subjects, $subject);
        self::createLessonsBetweenTwoDates(
            $start, 
            $end, 
            9,
            $schoolClass,
            $subject);

        $subject = factory(Subject::class)->create();
        array_push($subjects, $subject);
        self::createLessonsBetweenTwoDates(
            $start, 
            $end, 
            10,
            $schoolClass,
            $subject);

        $subject = factory(Subject::class)->create();
        array_push($subjects, $subject);
        self::createLessonsBetweenTwoDates(
            $start, 
            $end, 
            11,
            $schoolClass,
            $subject);

        // Definindo disciplinas da turma
        $schoolClassSubjects = [];
        foreach ($subjects as $key => $subject) {
            array_push($schoolClassSubjects, [
                    'school_class_id' => $schoolClass->id,
                    'subject_id' => $subject->id
                ]); 
        }

        SchoolClassSubject::insert($schoolClassSubjects);


        // Marcar 4 faltas
        // Registra nota para todos os alunos em todas as disciplinas
        // 1º disciplina criada é a maior média do ano = 10
        // 2º disciplina criada é a menor média do ano = 0.2
        $fixedDataSubjects = [
                [
                    'subject_id' =>  $subjectFixedData->id,
                    'grade' => 10,
                    'student_id' => 1,
                ],
                [
                    'subject_id' => $subjectFixedData2->id,
                    'grade' => 0.2,
                    'student_id' => 1,
                ]
            ];
        // 1 Bimeste: 4 faltas pro aluno $studentFixedData (1 aluno criado)
        self::createAttendanceRecords($schoolCalendarPhase1, 4, $studentFixedData->id);
        self::createStudentGrades($schoolCalendarPhase1, 
            $schoolClass, $subjects, $fixedDataSubjects);

        // 2 Bimestre: 3 faltas pro aluno $studentFixedData
        self::createAttendanceRecords($schoolCalendarPhase2, 3, $studentFixedData->id);
        $assessments_phase_2 = $schoolCalendarPhase2->assessments;
        self::createStudentGrades($schoolCalendarPhase2, $schoolClass, 
            $subjects, $fixedDataSubjects);

        // 3 Bimestre: 6 faltas
        self::createAttendanceRecords($schoolCalendarPhase3, 6, $studentFixedData->id);
        self::createStudentGrades($schoolCalendarPhase3, $schoolClass, 
            $subjects, $fixedDataSubjects);

        // 4 Bimestre: 2 faltas
        self::createAttendanceRecords($schoolCalendarPhase4, 2, $studentFixedData->id);
        $assessments_phase_4 = $schoolCalendarPhase4->assessments;
        self::createStudentGrades($schoolCalendarPhase4, $schoolClass, 
            $subjects, $fixedDataSubjects);

    }

    /**
     * Cria 1 aula por dia entre um intervalo de datas.
     * 
     * @param  Carbon $firstDay        
     * @param  Carbon $lastDay         
     * @param  int $lessonStartTime 
     * @param  App\SchoolClass $schoolClass     
     * @param  App\Subject $subject    
     *      
     * @return void                  
     */
    public static function createLessonsBetweenTwoDates(
        Carbon $firstDay, 
        Carbon $lastDay, 
        int $lessonStartTime,
        \App\SchoolClass $schoolClass,
        \App\Subject $subject
        ){

        $startLesson = clone $firstDay;
        $startLesson->hour = $lessonStartTime;
        $endLesson = clone $startLesson;
        $endLesson->addMinutes(45);

        $lessons = [];
        while ( $startLesson->lt($lastDay) ) {
        	if ($startLesson->dayOfWeek !=  Carbon::SUNDAY && 
        		$startLesson->dayOfWeek != Carbon::SATURDAY) {
        		
                array_push($lessons, [
	        			'school_class_id' => $schoolClass->id,
	        			'start' => $startLesson->format('Y-m-d'),
	        			'end' => $endLesson->format('Y-m-d'),
                        'subject_id' => $subject->id,
                    ]);
            
            }

            $startLesson->addDays(1);
            $endLesson->addDays(1);
        }

        \App\Lesson::insert($lessons);
    }

    /**
     * Cria registro de presença para todas as aulas 
     * da fase do ano letivo $schoolCalendarPhase
     * 
     * @param  SchoolCalendarPhase $schoolCalendarPhase  
     * @param  int                 $totalAbsences        Opcional
     * @param  int                 $student_id           Opcional. 
     *                                                   Atribui total de faltas ($totalAbsences) 
     *                                                   para esse aluno.        
     * @return void                                   
     */
    public static function createAttendanceRecords(
        SchoolCalendarPhase $schoolCalendarPhase,
        int $totalAbsences,
        int $student_id=null
        ){

        $lessons = $schoolCalendarPhase->lessons;
        $attendanceRecords = [];
        $total_absences_student_id = 0;

        foreach ($lessons as $lesson) {

            foreach ($lesson->students() as $key => $student) {
                
                if ($student_id == $student->id &&
                        $total_absences_student_id < $totalAbsences && 
                        $lesson->subject_id == 1 
                    ){
                    
                    $presence = 0;
                    $total_absences_student_id++;
                }else{
                    $presence = ($key <= $totalAbsences-1) ? 0 : 1;
                }

                array_push($attendanceRecords, [
                        'lesson_id' => $lesson->id,
                        'student_id' => $student->id,
                        'presence' => $presence
                    ]);
            }

        }

        App\AttendanceRecord::insert($attendanceRecords);
    }

    /**
     * Cria nota para todos os alunos da turma
     * na fase do ano letivo e disciplinas
     * 
     * @param  SchoolCalendarPhase $schoolCalendarPhase 
     * @param  SchoolClass         $schoolClass         
     * @param  array               $subjects            
     * @param  array               $subjects         
     * @param  array               $fixedData   Definir valores que não serão aleatório
     *                                          Exemplo:
     *                             [
     *                                 [
     *                                     'student_id' => 1,
     *                                     'subject_id' => 1,
     *                                     'assessment_id' => 1,
     *                                     'grade' => 10
     *                                 ],
     *                                 [
     *                                     'student_id' => 1,
     *                                     'subject_id' => 1,
     *                                     'assessment_id' => 2,
     *                                     'grade' => 9.2
     *                                 ]
     *                              ]
     * 
     * @return void                                   
     */
    public static function createStudentGrades(
        SchoolCalendarPhase $schoolCalendarPhase,
        SchoolClass $schoolClass,
        array $subjects,
        array $fixedDataSubjects=[]
        ){

        $studentGrades = [];
        $faker = \Faker\Factory::create();

        $schoolClass->students->each(function($student, $key) 
            use ($schoolCalendarPhase, $subjects, &$studentGrades, 
                $fixedDataSubjects, $faker){
            
                foreach ($schoolCalendarPhase->assessments as $key => $assessment) {
                    foreach ($subjects as $key => $subject) {


                        $grade = false;
                        if (!empty($fixedDataSubjects)) {
                            
                            foreach ($fixedDataSubjects as $data) {
                                
                                if ($student->id == $data['student_id'] && 
                                    $subject->id == $data['subject_id']
                                    ) {
                                    
                                    $grade = $data['grade'];
                                }
                            }
                        }

                        $grade = $grade ? $grade : $faker->randomFloat(1,0,10);

                        $studentGrade = factory(App\StudentGrade::class)->make([
                                'assessment_id' => $assessment->id,
                                'student_id' => $student->id,
                                'subject_id' => $subject->id,
                                'grade' => $grade
                            ])->toArray();

                        array_push($studentGrades, $studentGrade);
                    }
                }
        });

        StudentGrade::insert($studentGrades);
    }
}
