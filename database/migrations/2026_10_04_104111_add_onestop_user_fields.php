<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable()->after('email');
            $table->string('phone')->nullable()->after('role_id');
            $table->string('status')->default('active')->after('phone');
            $table->string('avatar')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role_id', 'phone', 'status', 'avatar']);
        });
    }
};
