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
        'deleted_at',
        'created_at',
        'updated_at',
        'created_by',
        'deleted_by',
        'updated_by'
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

    /**
     * Itens da avaliação descritiva
     *
     * @Relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function items()
    {
        return $this->hasMany('App\ProgressSheetItem');
    }

}
