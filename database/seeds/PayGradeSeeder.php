<?php

use Carbon\Carbon;
use FontLib\Table\Type\name;
use Illuminate\Database\Seeder;

class PayGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        DB::table('pay_grades')->insert([
            'id' => 1,
            'name' => 'Group A',
            'payment_structure_id' => 1,
            'currency_id' => 75,
            'basic_salary' => 50000,
            'annual_increment' => 5,
            'default_allowances' => '1',
            'default_deductions' => '1,2,3',
            'is_system' => true,
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
