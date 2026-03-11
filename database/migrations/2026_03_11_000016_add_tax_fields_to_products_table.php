<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 2)->default(18)->after('price')->comment('Tax percentage (e.g., 18 for 18%)');
            $table->string('tax_type', 20)->default('gst')->after('tax_rate')->comment('gst, cgst_sgst, igst');
            $table->string('hsn_code', 20)->nullable()->after('tax_type')->comment('HSN/SAC code for GST');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['tax_rate', 'tax_type', 'hsn_code']);
        });
    }
};