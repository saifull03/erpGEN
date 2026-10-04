<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('cash_registers');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('members');
        Schema::dropIfExists('membership_types');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('products');
        Schema::dropIfExists('units');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->primary(['permission_id', 'role_id']);
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('barcode')->unique()->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->default(0);
            $table->decimal('wholesale_price', 12, 2)->default(0);
            $table->decimal('discount_price', 12, 2)->default(0);
            $table->decimal('tax_rate', 8, 2)->default(0);
            $table->integer('minimum_stock')->default(0);
            $table->integer('current_stock')->default(0);
            $table->string('image')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('status')->default('active');
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_id')->unique();
            $table->string('company_name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->decimal('current_payable_balance', 12, 2)->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id')->unique();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->decimal('current_balance', 12, 2)->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('membership_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->integer('reward_points')->default(0);
            $table->decimal('minimum_purchase', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('membership_id')->unique();
            $table->string('member_number')->unique();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('membership_type_id');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('join_date');
            $table->date('expiry_date')->nullable();
            $table->integer('points')->default(0);
            $table->decimal('total_purchase_amount', 12, 2)->default(0);
            $table->integer('total_transactions')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('member_id')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('due_amount', 12, 2)->default(0);
            $table->string('status')->default('completed');
            $table->timestamps();
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_invoice_number')->unique();
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('paid', 12, 2)->default(0);
            $table->decimal('due', 12, 2)->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
        });

        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_id')->unique();
            $table->string('category');
            $table->text('description');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->default('cash');
            $table->date('date');
            $table->string('reference')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->decimal('expected_balance', 12, 2)->default(0);
            $table->decimal('actual_balance', 12, 2)->default(0);
            $table->decimal('difference', 12, 2)->default(0);
            $table->string('status')->default('open');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('type');
            $table->integer('quantity');
            $table->integer('previous_stock');
            $table->integer('new_stock');
            $table->string('reference')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');
            $table->string('module');
            $table->string('record_id')->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('cash_registers');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('members');
        Schema::dropIfExists('membership_types');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('products');
        Schema::dropIfExists('units');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
