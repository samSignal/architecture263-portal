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
        Schema::create('plan_applications', function (Blueprint $table) {
            $table->id();
            
            // Link to authenticated user
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            
            // Step 1: Project Details
            $table->string('plan_no')->unique()->comment('Official Plan Number');
            $table->string('stand_no');
            $table->text('postal_address');
            $table->decimal('estimated_cost', 15, 2);
            $table->string('purpose');
            $table->string('industry_type')->nullable();
            $table->string('project_type'); // New Building, Alteration, Addition

            // Step 2: Ownership & Professionals
            $table->string('owner_name');
            $table->text('owner_address');
            $table->string('owner_phone')->nullable();
            
            $table->string('architect_name')->nullable();
            $table->text('architect_address')->nullable();
            $table->string('architect_phone')->nullable();
            
            $table->string('contractor_name')->nullable();
            $table->text('contractor_address')->nullable();
            $table->string('contractor_phone')->nullable();
            
            $table->string('supervision'); // Architect, Engineer, etc.

            // Step 3: Dimensions & Specs
            $table->decimal('area_ground_floor', 10, 2);
            $table->decimal('area_first_floor', 10, 2)->nullable();
            $table->decimal('area_total', 10, 2);
            $table->decimal('area_outbuildings', 10, 2)->nullable();
            $table->string('fire_fighting_equipment')->nullable();

            // Status tracking
            $table->string('status')->default('pending'); // pending, approved, rejected
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_applications');
    }
};
