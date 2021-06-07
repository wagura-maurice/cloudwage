<?php

use Illuminate\Database\Seeder;
use Payroll\Models\PayGrade;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::getDefaultConnection() == 'mysql') {
            $this->call(OrganizationSeeder::class);
            $this->call(PassportSeeder::class);
            return;
        }
        $this->call(CurrencyTableSeeder::class);
        $this->call(CompanyProfilesTableSeeder::class);
        $this->call(AllowancesTableSeeder::class);
        $this->call(DeductionsTableSeeder::class);
        $this->call(DeductionSlabsTableSeeder::class);
        $this->call(ReliefsTableSeeder::class);
        $this->call(ModulesTableSeeder::class);
        $this->call(PoliciesTableSeeder::class);
        $this->call(PaymentStructureSeeder::class);
        $this->call(PayGradeSeeder::class);
        $this->call(EmployeeTypesSeeder::class);
        $this->call(PaymentMethodsSeeder::class);
    }
}
