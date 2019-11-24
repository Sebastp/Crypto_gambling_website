<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bets', function (Blueprint $table) {
          $table->unsignedInteger('bet_id');
          $table->unsignedInteger('user_id');
          $table->float('amount', 9, 4);
          $table->enum('type', ['up', 'down']);
          $table->enum('result', ['win', 'lose', 'draw'])->nullable();
          $table->float('return', 9, 4)->nullable();
          $table->timestamp('starts_at')->default(DB::raw('CURRENT_TIMESTAMP'));
          $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bets');
    }
}
