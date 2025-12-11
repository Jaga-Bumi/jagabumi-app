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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 30);
            $table->string('slug')->unique();
            $table->string('handle', 30)->unique();
            $table->string('org_email')->unique();
            $table->text('desc');
            $table->string('motto')->nullable();
            $table->text('banner_img')->nullable();
            $table->text('logo_img')->nullable();
            $table->text('website_url')->nullable();
            $table->text('instagram_url')->nullable();
            $table->text('x_url')->nullable();
            $table->text('facebook_url')->nullable();
            $table->enum('status', ['INACTIVE', 'ACTIVE', 'HIATUS'])->default('ACTIVE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
