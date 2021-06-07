<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Payroll\Models\Allowance;
use Payroll\Models\CompanyProfile;
use Payroll\Models\Deduction;
use Payroll\Models\Payroll;
use Payroll\Repositories\PolicyRepository;
use Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        view()->composer(['layout'], function ($view) {
            $company = CompanyProfile::first();
            $daysPolicy = PolicyRepository::get(Payroll::MODULE_ID, Payroll::ENABLE_DAYS_ATTENDANCE);

            $view->withCompany($company)
                ->withDaysEnabled($daysPolicy);
        });

        Relation::morphMap([
            'Allowance' => Allowance::class,
            'Deduction' => Deduction::class,
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
