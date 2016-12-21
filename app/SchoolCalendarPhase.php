<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Fases avaliativas de um ano letivo (bimestres, semestres, etc.)
 */
class SchoolCalendarPhase extends Model
{
    use SoftDeletes;

    /**
     * 
     * Atributos que devem ser ocultados 
     * durante a conversão do objeto para array
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
     * Atributos que devem ser modificados para data
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Atributos que podem ser incluídos/alterados na base de dados
     *
     * @var array
     */
    protected $fillable = [
        'school_calendar_id', 
        'name', 
        'start',
        'end',
        'average_formula'];

    /**
     * Avaliações da fase do ano letivo
     *
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assessments()
    {
        return $this->hasMany('App\Assessment');
    }

    /**
     * Aulas da fase do ano escolar
     * 
     * @Relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function lessons()
    {
        return $this->hasManyThrough(
            'App\Lesson', 'App\SchoolClass', 
            'school_calendar_id', 'school_class_id', 'school_calendar_id')
            ->where(\DB::raw('DATE_FORMAT(lessons.start, "%Y-%m-%d")'), '>=', $this->start)
            ->where(\DB::raw('DATE_FORMAT(lessons.end, "%Y-%m-%d")'), '<=', $this->end);
    }

    /**
     * Calendário escolar da fase avaliativa
     * 
     * @Relation
     * 
     * @param \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function schoolCalendar()
    {
        return $this->belongsTo('App\SchoolCalendar');
    }
}
