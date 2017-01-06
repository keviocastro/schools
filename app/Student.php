<?php

namespace App;

use App\SchoolCalendar;
use App\Subject;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Stringy\Stringy as S;

/**
 * Essa classe representa um aluno
 * 
 * @author Kévio Castro <keviocastro@gmail.com>
 */
class Student extends Model
{
     use SoftDeletes;

    /**
     * Atributos que serão convertidos para tipo de data.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    
    /**
     * 
     * Atributos que serão ocultos em arrays
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at', 
        'created_at',
        'updated_at',
        'created_by',
        'deleted_by',
        'updated_by'
    ];

    /**
     * Atributos que podem ser atribuidos a entidade 
     *
     * @var array
     */
    protected $fillable = ['person_id'];

    /**
     * Registro de pessoa associado com o estudante
     * 
     * @Relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author Kévio Castro <keviocastro@gmail.com>
     */
    public function person()
    {
        return $this->belongsTo('App\Person');
    }

    /**
     * Responsáveis pelo estudante
     *
     * @Relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * 
     * @author Kévio Castro <keviocastro@gmail.com>
     */
    public function responsibles()
    {
        return $this->hasMany('App\StudentResponsible');
    }

    /**
     * Todos os registros de presenças do aluno
     *
     * @Relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @author Kévio Castro <keviocastro@gmail.com>
     */
    public function attendanceRecords()
    {
        return $this->hasMany('App\AttendanceRecord');
    }

    /**
     * Todas as notas do aluno
     *
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @author Kévio Castro <keviocastro@gmail.com>
     */
    public function studentGrades()
    {
        return $this->hasMany('App\StudentGrade');
    }

    /**
     * Todas as notas do aluno contendo informações da avaliação
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @author Kévio Castro <keviocastro@gmail.com>
     */
    public function studentGradesWithAssessment()
    {
        return $this->studentGrades()
            ->join('assessments',
                'assessments.id',
                '=',
                'student_grades.assessment_id');
    }

    /**
     * Todas as ocorrências do aluno
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @author Kévio Castro <keviocastro@gmail.com>
     */
    public function occurences()
    {
        return $this->hasMany('App\Occurence', 'about_person_id');
    }

    /**
     * Todas as ocorrências do aluno em um ano letivo
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * 
     * @author Kévio Castro <keviocastro@gmail.com>
     */
    public function occurencesYear(SchoolCalendar $schoolCalendar)
    {
        return $this->occurences()
            ->where(DB::raw("DATE_FORMAT(occurences.created_at, '%Y-%m-%d')"),
                '>=', 
                $schoolCalendar->start)
            ->where(DB::raw("DATE_FORMAT(occurences.created_at, '%Y-%m-%d')"),
                '<=',
                $schoolCalendar->end
                );
    }

