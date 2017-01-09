<?php

namespace App\Http\Transformers;


use App\Student;

/**
 * Transformer for action SchoolClass::annualReport
 */
class AnnualReportTransformer
{


	/**
	 * Soma de todas a médias, durante um calendário escolar, 
	 * de todos os alunos da turma.
	 *
	 * Dado utilizado par calcular média geral da turma.
	 * 
	 * @var integer
	 */
	private $sumAllStudentGrades = 0;
	
	/**
	 * Quantidade de médias calculadas durante o calendário escolar,
	 * de todos os alunos da turma.
	 *
	 * Dado utilizado para calcular média atitimética geral da turma.
	 * 
	 * @var integer
	 */
	private $amountCalculatedAverages = 0;

	/**
	 * Soma das médias de todas as notas dos alunos por fase do calendário escolar.
	 *
     * [
     *  '{school_calendar_phase_id}' => [
     *      'sum' 		=> '{sumAllStudentGrades}',
     *      'amount' 	=> '{$amountCalculatedAverages}'
     *   ],
     *   ...
     * ]
     *
	 * @var array
	 */
	private	$sumAllStudentGradesByPhase = [];

    /**
	 * Calcula um nota total de todas as notas do aluno durante o calendário escolar.
	 * Calcula um média geral de todos os alunos durante o calendário por fase.
	 * 
	 * @param  $collection 
	 * @return array
	 */
	public function transformCollection($collection)
	{
		$errorMsg = 'no-averages-for-the-calculation';
		$resource['report_by_student'] = [];
		foreach ($collection as $student) {
			array_push($resource['report_by_student'], $this->transform($student));
		}

		$average = $this->amountCalculatedAverages == 0 ? $errorMsg :
			round($this->sumAllStudentGrades / $this->amountCalculatedAverages, 2); 

		$resource['school_class_report'] = [
			'school_calendar_report' => [
					'average' => $average
				]
		];

		$resource['school_class_report']['phases_report'] = [];
		foreach ($this->sumAllStudentGradesByPhase as $key => $item) {
			
			$average = $item['amount'] == 0 ? $errorMsg :
				round($item['sum'] / $item['amount'], 2);

			array_push($resource['school_class_report']['phases_report'], [
						'school_calendar_phase_id' => $key,
						'average' => $average
					]);
		}

		return $resource;
	}

	/**
	 * Simplifica removendo informações repetidas e remove os dados 
	 * da disciplina (subject), já o reurso implementado por SchoolClass::annualReport 
	 * é somente de uma disciplina.
	 * 
	 * @param  Student $student 
	 * @return array
	 */
	public function transform(Student $student)
	{
		$student = $student->toArray();
		$by_phase = [];

		$subject = $student['annual_report'][0]->toArray();

		$subject['school_calendar_phases'] = $subject['school_calendar_phases']->toArray();
		foreach ($subject['school_calendar_phases'] as $phase) {
			
			$phase['student_grades'] = $phase['student_grades']->toArray();

			$grades = [];
			foreach ($phase['student_grades'] as $studentGrade) {
				array_push($grades, [
						'grade' => $studentGrade['grade'],
						'id' => $studentGrade['id'],
						'assessment_id' => $studentGrade['assessment_id']
					]);

				// Se a nota do aluno foi calculada, então ela será utilizada para
				// calcular a média geral da turma que é a média aritmética
				// de todas as notas (por fase) dos alunos da turma.
				if (is_numeric($phase['average'])) {
					$this->sumAllStudentGrades += $phase['average'];
					$this->amountCalculatedAverages++;
				}

			}

			array_push($by_phase, [
					'school_calendar_phase_id' => $phase['id'],
					'average' => $phase['average'],
					'average_calculation' => $phase['average_calculation'],
					'average_formula' => $phase['average_formula'],
					'absences' => $phase['absences'],
					'student_grades' => $grades 
				]);

			// Para calcular a média da turma por fase, que é:
			// A média aritimética de todos os alunos da turma
			if(!isset($this->sumAllStudentGradesByPhase[$phase['id']])){
				$this->sumAllStudentGradesByPhase[$phase['id']] = [
					'amount' => 0, 'sum' => 0];
			}

			if (is_numeric($phase['average'])) {
                $this->sumAllStudentGradesByPhase[$phase['id']]['amount']++; 
                $this->sumAllStudentGradesByPhase[$phase['id']]['sum'] += $phase['average']; 
			}
		}

		return [
			'student' => collect($student)
				->except('annual_report', 'pivot', 'person_id')
				->toArray(),
			// Relatório/Boletim de notas e faltas do ano
			'school_calendar_report' => [
				'average' => $subject['average_year'],
				'average_calculation' => $subject['average_calculation'],
				'average_formula' => $subject['average_formula'],
				'absences' => $subject['absences'],
			],
			// Relatório/Boletim de notas e faltas por fase do ano
			'phases_report' => $by_phase, 
		];
	}	
}