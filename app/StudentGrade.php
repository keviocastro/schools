<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Notas dos alunos de uma avaliação em uma fase do ano letivo
 *
 * avaliação = Assessment
 * fase do ano letivo = SchoolCalendarPhase
 * 
 */
class StudentGrade extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['grade','student_id','subject_id','assessment_id','school_class_id'];
    
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
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo('App\Student', 'student_id');
    }

    /**
     * Avaliação da nota: Exmplo: 2º Nota do 1º Bimestre 
     * 
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subject()
    {
        return $this->belongsTo('App\Subject');
    }

    /**
     * Avaliação da nota do aluno
     * 
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assessment()
    {
        return $this->belongsTo('App\Assessment');
    }

    /**
     * Turma em qual foi registrada a nota
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