    /**
     * Média anual das disciplinas cursadas pelo aluno em 
     * um ano letivo. 
     * Utiliza sempre a precisão de 1 casa decimal.
     * 
     * @param  string $schoolCalendar 
     * @param  string $toArray
     * @param  Subject $filterBySubject         
     *  
     * @return \Illuminate\Database\Eloquent\Collection 
     */
    public function subjectAvaragePerYear(SchoolCalendar $schoolCalendar, 
            $toArray=false, 
            Subject $filterBySubject=null
        ){

        $formula = $schoolCalendar->average_formula;
        $formula_variables = [];
        
        // Extrair as variáveis da formula
        while (  $phase_name = (string) S::create($formula)->between('{', '}') ) 
        {
            $formula_variables[] = $phase_name;
            $formula = str_replace('{'.$phase_name.'}', '', $formula);
        }

        $averagesPerPhase = $this->subjectAvaragePerYearPhase($schoolCalendar);
        $querySubjects = $this->subjectsYear($schoolCalendar->id);
        if ($filterBySubject) {
            $querySubjects->where('subjects.id', $filterBySubject->id);
        }

        $subjects = $querySubjects->get();

        $foundVariables = true;
        foreach ($subjects as $key => $subject) {
            $calculation = $schoolCalendar->average_formula;
            $subject->school_calendar_phases = collect();

            foreach ($formula_variables as $key => $variable) {
                
                // Encontra a fase com mesmo nome da variável da formula
                // Encontra a média da disciplina na fase
                $phase = $averagesPerPhase
                    ->where('name', $variable)
                    ->first();

                if (empty($phase)) {
                    // A variável não corresponde ao nome de nenhuma fase
                    // do ano letivo
                    $calculation = str_replace(
                        '{'.$variable.'}', 
                        '{'.$variable.':notFound}', 
                        $calculation);    
                        $foundVariables = false;

                    continue; 
                }
                
                // Substitui a nota da disciplina na fase pela
                // variável da formula $schoolCalendar->average_formula
                if ($phase->has('subject_average') ) {
                    $subject_grade = $phase['subject_average']
                        ->where('id', $subject->id)
                        ->first()
                        ->toArray();

                    if ($subject_grade && is_numeric($subject_grade['average']) ) {
                            $calculation = str_replace(
                                '{'.$variable.'}', 
                                round($subject_grade['average'], 1), 
                                $calculation);
                            
                    }else{
                        // A diciplina não tem média para a fase do ano letivo
                        $calculation = str_replace(
                        '{'.$variable.'}', 
                        '{'.$variable.':doesNotExist:AverageForYearPhase}', 
                        $calculation);  
                        $foundVariables = false; 
                    }

                    $subject->school_calendar_phases->push([
                            'id' => $phase['id'],
                            'name' => $phase['name'],
                            'average' => $subject_grade['average'],
                            'average_formula' => $subject_grade['average_formula'],
                            'average_calculation' => $subject_grade['average_calculation'],
                            'student_grades' => $subject_grade['student_grades'] 
                        ]);

                }else{
                    // A variável não corresponde ao nome de nenhuma fase
                    // do ano letivo
                    $calculation = str_replace(
                        '{'.$variable.'}', 
                        '{'.$variable.':notFound}', 
                        $calculation);    
                        $foundVariables = false;          
                }
                
            }

            $subject->average_calculation = $calculation;
            $subject->average_formula = $schoolCalendar->average_formula;

            if ($foundVariables) {
                eval("\$result = $calculation;");
                $subject->average_year = round($result, 1);
            }else{
                $subject->average_year = 'incomplete-calculation';
            }

            if ($toArray) {
                $subject->school_calendar_phases->transform(function($item, $key){
                    $item['student_grades'] = $item['student_grades']->toArray();
                    return $item; 
                });
                $subject->school_calendar_phases = $subject->school_calendar_phases->toArray();
                
            }
        }

        if ($toArray) {
            $subjects = $subjects->toArray();
        }

        return  $subjects;
    }

