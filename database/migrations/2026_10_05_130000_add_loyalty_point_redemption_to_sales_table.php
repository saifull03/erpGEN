<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'points_redeemed')) {
                $table->integer('points_redeemed')->default(0)->after('invoice_discount');
            }
            if (!Schema::hasColumn('sales', 'points_discount')) {
                $table->decimal('points_discount', 12, 2)->default(0)->after('points_redeemed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'points_redeemed')) {
                $table->dropColumn('points_redeemed');
            }
            if (Schema::hasColumn('sales', 'points_discount')) {
                $table->dropColumn('points_discount');
            }
        });
    }
};
