<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Occurence extends Model
{
    use SoftDeletes;

    /**
     * 
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['comment','level_id','owner_person_id','about_person_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @Relation
     * 
     * @return App\Level
     */
    public function level()
    {
    	return $this->belongsTo('App\Level');
    }
    /**
     * @Relation
     * 
     * @return App\Person
     */
    public function ownerPerson()
    {
    	return $this->belongsTo('App\Person', 'owner_person_id');
    }
    /**
     * @Relation
     * 
     * @return App\Person
     */
    public function aboutPerson()
    {
    	return $this->belongsTo('App\Person', 'about_person_id');
    }
}