    /**
     * @todo Remover parametro toArray e fazer override para o 
     *       Eloquent\Collection fazer toArray recursivamente
     *
     * 
     * Médias de todas as disciplinas cursadas em um ano (SchoolCalendar).
     * As médias retornadas não agrupadas por fase do ano (SchoolCalendarPhase).
     * As médias são calculadas a partir da formula definida na fase do ano (SchoolCalendarPhase). 
     * 
     * @param  SchoolCalendar   $schoolCalendar 
     * @param  bool             $toArray          Para definir o retorno no 
     *                                            formato de array.
     * @param  Subject          $filterBySubject                                          
     * 
     * @return mixed \Illuminate\Database\Eloquent\Collection | array                         
     * 
     * @author Kévio Castro <keviocastro@gmail.com>
     */
    public function subjectAvaragePerYearPhase(
        SchoolCalendar $schoolCalendar,
        $toArray=false,
        Subject $filterBySubject=null)
    {
        // Contém fases (schoolCalendarPhase), 
        // avaliações (assessments) da fase 
        // e notas do aluno (studentGrades)
        // no ano letivo (schoolCalendar)
        $phases = $schoolCalendar->phases()
            ->with(['assessments.studentGrades' => function($query){
                $query->where('student_id', $this->id);
            }])->get();


        $phases->each(function($phase, $key) 
            use ($schoolCalendar, $toArray, $filterBySubject) {

            $querySubjects = $this->subjectsYear($schoolCalendar->id);
            if ($filterBySubject) {
                $querySubjects
                    ->where('subject.id', $filterBySubject->id);
            }

            $phase->subject_average = $querySubjects->get();

            $formula = $phase->average_formula;
            $formula_variables = [];
            
            // Extrair as variáveis da formula
            while (  $assessment_name = (string) S::create($formula)->between('{', '}') ) 
            {
                $formula_variables[] = $assessment_name;
                $formula = str_replace('{'.$assessment_name.'}', '', $formula);
            }

            // Substituir as variáveis da formula por nota 
            // de cada disciplina
            $formula = $phase->average_formula;
            $phase->subject_average->each(function($subject, $key) 
                use ($phase, $formula_variables, $formula, $toArray){


                // Obtem a nota da disciplina referente
                // a avaliação.
                $assessment_and_grade = collect();
                $calculation = $formula; 
                $result = null;
                $all_variables_found = true;
                $all_grades_found = true;
                foreach ($formula_variables as $key => $variable) {
                    
                    // Encontra a avaliação (assessment) correspondente
                    // a váriavel da formula
                    $assessment = $phase->assessments
                        ->where('name', $variable)->first();
                        // first() porque só existe um nota de uma disciplina (subject)
                        // para uma avaliação.

                    if ($assessment) {
                        // Encontra a nota para avaliação na disciplina (subject)
                        $grade = $assessment->studentGrades
                            ->where('subject_id', $subject->id)
                            ->first();   

                        if ($grade) {

                            $grade->load('assessment');
                            $assessment_and_grade->push($toArray ? $grade->toArray() : $grade);
                            // Se a nota for null ele será incluida no resultado mas não 
                            // será calcudada a média
                            if (is_null($grade->grade)) {
                                $calculation = str_replace('{'.$variable.'}', 
                                '{'.$variable.':gradeIsNull}', $calculation);
                                $all_variables_found = false;
                            }else{
                                $calculation = str_replace('{'.$variable.'}', 
                                    $grade->grade, $calculation);
                            }
                        }else{
                            $calculation = str_replace('{'.$variable.'}', 
                                '{'.$variable.':notFoundGrade}', $calculation);
                            $all_grades_found = false;
                        }
                    }else{
                        $calculation = str_replace('{'.$variable.'}', 
                                '{'.$variable.':notFoundAssessment}', $calculation);
                        $all_variables_found = false;
                    }


                }

                $subject->average_calculation = $calculation;
                $subject->average_formula = $phase->average_formula;
                if ($all_variables_found === true && 
                    $all_grades_found === true) {

                    eval("\$result = $calculation;");
                    $subject->average = round($result, 1);
                 }else{
                    // Não foi possivel calcular porque não existe nota ainda
                    // ou não foi possivél resolver a formula
                    $subject->average = 'incomplete-calculation'; 
                 } 

                if ($toArray) {
                     $assessment_and_grade = $assessment_and_grade->toArray(); 
                }

                $subject->student_grades = $assessment_and_grade;

                return $subject;

            });

            if ($toArray) {
                $phase->subject_average = $phase->subject_average->toArray();
            }

        });

        // Remove o array de assesments de cada fase porque
        // ele já esta agrupado no atributo 
        // $phases[$key]['subject_average'][$key]['student_grades']
        $phases->transform(function($item, $key){
            $item = collect($item)->filter(function($item, $key){
                    return $key != 'assessments';
            });
            
            return $item;
        });

        if ($toArray) {
            $phases = $phases->toArray();
        }

        return $phases;
    }

