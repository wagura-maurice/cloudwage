<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvancePaymentsTable extends Migration
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
        Schema::create('advance_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('advance_id')->unsigned()->index();
            $table->bigInteger('employee_id')->unsigned()->index();
            $table->double('amount');
            $table->text('comment'); // use to state its from salary or cash
            $table->timestamps();

            $table->foreign('advance_id')
                ->references('id')
                ->on('advances')
                ->onDelete('cascade');

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
        Schema::drop('advance_payments');
    }
}
