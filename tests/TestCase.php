<?php

namespace Tests;

use App\Student;
use Auth0\SDK\Auth0AuthApi;
use Config;
use Dotenv\Dotenv;
use Illuminate\Foundation\Testing\TestCase as TestCaseLara;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Tests\selectDatabaseTest;
use Illuminate\Support\Facades\Artisan;

class TestCase extends TestCaseLara
{
    use SoftDeletes;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Obtem a resposta de $uri
     * Se o conteúdo da resposta for json, o resultado é
     * retornado decodificado em array
     * 
     * @param  string $httpMethod      
     * @param  string $uri         
     * @param  array  $parameters  
     * @return mixed               
     */
    public function getResponseContent($httpMethod, $uri, $parameters=[])
    {
        $autHeader = $this->transformHeadersToServerVars($this->getAutHeader());

        $response = $this->call($httpMethod,
            $uri,
            $parameters,
            [],
            [],
            $autHeader);

        $content = $response->getContent();
        $json_response = json_decode($content, true);

        return $json_response ? $json_response : $content; 
    }

    /**
     * Obtem o id do usuário utilizado nos testes unistários
     *
     * @return string
     */
    public function getLoggedInUserId(){
        return Config::get('laravel-auth0.token_id_tester');
    }

    /**
     * Obtem id_token de autentificação auth0 para 
     * testes de autentificação 
     * 
     * @return array
     */
    public function getAutHeader()
    {
        $token = Config::get('laravel-auth0.token_user_tester');

        if (empty($token)) {
            
            $tokens = self::getTokenUserTester();
            $token = $tokens['id_token'];
            $path = base_path('.env');
            
            file_put_contents($path, str_replace(
                'AUTH0_TOKEN_USER_TESTER='.Config::get('laravel-auth0.token_user_tester'), 
                'AUTH0_TOKEN_USER_TESTER='.$token, 
                file_get_contents($path)
            ));

            putenv('AUTH0_TOKEN_USER_TESTER='.$token);
        }

        return ['authorization' => "Bearer {$token}"];
    }

    /**
     * Obtem o token_id e access_token 
     * do usuário para automatização de testes
     * 
     * 
     * @return array tokens
     */
    public static function getTokenUserTester()
    {
        $auth0Api = new Auth0AuthApi(
            Config::get('laravel-auth0.domain'), 
            Config::get('laravel-auth0.client_id'), 
            Config::get('laravel-auth0.client_secret'));
        
        $tokens = $auth0Api->authorize_with_ro(
            Config::get('laravel-auth0.email_user_tester'),
            Config::get('laravel-auth0.pass_user_tester'),
            'openid',
            'Username-Password-Authentication');
        
        return $tokens;
    }

    /**
     * Cria uma base de dados adicionando um prefixo test_ com somente os dados 
     * do seeder SchoolCalendar2016.
     * 
     * Esse metodo existe porque executar o seeder todas vezes que for executado
     * um teste que precise dele leva muito tempo.
     *
     * Esse metodo vai gerar um novo arquivo em
     * database/seeds/dump_SchoolCalendar2016_{hash}.sql. 
     * Esse arquivo deve ser sempre mantido em revisão para 
     * melhor desempenho dos testes. 
     * 
     * @return void
     */
    public function selectDatabaseTest()
    {
        $connections = Config::get('database.connections');
        
        $base = $connections['mysql']['database'];
        $user = $connections['mysql']['username'];
        $pass = $connections['mysql']['password'];
        $host = $connections['mysql']['host'];

        $dir = database_path()."/seeds";
        $hash = md5_file($dir."/SchoolCalendar2016.php");
        $name = "dump_SchoolCalendar2016_{$hash}.sql";
        $nameFile = $dir."/dump_SchoolCalendar2016_{$hash}.sql";

        // Se já existir o arquivo é porque o seeder não modificou,
        // então não precisa criar um novo dump.
        if (!file_exists($nameFile)) {
            
            Artisan::call('migrate:refresh',[
                '--seed' => true
            ]);

            Artisan::call('db:seed',[
                '--class' => 'SchoolCalendar2016'
            ]);

            // Remove os arquivos de dump desatualizados.
            $files = collect(scandir($dir));
            $oldFiles = $files->filter(function($nameFile, $key) use ($name){
                return strpos($nameFile, 'dump_school_calendar_2016_') !== false &&
                    $name != $onlyFileName;
            });
            foreach ($oldFiles as $fileName) {
                unlink($dir."/$fileName");
            }

            // Gera um novo dump
            // Esse novo arquivo deve ser adicionado em seu commit 
            // sempre que for alterado
            $process = new Process(
                "mysqldump -u{$user} -h{$host} -p{$pass} {$base} > $nameFile"
            );

            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

        }

        $base = $connections['mysql_testing']['database'];
        $user = $connections['mysql_testing']['username'];
        $pass = $connections['mysql_testing']['password'];
        $host = $connections['mysql_testing']['host'];

        $process = new Process(
            "mysql -u{$user} -h{$host} -p{$pass} -e 'CREATE DATABASE IF NOT EXISTS {$base};'"
        );

        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        Config::set('database.default', 'mysql_testing');
        
        // Se não existir registros na base dados, restaura o dump.
        // Se existir é porque a base de teste está já esta populada e não precisa ser modificada.
        if (!Schema::hasTable('students') || !(Student::count() > 0) ) {
            $process = new Process(
                "mysql -u{$user} -h{$host} -p{$pass} {$base} < $nameFile"
            );

            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        }

    }
}
