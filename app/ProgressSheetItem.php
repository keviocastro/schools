<?php

namespace App;

class ProgressSheetItem extends Model
{
    use \Conner\Tagging\Taggable;

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
        'updated_by',
        'tagged' //  appends tags anexa dois atributos tagged e tags. tagged não é necessário
    ];

    /**
     * Atributos que podem ser preenchidos
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'progress_sheet_id'
    ];

    protected $appends = ['tags'];

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
