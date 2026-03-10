<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shipping_provider_id')->constrained()->cascadeOnDelete();
            $table->string('tracking_number')->nullable();
            $table->string('awb_number')->nullable();
            $table->string('shipment_id')->nullable()->comment('Provider shipment ID');
            $table->enum('status', ['pending', 'created', 'picked', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned'])->default('pending');
            $table->decimal('shipping_cost', 10, 2)->nullable();
            $table->string('courier_name')->nullable();
            $table->text('tracking_url')->nullable();
            $table->text('label_url')->nullable();
            $table->json('tracking_history')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('order_id');
            $table->index('tracking_number');
            $table->index('awb_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};