<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganizationsTable extends Migration
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

        Schema::create('organizations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('database');
            $table->boolean('is_active')->default(true);
            $table->date('subscription_end')->nullable();
            $table->integer('branches_cap')->nullable();
            $table->integer('department_cap')->nullable();
            $table->integer('employee_cap')->nullable();
            $table->integer('payroll_cap')->nullable();
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
        Schema::dropIfExists('organizations');
    }
}
