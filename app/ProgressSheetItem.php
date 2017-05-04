<?php

namespace App;

class ProgressSheetItem extends Model
{
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
        'progress_sheet_id'
    ];

    /**
     * Avaliação descritiva a qual o item está relacionado
     *
     * @Relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function progressSheet()
    {
        return $this->belongsTo('App\ProgressSheet');
    }
}
