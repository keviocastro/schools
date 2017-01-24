<?php 

use App\SchoolCalendarPhase;
use App\SchoolClass;
use App\StudentGrade;

/**
 * Fábrica para criar objectos relacionados a notas dos alunos
 */
class StudentGradesFactory
{

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
     *                                     'assessment_id' => 1,  // Se não informar valor 
     *                                                            // para avaliação (assessment_id)
     *                                                            // será atribuido "grade"
     *                                                            // para todas as avaliações
     *                                                            // Deixando a nota com a mesma
     *                                                            // média do valor de "grade"
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
    public static function create(
        SchoolCalendarPhase $schoolCalendarPhase,
        SchoolClass $schoolClass,
        array $subjects,
        array $fixedDataSubjects=[]
        ){

        $studentGrades = [];
        $faker = \Faker\Factory::create();

        $schoolClass->students->each(function($student, $key) 
            use ($schoolCalendarPhase, $subjects, &$studentGrades, 
                $fixedDataSubjects, $faker, $schoolClass){
            
                foreach ($schoolCalendarPhase->assessments as $key => $assessment) {
                    foreach ($subjects as $key => $subject) {


                        $grade = false;
                        if (!empty($fixedDataSubjects)) {
                            
                            foreach ($fixedDataSubjects as $data) {
                                
                                if ($student->id == $data['student_id'] && 
                                    $subject->id == $data['subject_id']
                                    ) {

                                    if (empty($data['assessment_id'])) {
                                        $grade = $data['grade'];
                                    }elseif($assessment->id == $data['assessment_id']){
                                        $grade = $data['grade'];
                                    }
                                    
                                }
                            }
                        }


                        $grade = $grade ? $grade : $faker->randomFloat(1,0,10);

                        if ($grade != 'do-not-create') {
                            $studentGrade = factory(App\StudentGrade::class)->make([
                                    'assessment_id' => $assessment->id,
                                    'student_id' => $student->id,
                                    'subject_id' => $subject->id,
                                    'grade' => $grade,
                                    'school_class_id' => $schoolClass->id
                                ])->toArray();

                            array_push($studentGrades, $studentGrade);
                        }
                    }
                }
        });

        StudentGrade::insert($studentGrades);
    }
}