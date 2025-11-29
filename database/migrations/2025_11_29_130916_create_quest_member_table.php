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
        Schema::create('quest_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quest_id')->references('id')->on('quests')->cascadeOnDelete();
            $table->foreignId('member_id')->references('id')->on('members')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quest_member');
    }
};
