<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('activity_type', ['call', 'email', 'meeting', 'note', 'status_change', 'assignment', 'other']);
            $table->string('subject');
            $table->text('description')->nullable();
            $table->timestamp('activity_date');
            $table->timestamp('next_follow_up_date')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index('lead_id');
            $table->index('activity_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_activities');
    }
};