<?php

use App\Http\Controllers\PaymentMethodsController;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Payroll\Models\PaymentMethod;
use Payroll\Models\UDF;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $method = PaymentMethod::create([
            'name' => 'Cash',
            'description' => 'Payments remitted as cash.',
            'has_udf' => true,
            'coinage' => true,
            'is_system' => true,
        ]);

        UDF::insert([
            'udfable_id' => $method->id,
            'udfable_type' => PaymentMethod::class,
            'field_title' => 'Collectors Name',
            'field_name' => 'collectors_name',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
