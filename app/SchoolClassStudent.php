<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Estudantes de uma turma
 */
class SchoolClassStudent extends Model
{
    use SoftDeletes;


    /**
     * Tabela associada com esse modelo
     * 
     * @var string
     */
    protected $table = 'school_class_student';

    /**
     * 
     * Atributos que não serão exibidos quando esse modelo
     * for convertido para array
     *
     * @var array
     */
    protected $hidden = ['deleted_at'];
    
    /**
     * Atributos que serão convertidos para data
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Atributos que podem ser alterados
     *
     * @var array
     */
    protected $fillable = [
        'school_class_id', 
        'student_id'];


    /**
     * Get student and person
     * 
     * @Relation 
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo('App\Student')
            ->with('person');
    }

    /**
     * Turma do aluno 
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
