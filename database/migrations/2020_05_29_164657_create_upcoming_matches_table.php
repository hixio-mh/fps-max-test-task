<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpcomingMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upcoming_matches', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table
                ->integer('panda_score_upcoming_match_id', false, true)
                ->nullable(false)
                ->index();
            $table->string('type', 100)->nullable(false);
            $table->string('status', 100)->nullable(false);
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
        Schema::dropIfExists('upcoming_matches');
    }
}
