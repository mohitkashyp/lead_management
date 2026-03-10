<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('sku');
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('product_category_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('price', 10, 2);
            $table->decimal('cost', 10, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->decimal('weight', 8, 2)->nullable()->comment('in kg');
            $table->decimal('length', 8, 2)->nullable()->comment('in cm');
            $table->decimal('width', 8, 2)->nullable()->comment('in cm');
            $table->decimal('height', 8, 2)->nullable()->comment('in cm');
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['organization_id', 'sku']);
            $table->index('organization_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};