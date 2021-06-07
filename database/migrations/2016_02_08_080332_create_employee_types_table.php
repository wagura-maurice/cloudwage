<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeTypesTable extends Migration
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
        Schema::create('employee_types', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payment_structure_id')->unsigned();
            $table->string('name');
            $table->string('description');
            $table->boolean('is_system')->default(false);
            $table->timestamps();

            $table->foreign('payment_structure_id')
                ->references('id')
                ->on('payment_structures')
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
        Schema::drop('employee_types');
    }
}
