<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('org_secrets', function (Blueprint $table) {

            $table->id();

            $table->foreignId('organization_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('shipping_provider_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('key');   // api_key, token etc
            $table->text('value');   // encrypted secret

            $table->timestamps();

            $table->unique([
                'organization_id',
                'shipping_provider_id',
                'key'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_secrets');
    }
};