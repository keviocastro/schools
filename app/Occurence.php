<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Registro de ocorrência acontecida com um aluno/professor
 */
class Occurence extends Model
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
        'deleted_by',
        'updated_by'
        ];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['comment','level_id','about_person_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @Relation
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level()
    {
    	return $this->belongsTo('App\Level');
    }
    /**
     * @Relation
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function aboutPerson()
    {
    	return $this->belongsTo('App\Person', 'about_person_id');
    }
}
