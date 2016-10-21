<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentResponsible extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['student_id', 'person_id'];

     /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the person record.
     * 
     * @Relation
     *
     * @return App\Person
     */
    public function person()
    {
        return $this->belongsTo('App\Person');
    }
}
