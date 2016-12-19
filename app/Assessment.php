<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Avaliações de uma fase do ano letivo
 */
class Assessment extends Model
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
        'created_at',
        'updated_at',
        'deleted_by',
        'updated_by'
        ];
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'school_calendar_phase_id',
        'name'
    ];

    /**
     * Notas da avaliação
     * 
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentGrades()
    {
        return $this->hasMany('App\StudentGrade');
    }

    /**
     * Get a SchoolCalendarPhase
     *
     * @Relation
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function schoolCalendarPhase()
    {
        return $this->belongsTo('App\SchoolCalendarPhase');
    }
}
