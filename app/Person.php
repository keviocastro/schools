<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Informações básica de pessoas
 * 
 * Table: people
 */
class Person extends Model
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
    	'name', 
    	'birthday', 
    	'gender', 
    	'place_of_birth', 
    	'more',
        'avatarUrl',
        'phone'];
}
