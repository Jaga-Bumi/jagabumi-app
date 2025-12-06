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
        Schema::create('quests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('desc');
            $table->text('banner_url');
            $table->text('location_name');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('radius_meter')->default(100);
            $table->text('liveness_code')->nullable(); // Secret code di onsite
            $table->dateTime('registration_start_at');
            $table->dateTime('registration_end_at');
            $table->dateTime('quest_start_at');
            $table->dateTime('quest_end_at');
            $table->dateTime('judging_start_at');
            $table->dateTime('judging_end_at');
            $table->dateTime('prize_distribution_date');
            $table->enum('status', ['IN REVIEW', 'REJECTED', 'APPROVED', 'ACTIVE', 'ENDED', 'CANCELLED'])->default('IN REVIEW');
            $table->integer('participant_limit');
            $table->integer('winner_limit');
            $table->dateTime('approval_date')->nullable();
            $table->foreignId('org_id')->references('id')->on('organizations')->cascadeOnDelete();
            $table->timestamps();
        });
    } 

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quests');
    }
};
