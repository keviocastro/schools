<?php

namespace App;

class ProgressSheetItem extends Model
{
    /**
     *
     * Atributos que não serão exibidos em arrya ou jsons
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
     * Atributos que podem ser preenchidos
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'progress_sheet_id',
        'group_id'
    ];

    /**
     * Grupo associado ao item de avaliação
     *
     * @Relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo('App\Group');
    }

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
