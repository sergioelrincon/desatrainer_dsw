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
        Schema::create('transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_instruction_id')->constrained('instructions')->onDelete('cascade');
            $table->foreignId('to_instruction_id')->constrained('instructions')->onDelete('cascade');
            $table->enum('trigger', ['time', 'user_choice', 'loop']);
            $table->integer('time_seconds')->nullable();
            $table->foreignId('desa_button_id')->nullable();
            $table->integer('loop_count')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('transitions', function (Blueprint $table) {
            $table->dropForeign(['from_instruction_id']);
            $table->dropForeign(['to_instruction_id']);
        });
    
        Schema::dropIfExists('transitions');

    }
};
