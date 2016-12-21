<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Escolas
 */
class School extends Model
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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at','updated_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'contacts'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'contacts' => 'array',
    ];


    /**
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schoolClasses()
    {
        return $this->hasMany('App\SchoolClass');
    }

}
