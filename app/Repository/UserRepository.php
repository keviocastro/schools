<?php

namespace App\Repository;

use Auth0\Login\Auth0User;
use Auth0\Login\Auth0JWTUser;
use Auth0\Login\Contract\Auth0UserRepository;

class UserRepository implements Auth0UserRepository
{
    /**
     * @todo $user->id foi incluido para que o package spatie/laravel-responsecache funcione
     *      Estava ocorrendo um erro "BaseCacheProfile::cacheNameSuffix() must be of the type string, null returne"
     *      porque o atributo id do usuário loado não existia.
     *      O usuário auth0 deve ser persistido na table persons, conforme esse exemplo:
     *      https://github.com/auth0/laravel-auth0#storing-users-in-your-database
     *      A solução  $user->id = $user->sub; é provisória.
     * 
     * @param \Auth0\Login\Contract\stdClass $jwt
     *
     * @return Auth0JWTUser
     */
    public function getUserByDecodedJWT($jwt)
    {
        $user = new Auth0JWTUser($jwt);
        $user->id = $user->sub;
        return $user;
    }

    /**
     * @param array $userInfo
     *
     * @return Auth0User
     */
    public function getUserByUserInfo($userInfo)
    {
        return new Auth0User($userInfo['profile'], $userInfo['accessToken']);
    }

    /**
     * @param \Auth0\Login\Contract\the $identifier
     *
     * @return Auth0User|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function getUserByIdentifier($identifier)
    {
        // Get the user info of the user logged in (probably in session)
        $user = \App::make('auth0')->getUser();

        if ($user === null) {
            return;
        }

        // Build the user
        $auth0User = $this->getUserByUserInfo($user);

        // It is not the same user as logged in, it is not valid
        if ($auth0User && $auth0User->getAuthIdentifier() == $identifier) {
            return $auth0User;
        }
    }
}
