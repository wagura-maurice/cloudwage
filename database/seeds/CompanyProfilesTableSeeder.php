<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CompanyProfilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('company_profiles')->insert([
            'logo' => 'images/smalllogo.jpg',
            'name' => 'Demo Company',
            'city' => 'Nairobi',
            'country' => 'Kenya',
            'phone' => '020-XXXXXXX',
            'mobile' => '07XXXXXXXX / 07XXXXXXXX',
            'email' => 'demo@example.com',
            'website' => 'http://example.com',
            'kra_pin' => ' ',
            'currency_id' => '75',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
