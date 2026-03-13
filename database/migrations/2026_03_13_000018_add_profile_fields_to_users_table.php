<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->json('settings')->nullable()->after('avatar');
            $table->string('two_factor_secret')->nullable()->after('settings');
            $table->boolean('two_factor_enabled')->default(false)->after('two_factor_secret');
            $table->boolean('is_active')->default(true)->after('two_factor_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'avatar',
                'settings',
                'two_factor_secret',
                'two_factor_enabled',
                'is_active',
            ]);
        });
    }
};