<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repurchase_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('last_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->date('last_purchase_date');
            $table->date('next_reminder_date');
            $table->integer('reminder_interval_days')->default(30);
            $table->enum('status', ['pending', 'sent', 'converted', 'skipped'])->default('pending');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('reminded_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
            
            $table->index('customer_id');
            $table->index('next_reminder_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repurchase_reminders');
    }
};