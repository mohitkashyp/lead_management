<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('lead_number')->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('alternate_phone')->nullable();
            $table->foreignId('lead_source_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_status_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->string('product_interest')->nullable();
            $table->decimal('estimated_value', 10, 2)->nullable();
            $table->date('next_follow_up_date')->nullable();
            $table->text('notes')->nullable();
            $table->json('custom_fields')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['organization_id', 'lead_number']);
            $table->index(['organization_id', 'phone']);
            $table->index(['organization_id', 'assigned_to']);
            $table->index(['organization_id', 'lead_status_id']);
            $table->index(['organization_id', 'next_follow_up_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};