<?php

use Illuminate\Database\Seeder;

class EmployeeTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employee_types')->insert([
            'payment_structure_id' => 1,
            'name' => 'Permanent & Pensionable',
            'description' => 'These are the employees with a permanent status.',
            'is_system' => true,
        ]);
    }
}
