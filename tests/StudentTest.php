<?php
namespace Tests;

use App\SchoolCalendar;
use Config;
use App\SchoolCalendarPhase;
use App\Student;
use App\Subject;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class StudentTest extends TestCase
{
    /**
     * @covers App\Student::subjectAvaragePerYear
     *
     * @return void
     */
    public function testSubjectAvaragePerYear()
    {
      // Para utilizar a base de dados que tem somente dados do seeder SchoolCalendar2016
       $this->selectDatabaseTest();

    	 // Criados pelo seeder SchoolCalendar2016
        $student = Student::find(1);
        $schoolCalendar = SchoolCalendar::find(1);
        $subject = Subject::find(1);

        // Verificar a média annual da 1º disciplina, definida no seeder SchoolCalendar2016
        // Veja os comentário no seeder para mais detalhes.
        $averages = $student->subjectAvaragePerYear($schoolCalendar,true);
        $result = collect($averages)->where('id', $subject->id)->first();

        $expected = array_merge($subject->toArray(), [
                'average_year' => 9.5, // Informações do ano
                'average_calculation' => 
                    '( (9.6 + 9.3)*0.4 + (9.3 + 9.8)*0.6 )/2',
                'average_formula' => 
                    '( ({1º Bimestre} + {2º Bimestre})*0.4 + ({3º Bimestre} + {4º Bimestre})*0.6 )/2'
            ]);

        // Assert para as informações de nao da disciplina
        // Obs.: não é testado aqui a média das fases (school_calendar_phase).
        //       porque é de responsabilidade do metodo Student::subjectAvaragePerYearPhase
        $this->assertEquals(
            collect($expected)->except('school_calendar_phases'), 
            collect($result)->except('school_calendar_phases')
        );
    }

    /**
     * @covers App\Student::subjectAvaragePerYearPhase
     * 
     * @param string void
     */
    public function testSubjectAvaragePerYearPhase()
    {
      // Para utilizar a base de dados que tem somente dados do seeder SchoolCalendar2016
      self::selectDatabaseTest();

        // Criados pelo seeder SchoolCalendar2016
        $student = Student::find(1);
        $schoolCalendar = SchoolCalendar::find(1);
        $subject = Subject::find(1);

        // Verificar a média annual da 1º disciplina, definida no seeder SchoolCalendar2016
        // Veja os comentário no seeder para mais detalhes.
        $averages = $student->subjectAvaragePerYearPhase($schoolCalendar,true);
        $result = collect($averages)->where('id', $subject->id)->first();

        $expected = [
          "id" => 1,
          "name" => "1º Bimestre",
          "school_calendar_id" => 1,
          "start" => "2016-01-16",
          "end" => "2016-04-15",
          "average_formula" => "({Nota 1.1} + {Nota 1.2})/2",
          "subject_average" => [
            [
              "id" => 1,
              "name" => "Matématica",
              "average_calculation" => "(10 + 9.2)/2",
              "average_formula" => "({Nota 1.1} + {Nota 1.2})/2",
              "average" => 9.6,
              "student_grades" => [
                [
                  // "id" => 1, Não é preciso validar o id
                  // porque não a ordem que ele é criado não afeta o resultado
                  "grade" => 10.0,
                  "student_id" => 1,
                  "assessment_id" => 1,
                  "subject_id" => 1,
                  "school_class_id" => 1,
                  "assessment" => [
                    "id" => 1,
                    "school_calendar_phase_id" => 1,
                    "name" => "Nota 1.1"
                  ]
                ],
                [
                  // "id" => 6, Não é preciso validar o id
                  // porque não a ordem que ele é criado não afeta o resultado
                  "grade" => 9.2,
                  "student_id" => 1,
                  "assessment_id" => 2,
                  "subject_id" => 1,
                  "school_class_id" => 1,
                  "assessment" => [
                    "id" => 2,
                    "school_calendar_phase_id" => 1,
                    "name" => "Nota 1.2"
                  ]
                ]
              ]
            ]
          ]
        ];

        // Remove as outras disciplinas do resultado que não serão validadas.
        $result['subject_average'] = [$result['subject_average'][0]]; 
        foreach ($result['subject_average'][0]['student_grades'] as $key => $studentGrade) {
            unset($result['subject_average'][0]['student_grades'][$key]['id']);
            // Remove o StudentGrade::id porque não é possivel prever o número
            // do id que será criado para uma nota.
        }

        // Valida os dados da fase do ano
        $this->assertEquals(
            collect($expected), 
            collect($result)
        ); 


    }

}
