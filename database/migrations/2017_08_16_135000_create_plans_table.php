<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (DB::getDefaultConnection() != 'mysql') {
            return;
        }
        Schema::create('plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->double('price')->unique();
            $table->integer('branches_cap')->nullable();
            $table->integer('department_cap')->nullable();
            $table->integer('employee_cap')->nullable();
            $table->integer('payroll_cap')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::getDefaultConnection() != 'mysql') {
            return;
        }
        Schema::dropIfExists('plans');
    }
}
