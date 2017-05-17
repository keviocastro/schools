<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class StudentProgressSheet extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'option_identifier',
        'progress_sheet_item_id',
        'student_id',
        'school_calendar_phase_id',
        'school_class_id'
    ];
    
    /**
     * Atributos que não serão exibidos em array
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
        'deleted_at',
        'created_by',
        'deleted_by',
        'updated_by'
    ];

    /**
     * Itens que componhem uma ficha avaliativa
     * 
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function progressSheetItem()
    {
        return $this->belongsTo('App\ProgressSheetItem');
    }
    
    /**
     * Estudante avaliado
     * 
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo('App\Student');
    }
    
    /**
     * Fase em que a avaliacao aconteceu
     * 
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function schoolCalendarPhase()
    {
        return $this->belongsTo('App\SchoolCalendarPhase');
    }
    
    /**
     * Turma a qual a ficha avaliativa pertence
     * 
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function schoolClass()
    {
        return $this->belongsTo('App\SchoolClass');
    }
}
