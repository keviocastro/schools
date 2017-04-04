<?php

namespace App;

class Teacher extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['person_id'];


    /**
     * Registro de pessoa associado com o professor
     * 
     * @Relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function person()
    {
        return $this->belongsTo('App\Person');
    }

    /**
     * Obtem um professor por user_id do seriço de autentificação
     * 
     * @param  int $user_id 
     * @return Teacher
     */
    public static function findByUserId($user_id)
    {   
        $person = Person::where('user_id', $user_id)->first();
        if ($person) {
            return $person->teacher;
        }else{
            return [];
        }
    }    
}
