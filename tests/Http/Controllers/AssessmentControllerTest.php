<?php
namespace Tests\Http\Controllers;

use App\Assessment;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class AssessmentControllerTest extends TestCase
{
    /**
     * @covers App\Http\Controllers\AssessmentController::index
     *
     * @return void
     */
    public function testIndexSuccess()
    {
        $assessment = factory(Assessment::class)->create();
      
        $this->get('api/assessments?_sort=-id',
        	$this->getAutHeader())
        	->assertStatus(200)
        	->assertJsonFragment($assessment->toArray());
    }

    /**
     * @covers App\Http\Controllers\AssessmentController::index
     *
     * Teste do parametro _q = Full text search
     * 
     * @return void
     */
    public function testIndexParamQ()
    {
        //Testando a chave de busca _q
        // Verifica se o primeiro retornado é o mesmo
        // que foi pesquisado
        $name = 'Nota 2';
        $assessment = factory(Assessment::class)->create([
                'name' => $name
            ])->toArray();

        $struture = [
              "total",
              "per_page",
              "current_page",
              "last_page",
              "next_page_url",
              "prev_page_url",
              "from",
              "to",
              "data" => [
                [
                    "id",
                    "school_calendar_phase_id",
                    "name",
                    "_score"
                ]
              ]
            ];
        $this->get("api/assessments?_q=$name",
            $this->getAutHeader())
            ->assertStatus(200)
            ->assertJsonStructure($struture);
    }
}
