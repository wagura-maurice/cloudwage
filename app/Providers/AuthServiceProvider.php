<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Passport::tokensExpireIn(Carbon::now()->addYears(50));

        Passport::refreshTokensExpireIn(Carbon::now()->addYears(60));

        $this->setScopes();
    }

    private function setScopes()
    {
        Passport::tokensCan([
            'read-departments' => 'Access all departments',
            'read-employees' => 'View all employees',
            'read-employee-assignment' => 'View employee assignments',
            'read-employee-contracts' => 'View employee contracts',
        ]);
    }
}
