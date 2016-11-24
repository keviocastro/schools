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

        AccountConfig::firstOrCreate([
                    // Nota a qual o aluno é considerado como ótimo
                    'name' => 'grade_threshold_great',
                    'default' => '9'
                ]);
        AccountConfig::firstOrCreate([
                    // Nota a qual o aluno é considerado como bom
                    'name' => 'grade_threshold_good',
                    'default' => '6'
                ]);
        AccountConfig::firstOrCreate([
                    // Nota limite para aprovação de um aluno em uma
                    // disciplina
                    'name' => 'passing_grade_threshold',
                    'default' => '6'
                ]);
    }
}
