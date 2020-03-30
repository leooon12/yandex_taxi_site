<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopUpWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('top_up_withdrawals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transaction_number');
            $table->string('card_number');
            $table->unsignedInteger('sum');
            $table->unsignedInteger('status');
            $table->unsignedInteger('withdrawal_bank_card_id')->nullable();
            $table->timestamps();


            $table->foreign('withdrawal_bank_card_id')
                ->references('id')->on('withdrawal_bank_cards')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('top_up_withdrawals');
    }
}
