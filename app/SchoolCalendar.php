<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Calendários escolares/anos letivos
 */
class SchoolCalendar extends Model
{
	 use SoftDeletes;

     /**
     * The attributes that should be hidden for arrays.
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
    	'year', 
    	'start', 
    	'end',
        'average_formula'];

    /**
     * Os atributos que serão convertidos para formato nativo
     *
     * @var array
     */
    protected $casts = [
        'year' => 'integer',
    ];

    /**
     * Turma do ano letivo
     * 
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schoolClasses()
    {
        return $this->hasMany('App\SchoolClass');
    }

    /**
     * Get the Phases of the school calendar
     * 
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function phases()
    {
        return $this->hasMany('App\SchoolCalendarPhase');
    }

}
