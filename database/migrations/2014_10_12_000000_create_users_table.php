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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string("nom", 30);
            $table->string("prenom", 30);
            $table->string("tel", 14);
            $table->boolean("sexe")->default(false);
            $table->date("date_naissance");
            $table->string("lieu_naissance", 125);
            $table->string("id_service", 255);
            $table->string("id_fonction", 255);
            $table->string("adresse", 255);
            $table->string("email", 125);
            $table->string("username", 30)->unique();
            $table->boolean('admins')->default(false);
            $table->string("profil_img", 255)->nullable(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
