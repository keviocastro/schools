<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\Auth0Service;

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
        'created_by',
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
        'phone',
        'user_id' // id do usuário no serviço de autentificação: exmplo auth0
        ];

    /**
     * Registro de professor associado com a pessoa
     *
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function teacher()
    {
        return $this->hasOne('App\Teacher');
    }

    /**
     * Registro de estudante associado com a pessoa
     *
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function student()
    {
        return $this->hasOne('App\Teacher');
    }
    
    /**
     * Cria o registro de pessoa a partir dos dados do provedor de serviço de autentificação
     * @todo Utiliza atualmente somente o provedor de autentificação auth0.
     *       Deve ser criado drivers para que não seja dependente somente desse provedor.
     * 
     * @return \App\Person
     */
    public static function createFromAuthServiceProvider($user_id)
    {
        $attributes = Auth0Service::getUser($user_id);
        $personAttributes = [
            'name' => $attributes['name'], 
    	    'gender' => empty($attributes['gender']) ? '' : $attributes['gender'], 
            'avatarUrl' => empty($attributes['picture']) ? '' : $attributes['picture'],
            'phone' => empty($attributes['phone_number']) ? '' : $attributes['phone_number'],
            'user_id' => empty($attributes['user_id']) ? '' : $attributes['user_id']
        ];

        return Person::create($personAttributes);
    }
}
