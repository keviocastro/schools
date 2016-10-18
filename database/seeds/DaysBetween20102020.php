<?php

use App\Day;
use Illuminate\Database\Seeder;

class DaysBetween20102020 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $query  = "insert into days (day, created_at, updated_at) select day, NOW(), NOW() from (select adddate('2010-01-01',t4*10000 + t3*1000 + t2*100 + t1*10 + t0) day from (select 0 t0 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0, (select 0 t1 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1, (select 0 t2 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2, (select 0 t3 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3, (select 0 t4 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v where day between '2010-01-01' and '2020-12-31'";

        $result = DB::select($query);
       	Day::insert($result);
    }
}
