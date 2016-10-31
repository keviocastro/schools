<?php

use App\AccountConfig;
use Illuminate\Database\Seeder;

class AccountConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AccountConfig::firstOrCreate([
                    // Percentual máximo de faltas que um aluno pode ter
                    // para não ser reprovado.
                    // Exemplo: Existem 200 aulas de Matématica no ano, 
                    //          e consideramos que percentage_absences_reprove == 15,
                    //          então o aluno pode faltar no máximo 30 aulas (200*0,15).
                    'name' => 'percentage_absences_reprove',
                    'default' => '25'
                ]);
    }
}
