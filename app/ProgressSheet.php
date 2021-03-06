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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
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

    /**
     * Grupos associados aos itens da avaliação (ProgressSheetItem)
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function groups()
    {
        $groups = Group::select('groups.*')
            ->join('progress_sheet_items', 
                'progress_sheet_items.group_id', 
                '=', 'groups.id')
            ->where('progress_sheet_items.progress_sheet_id', $this->id)
            ->groupBy('groups.id')
            ->get();
            
        return $groups;
    }

}
