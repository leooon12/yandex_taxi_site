<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTopUpWithdrawalMorphRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('top_up_withdrawals', function (Blueprint $table) {
            $table->dropForeign(['withdrawal_bank_card_id']);
            $table->dropColumn('withdrawal_bank_card_id');
            $table->nullableMorphs('withdrawal');
            $table->renameColumn('card_number', 'requisites');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('top_up_withdrawals', function (Blueprint $table) {

            $table->renameColumn('requisites', 'card_number');


            $table->dropColumn('withdrawal_id');
            $table->dropColumn('withdrawal_type');

            $table->unsignedInteger('withdrawal_bank_card_id')->nullable();

            $table->foreign('withdrawal_bank_card_id')
                ->references('id')->on('withdrawal_bank_cards')
                ->onUpdate('cascade');
        });
    }
}
