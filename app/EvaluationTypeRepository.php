<?php

namespace App;

/**
 * Tipos de avaliação de aluno 
 */
class EvaluationTypeRepository
{
    const GRADE_PHASE = 'grade_phase';
    const PROGRESS_SHEET_PHASE = 'progress_sheet_phase';

    /**
     * Tipos de avaliações existentes
     * 
     * @return array
     */
    public static function all()
    {
        return [
            self::GRADE_PHASE,
            self::PROGRESS_SHEET_PHASE,
        ];
    }
}
