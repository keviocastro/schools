<?php
namespace Http\Controllers;

use App\Assessment;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class AssessmentControllerTest extends TestCase
{
    /**
     * @covers AssessmentController::index
     *
     * @return void
     */
    public function testIndex()
    {
        $assessment = factory(Assessment::class)->create();
      
        $this->get('api/assessments?_sort=-id',
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($assessment->toArray());

        //Testando a chave de busca _q
        // Verifica se o primeiro retornado é o mesmo
        // que foi pesquisado
        $name = 'Nota_23 abc';
        $assessment = factory(Assessment::class)->create([
                'name' => $name
            ])->toArray();

        $result = $this->getResponseContent('GET', 
            "api/assessments?_q=$name");
        // var_dump($assessment['id']);
        dump($result);
        dd('fim');
        $this->assertEquals($assessment['id'], $result['data'][0]['id']);
    }
}
