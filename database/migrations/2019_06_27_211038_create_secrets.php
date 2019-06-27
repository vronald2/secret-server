<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSecrets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('secrets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('hash', 128)->unique();
            $table->text('secretText');
            $table->integer('remainingViews');
            $table->dateTime('expiresAt')->nullable()->default(null);
            $table->dateTime('createdAt');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('secrets');
    }
}
