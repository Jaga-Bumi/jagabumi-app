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
        Schema::create('prize_users', function (Blueprint $table) {
            $table->id();
            
            // NFT
            $table->string('nft_token_id')->nullable(); 
            $table->string('tx_hash')->nullable(); 
            $table->text('token_uri')->nullable();

            $table->foreignId('prize_id')->references('id')->on('prizes')->cascadeOnDelete();
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
            $table->index(['user_id', 'prize_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prize_users');
    }
};
