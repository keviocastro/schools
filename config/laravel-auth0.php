<?php

return array(

    /**
      * Para configurar a decodificação de tokens
      * Solucionando esse problema:
      *     Os horários do servidor e do browser do usuário (que gera o token em outra aplicação SPA)
      *     tem pequenas diferença, impossibilitando decodificar um token gerar em uma data acima a 
      *     data atual do servidor de SchoolsApi.
      *
      * Esse atributo configura a classe Firebase\JWT para permitir 
      * uma pequena diferença toleravel
      *
      * Em segundos
      *
      */
    'jwt_leeway' => env('JWT_LEEWAY', 60),

    /**
     * @deprecated Deve ser utilizado as credenciais por papel de usuário
     *             AUTH0_USER_ID_ROLE_TEACHER
     * 
     * Credenciais para autentificação de usuário utilizado
     * na automatização de testes
     */
    'email_user_tester' => env('AUTH0_EMAIL_USER_TESTER'),
    'pass_user_tester' => env('AUTH0_PASS_USER_TESTER'),
    'token_user_tester' => env('AUTH0_TOKEN_USER_TESTER'),
    'token_id_tester' => env('AUTH0_ID_USER_TESTER'),

    'user_id_role_teacher_1' => str_replace('\\', '', env('AUTH0_USER_ID_ROLE_TEACHER_1')), 
    'user_id_role_teacher_2' => str_replace('\\', '', env('AUTH0_USER_ID_ROLE_TEACHER_2')),
    'user_id_role_teacher_3' => str_replace('\\', '', env('AUTH0_USER_ID_ROLE_TEACHER_3')),
    'user_id_role_teacher_3' => str_replace('\\', '', env('AUTH0_USER_ID_ROLE_TEACHER_4')),
    // replace porque foi preciso escapar caracteres especiais no token.



    /*
    |--------------------------------------------------------------------------
    |   Your auth0 domain
    |--------------------------------------------------------------------------
    |   As set in the auth0 administration page
    |
    */

    'domain'        => env('AUTH0_DOMAIN'),
    /*
    |--------------------------------------------------------------------------
    |   Your APP id
    |--------------------------------------------------------------------------
    |   As set in the auth0 administration page
    |
    */

    'client_id'     => env('AUTH0_CLIENT_ID'),

    /*
    |--------------------------------------------------------------------------
    |   Your APP secret
    |--------------------------------------------------------------------------
    |   As set in the auth0 administration page
    |
    */
    'client_secret' => env('AUTH0_CLIENT_SECRET'),


   /*
    |--------------------------------------------------------------------------
    |   The redirect URI
    |--------------------------------------------------------------------------
    |   Should be the same that the one configure in the route to handle the
    |   'Auth0\Login\Auth0Controller@callback'
    |
    */

    'redirect_uri'  => env('AUTH0_REDIRECT_URI'),

    /*
    |--------------------------------------------------------------------------
    |   Persistence Configuration
    |--------------------------------------------------------------------------
    |   persist_user            (Boolean) Optional. Indicates if you want to persist the user info, default true
    |   persist_access_token    (Boolean) Optional. Indicates if you want to persist the access token, default false
    |   persist_id_token        (Boolean) Optional. Indicates if you want to persist the id token, default false
    |
    */

    // 'persist_user' => true,
    // 'persist_access_token' => false,
    // 'persist_id_token' => false,

    /*
    |--------------------------------------------------------------------------
    |   The authorized token issuers
    |--------------------------------------------------------------------------
    |   This is used to verify the decoded tokens when using RS256
    |
    */
    'authorized_issuers'  => [env('AUTH0_AUTHORIZED_ISSUERS')],
    /*
    |--------------------------------------------------------------------------
    |   The authorized token issuers
    |--------------------------------------------------------------------------
    |   This is used to verify the decoded tokens when using RS256
    |
    */
    'api_identifier'  => [env('AUTH0_API_IDENTIFIER')],

);
