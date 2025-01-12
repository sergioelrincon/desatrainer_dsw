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
        Schema::create('desa_trainers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('desa_buttons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_trainer_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('label');
            $table->json('area');
            $table->string('color')->default('#007bff')->nullable(); 
            $table->boolean('is_blinking')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desa_buttons');
        Schema::dropIfExists('desa_trainers');
    }
};
