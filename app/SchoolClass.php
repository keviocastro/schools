<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Turmas (1º Ano A, Jardim I - A, Jardim I - B)
 */
class SchoolClass extends Model
{
     use SoftDeletes;

     /**
     * 
     * Atributos que não são exibidos json ou arrays
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
     * Atributos convertidos em formato de data
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Atributos que podem ser preenchidos/modificados
     *
     * @var array
     */
    protected $fillable = [
        'identifier', 
        'grade_id', 
        'shift_id',
        'school_calendar_id',
        'school_id',
        'evaluation_type',
        'progress_sheet_id'];


    protected $appends = ['evaluation_type'];


    /**
     * Obtem o tipo que os alunos da turma são avaliados.
     * 
     * descriptive_sheet = Ficha de avaliação descritiva preenchida por fase
     * grade_per_phase = Nota numérica caclulada por fase
     *
     * @return string
     */
    public function getEvaluationTypeAttribute()
    {
        $evaluation_type = ['grade_per_phase', 'progress_sheet_per_phase'];
        return $this->attributes['evaluation_type'] = $evaluation_type[array_rand($evaluation_type, 1)];
    }

    /**
     * Turno da turma 
     *
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shift()
    {
    	return $this->belongsTo('App\Shift');
    }

    /**
     * Estudantes da aula
     * 
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function students()
    {
        return $this->belongsToMany('App\Student')->with('person');
    }

    /**
     * Ano letivo da turma
     * 
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function schoolCalendar()
    {
        return $this->belongsTo('App\SchoolCalendar');
    }

    /**
     * Disciplinas da turma
     * 
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subjects()
    {
        return $this->belongsToMany('App\Subject', 'school_class_subjects');
    }

    /**
     * Nível escolar da turma (Ex.: 1º Ano, 2º Ano)
     *
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grade()
    {
       return $this->belongsTo('App\Grade');
    }

    /**
     * Escola que a turma pertence
     *
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo('App\School');
    }

}
