<?php 

namespace App\Http\Transformers;

use App\Exceptions\TransformerException;
use App\StudentGrade;
use Illuminate\Database\Eloquent\Collection;
use League\Fractal;

/**
* Transformer for StudentGrades
* 
*/
class StudentGradeTransformer extends Fractal\TransformerAbstract
{
	
	/**
	 * Agrupa aulas em uma colleção disciplina e fase do ano
	 * school_calendar_phase_id e subject_id deve existir em $grades
	 * 
	 * @param  Collection $grades 
	 * 
	 * @return \Illuminate\Database\Eloquent\Collection"
	 */
	public static function groupBySubjectAndPhase(Collection $grades)
	{
		$grouped = $grades->groupBy(function($item, $key) use ($grades){
			
			if (empty($item->school_calendar_phase_id) ||
				empty($item->subject_id)) {
				
				throw new TransformerException('The attribute school_calendar_phase_id '.
					'or subject_id is not in collection.'); 
			}

            return $item->school_calendar_phase_id.'-'.$item->subject_id; 
        });

		$formated = collect();
        $grouped->each(function($item, $key) use (&$formated){
            $ids = explode('-',$key);
            $formated->push([
                    'school_calendar_phase_id' => $ids[0],
                    'subject_id' => $ids[1],
                    'assessments' => $item->toArray()
                ]);
        });

        return $formated;
	}
}