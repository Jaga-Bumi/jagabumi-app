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
        Schema::create('quest_participant', function (Blueprint $table) {
            $table->id();
            $table->dateTime('joined_at');
            $table->enum('status', ['REGISTERED', 'COMPLETED', 'APPROVED', 'REJECTED'])->default('REGISTERED');
            $table->text('video_url')->nullable();
            $table->longText('description')->nullable();
            $table->dateTime('submission_date')->nullable();
            $table->foreignId('quest_id')->references('id')->on('quests')->cascadeOnDelete();
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quest_participant');
    }
};
