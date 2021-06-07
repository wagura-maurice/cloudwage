<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PassportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('oauth_clients')->insert([
            [
                'name' => config('app.name') . ' Personal Access Client',
                'secret' => 'cVQaW1RljMOMlfRK2w3UUzA5kuFaxzhlpwApzx9t',
                'redirect' => 'http://localhost',
                'personal_access_client' => 1,
                'password_client' => 0,
                'revoked' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => config('app.name') . ' Password Grant Client',
                'secret' => 'gHATffqyaazsdK8sdgyxuadOYlLtAQBhsmuOPeia',
                'redirect' => 'http://localhost',
                'personal_access_client' => 0,
                'password_client' => 1,
                'revoked' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        Artisan::call('passport:keys');
    }
}
