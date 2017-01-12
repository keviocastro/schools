<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class LessonPlanModel extends Model
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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['definition'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'definition' => 'array',
    ];
}
