<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // if (!Schema::hasTable('users')) {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email'); //->unique();
            $table->string('password'); //->unique();
            $table->string('role')->default('user');
            $table->string('profile_picture_path')->default('none');
            $table->timestamps();
        });
    // }
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
