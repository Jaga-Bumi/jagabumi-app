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
        Schema::create('prize_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prize_id')->references('id')->on('prizes')->cascadeOnDelete();
            $table->foreignId('member_id')->references('id')->on('members')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prize_member');
    }
};