    /**
     * Ausências/faltas e médias do aluno durante o ano letivo, 
     * agrupado por disciplina.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     *
     * @example [ 
     *             [
     *                  "id": 1,
     *                  "name": "Matématica",
     *                  "average_calculation": "((9.6 + 9.3)*0.4 + (9.3 + 9.8)*0.6)/2",
     *                  "average_formula": "( ({1º Bimestre} + ... )/n"
     *                  "average_year": 8.2,
     *                  "absences": 10,
     *                  "school_calendar_phases" => [
     *                       "id": 1,
     *                       "average": 7.2,
     *                       "average_calculation": "(6.5 + 5.7)/2",
     *                       "average_f ormula": "({Nota 1.1} + {Nota 1.2})/2",
     *                       "absences": 2,
     *                       "student_grades" => [
     *                             "id": 1,
     *                             "grade": 7.5,
     *                             "student_id": 1,
     *                             "subject_id": 1,
     *                             "assessment_id": 1,
     *                             "school_class_id": 1
     *                       ],
     *                       [....]
     *                  ],
     *                  [...]
     *             ]
     * ]
     */
    public function averagesAndAbsencesInTheYear(
        SchoolCalendar $schoolCalendar,
        Subject $filterBySubject=null)
    {  
        $subjects = $this->subjectAvaragePerYear($schoolCalendar, false, $filterBySubject);
        $absences = $this->totalAbsencesYearPerSubject($schoolCalendar);

         // Inclui quantidade de faltas do aluno por fase do ano
        $subjects->each(function($subject, $key) 
            use ($absences){
            
            $subject->absences = 0; // Total de faltas no ano para disciplina
            $subject->school_calendar_phases->transform(function($phase, $key) 
                use ($absences, $subject){
                
                // Encontra a quantidade de faltas para disciplina em uma fase do ano
                $absences_phase = $absences->filter(function($item, $key) 
                    use ($phase, $subject){

                    return $item->school_calendar_phase_id == $phase['id'] && 
                        $item->subject_id == $subject->id;
                })->first();

                $phase['absences'] = empty($absences_phase->absences) ? 0 :
                    $absences_phase->absences;

                $subject->absences += $phase['absences']; 

                return $phase;
            });

        });

        return $subjects;
    }

    /**
     * 
     * Resumo anual de notas e faltas do aluno
     * 
     * @param  integer $school_calendar_id       
     * @param  integer $school_calendar_phase_id Opctional. Inclui resumo da fase do ano.
     * 
     * @return array [
     *         'absences_year' => 25,               // Faltas no ano
     *         'best_average_year' => [
     *              '9.7'                           // Melhor nota do ano
     *              'subject' => [
     *                  'id' => 10,
     *                  'name' => 'Matématica'
     *              ]   
     *         ],
     *         'low_average_year' => [
     *             'average' => '5.2',              // Nota mais baixa do ano
     *             'subject' => [...] 
     *         ]   
     * ]  
     *                           
     * @author Kévio Castro <keviocastro@gmail.com>
     */
    public function annualSummary($schoolCalendar)
    {
         $absences = $this->absencesYear($schoolCalendar->id)
            ->count();

        // Faltas no ano
        $result['absences'] = ['total' => $absences];

        // Obter todas as médias do ano
        $averages = collect();
        $averagesPerPhase = collect($this->subjectAvaragePerYearPhase($schoolCalendar,true)); 
        
        $averagesPerPhase->each(function($item, $key) use ($averages){
            foreach ($item['subject_average'] as $key => $average) {
                $averages->push($average);        
            }
        });

        $averages_ordered = $averages->sortByDesc('average');

        $result['best_average'] = $averages_ordered->first();

        $result['low_average'] = $averages_ordered->last();

        return $result;
    }

    /**
     * 
     * Registros de chamada do aluno durante
     * um ano letivo especifico (SchoolCalendar)
     * 
     * @param  int $school_calendar_id
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * 
     * @author Kévio Castro <keviocastro@gmail.com>
     */
    public function absencesYear($school_calendar_id)
    {
        return $this->attendanceRecords()
            ->join('lessons', 'lessons.id', 
                '=' ,
                'attendance_records.lesson_id')
            
            ->join('school_classes', 
                'school_classes.id', 
                '=', 
                'lessons.school_class_id')
            ->where('presence', 0)
            ->where('school_classes.school_calendar_id', $school_calendar_id);
    }

    /**
     * 
     * Registros de chamada do aluno durante
     * uma fase de ano letivo (SchoolCalendarPhase)
     * 
     * @param  int $school_calendar_id 
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author Kévio Castro <keviocastro@gmail.com>
     */
    public function absencesYearPhase($school_calendar_phase_id)
    {
        return $this->attendanceRecords()
            ->join('lessons', 
                'lessons.id', 
                '=' ,
                'attendance_records.lesson_id')
            
            ->join('school_classes', 
                'school_classes.id', 
                '=', 
                'lessons.school_class_id')
            
            ->join('school_calendars', 
                'school_calendars.id', 
                '=', 
                'school_classes.school_calendar_id')

            ->join('school_calendar_phases',
                'school_calendar_phases.school_calendar_id',
                '=',
                'school_calendars.id')

            ->where('presence', 0)
            ->where('school_calendar_phases.id', $school_calendar_phase_id)
            
            ->whereRaw('DATE_FORMAT(lessons.start, "%Y-%m-%d") >= school_calendar_phases.start')

            ->whereRaw('DATE_FORMAT(lessons.end, "%Y-%m-%d") <= school_calendar_phases.end');
    }

