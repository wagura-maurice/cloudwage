<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PaymentStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        DB::table('payment_structures')->insert([
            'id' => 1,
            'name' => 'Monthly',
            'description' => 'Payments in this category are remitted every month.',
            'unit' => 'Month',
            'is_system' => true,
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
