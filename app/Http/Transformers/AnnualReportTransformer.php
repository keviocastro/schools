<?php

namespace App\Http\Transformers;


use App\Student;
use League\Fractal;

/**
 * Transformer for action SchoolClass::annualReport
 */
class AnnualReportTransformer extends Fractal\TransformerAbstract
{

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

		foreach ($subject['school_calendar_phases'] as $phase) {
			
			$grades = [];
			foreach ($phase['student_grades'] as $studentGrade) {
				array_push($grades, [
						'grade' => $studentGrade['grade'],
						'id' => $studentGrade['id'],
						'assessment_id' => $studentGrade['assessment_id']
					]);
			}

			array_push($by_phase, [
					'school_calendar_phase_id' => $phase['id'],
					'average' => $phase['average'],
					'average_calculation' => $phase['average_calculation'],
					'average_formula' => $phase['average_formula'],
					'absences' => $phase['absences'],
					'student_grades' => $grades 
				]);
		}

		return [
			'student' => collect($student)->except('annual_report', 'pivot', 'person_id'),
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