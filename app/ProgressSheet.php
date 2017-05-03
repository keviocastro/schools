<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class ProgressSheet extends Model
{
	 use SoftDeletes;

     /**
     * 
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'options'
    ];

}
