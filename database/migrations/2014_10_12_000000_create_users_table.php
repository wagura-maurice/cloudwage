<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('organization_id')->index()->unsigned();
            $table->string('name');
//            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->text('permissions')->nullable();
            $table->boolean('change_password')->default(false);
            $table->timestamp('last_login')->nullable();
            $table->boolean('is_master')->default(false);
            $table->boolean('is_activated')->default(false);
            $table->string('activation_code');
            $table->string('database');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
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
        Schema::dropIfExists('users');
    }
}
