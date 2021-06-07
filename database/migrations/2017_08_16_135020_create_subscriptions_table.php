<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionsTable extends Migration
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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('plan_id')->index()->unsigned();
            $table->bigInteger('organization_id')->index()->unsigned();
            $table->date('start_date');
            $table->date('end_date');
            $table->double('amount_paid');
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
        Schema::dropIfExists('subscriptions');
    }
}
