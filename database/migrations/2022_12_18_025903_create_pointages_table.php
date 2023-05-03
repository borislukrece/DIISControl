<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pointages', function (Blueprint $table) {
            $table->id();
            $table->string("id_agent", 255);
            $table->string("id_admin", 255);
            $table->string("heure_arrivee", 5);
            $table->string("heure_depart", 5);
            $table->string("total_heure", 8)->nullable()->default(0);
            $table->date("date_actuelle");
            $table->string("now", 255);
            $table->string("motif", 255);
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
        Schema::dropIfExists('pointages');
    }
};
