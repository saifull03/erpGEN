<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Branches Table
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_main')->default(false);
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });

        // 2. Warehouses Table
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });

        // 3. Warehouse Product Stock (Branch/Warehouse level inventory)
        Schema::create('warehouse_product_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('stock')->default(0);
            $table->timestamps();

            $table->unique(['warehouse_id', 'product_id']);
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        // 4. Stock Transfers
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_no')->unique();
            $table->unsignedBigInteger('from_warehouse_id');
            $table->unsignedBigInteger('to_warehouse_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->string('status')->default('pending'); // pending, in_transit, received, cancelled
            $table->text('notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();

            $table->foreign('from_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('to_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
        });

        // 5. Stock Transfer Items
        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_transfer_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->integer('received_quantity')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('stock_transfer_id')->references('id')->on('stock_transfers')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        // 6. Add branch_id and warehouse_id to users, sales, purchases, expenses, cash_registers, stock_movements
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('role_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('customer_id');
            $table->unsignedBigInteger('warehouse_id')->nullable()->after('branch_id');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('supplier_id');
            $table->unsignedBigInteger('warehouse_id')->nullable()->after('branch_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('expense_category_id');
        });

        Schema::table('cash_registers', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('user_id');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id')->nullable()->after('product_id');
            $table->unsignedBigInteger('branch_id')->nullable()->after('warehouse_id');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn(['warehouse_id', 'branch_id']);
        });

        Schema::table('cash_registers', function (Blueprint $table) {
            $table->dropColumn(['branch_id']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['branch_id']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['branch_id', 'warehouse_id']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['branch_id', 'warehouse_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['branch_id']);
        });

        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('warehouse_product_stocks');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('branches');
    }
};
