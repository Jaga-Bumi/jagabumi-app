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
        Schema::create('organization_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('organization_name');
            $table->text('organization_description');
            $table->enum('organization_type', ['NGO', 'Community Group', 'Educational', 'Corporate CSR', 'Environmental', 'Other']);
            $table->text('website_url')->nullable();
            $table->text('instagram_url')->nullable();
            $table->text('x_url')->nullable();
            $table->text('facebook_url')->nullable();
            $table->text('reason');
            $table->text('planned_activities');
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->text('admin_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_requests');
    }
};
