<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['administrateur', 'animateur', 'utilisateur'])->default('utilisateur');
            $table->rememberToken();
            $table->timestamps();
        });
        
        \App\Models\User::create([
            'nom' => 'Salma',
            'prenom' => 'ElQadi',
            'email' => 'salma@gmail.com',
            'password' => Hash::make('12345678'), 
            'role' => 'administrateur', 
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
