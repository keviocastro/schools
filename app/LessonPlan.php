<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class LessonPlan extends Model
{
    use SoftDeletes;

    /**
     * 
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
            'deleted_at',
            'updated_at',
            'created_at',
            'created_by',
            'deleted_by',
            'updated_by'
        ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lesson_plan_template_id',
        'content'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'content' => 'array',
    ];

    /**
     * Modelo de plano de aula utilizado para o plano
     * 
     * @Relation
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lessonPlanModel()
    {
    	return $this->belongsTo('App\LessonPlanModel');
    }

    /**
     * Aulas que o plano de aula está relacionado
     * 
     * @Relation 
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lessons()
    {
        return $this->hasMany('App\Lesson');
    }
}
