<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (DB::getDefaultConnection() == 'mysql') {
            return;
        }
        Schema::create('payrolls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('employee_id')->unsigned()->index();
            $table->date('payroll_date');
            $table->boolean('finalized')->default(0);
            $table->text('kra');
            $table->double('basic_pay');
            $table->string('for_rate');
            $table->string('filter');
            $table->text('deductions', 500);
            $table->text('allowances', 500);
            $table->text('advances', 500);
            $table->text('loans', 500);
            $table->timestamps();

            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::getDefaultConnection() == 'mysql') {
            return;
        }
        Schema::drop('payrolls');
    }
}