    /**
     * 
     * Query para obter todas as disciplinas que o aluno estudou no ano
     * 
     * @param  integer $school_calendar_id 
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function subjectsYear($school_calendar_id)
    {
        // Turmas do aluno no ano letivo
        $classes_id = [];
       
       $schoolClasses = $this->schoolClasses()
            ->select('school_classes.id')
            ->where('school_calendar_id', $school_calendar_id)
            ->get();

        $schoolClasses->each(function($item, $key) use (&$classes_id){
            array_push($classes_id, $item->id);
        });

        // Todas as disciplinas de todas as turmas do aluno
        // no ano letivo
        $query = Subject::
            select(\DB::raw('DISTINCT subjects.id'),
                'subjects.*')
            ->join('school_class_subjects', 
                'school_class_subjects.subject_id',
                '=',
                'subjects.id')
            ->groupBy('subjects.id')
            ->whereIn('school_class_subjects.school_class_id', $classes_id);


        return $query;
    }

    /**
     * 
     * Total de faltas agrupado por disciplina e por fase do ano
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     *
     * @example  [
     *               [
     *                   absences => 4,
     *                   school_calendar_id => 1,
     *                   subject_id => 1,
     *               ],[
     *                   absences => 2,
     *                   school_calendar_id => 1,
     *                   subject_id => 2,
     *               ]
     *       ]
     */
    public function totalAbsencesYearPerSubject(SchoolCalendar $schoolCalendar)
    {
        return $this->attendanceRecords()
            ->addSelect(
                DB::raw('COUNT(school_calendar_phases.id) as absences'),
                'school_calendar_phases.id as school_calendar_phase_id',
                'subject_id'
                )
            ->join('lessons', 
                'lessons.id', 
                 '=' ,
                'attendance_records.lesson_id')

            ->join('school_calendar_phases',function($join){
                $join->on(DB::raw('DATE_FORMAT(lessons.start, "%Y-%m-%d")'), 
                    '>=', 
                    'school_calendar_phases.start');

                $join->on(DB::raw('DATE_FORMAT(lessons.end, "%Y-%m-%d")'), 
                    '<=', 
                    'school_calendar_phases.end');
            })
            ->where('presence', 0)
            ->where('school_calendar_phases.school_calendar_id', $schoolCalendar->id)
            ->groupBy('subject_id', 'school_calendar_phase_id')
            ->get();
    }

    /**
     * 
     * Resumo de ausência do aluno em um ano letivo,
     * de uma disciplina
     * 
     * @param  string $school_class_id  id da turma
     * @param  string $subject_id       id da disciplina
     * 
     * @return array [
     *         'percentage_absences_reprove' => 23,
     *         'total_lessons_year' => 180,
     *         'total_absences_year' => 22
     * ]
     * @author Kévio Castro <keviocastro@gmail.com>
     */
    public function absenceSummaryYear($school_class_id, $subject_id)
    {
        $total_absences = $this->attendanceRecords()
            ->join('lessons', 'lessons.id', '=', 'attendance_records.lesson_id')
            ->join('school_classes', 'school_classes.id', '=', 'lessons.school_class_id')
            ->where('lessons.school_class_id', $school_class_id)
            ->where('lessons.subject_id', $subject_id )
            ->where('attendance_records.presence', 0)
            ->count();

        return [
            'percentage_absences_reprove' => AccountConfig::getPercentageAbsencesReprove(),
            'total_lessons_year' =>  Lesson::totalLessonsInYear($school_class_id, $subject_id),
            'total_absences_year' => $total_absences,
        ];

        return $totalAbsences;
    }

    /**
     * Turmas onde o aluno estudou
     * 
     * @Relation 
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany 
     */
    public function schoolClasses()
    {
        return $this->belongsToMany('App\SchoolClass');
    }
}