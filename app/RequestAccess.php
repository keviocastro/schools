<?php

namespace App;
use App\Person;

use Illuminate\Database\Eloquent\SoftDeletes;

class RequestAccess extends Model
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
        'user_id', 'status'
    ];
    
    public static function create($attributes)
    {
        $person = Person::
            where('user_id', $attributes['user_id'])
            ->first();
        
        if(!$person){
            $personAttributes['user_id'] = $attributes['user_id'];
            Person::create($personAttributes);
        }

        return parent::create($attributes);
    }

}
