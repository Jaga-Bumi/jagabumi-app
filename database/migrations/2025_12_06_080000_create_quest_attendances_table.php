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
        Schema::create('quest_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quest_participant_id')->constrained('quest_participant')->cascadeOnDelete();
            $table->enum('type', ['CHECK_IN', 'CHECK_OUT']);
            $table->decimal('proof_latitude', 18, 15); 
            
            $table->decimal('proof_longitude', 18, 15);
            
            $table->text('proof_photo_url');
            $table->text('notes')->nullable();
            $table->decimal('distance_from_quest_location', 18, 15)->nullable();
            $table->boolean('is_valid_location')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quest_attendances');
    }
};
