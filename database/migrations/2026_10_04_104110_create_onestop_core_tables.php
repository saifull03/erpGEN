<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop all tables in reverse dependency order
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('ledger_entries');
        Schema::dropIfExists('held_sales');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('sale_return_items');
        Schema::dropIfExists('sale_returns');
        Schema::dropIfExists('purchase_return_items');
        Schema::dropIfExists('purchase_returns');
        Schema::dropIfExists('stock_adjustment_items');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('product_price_histories');
        Schema::dropIfExists('membership_point_logs');
        Schema::dropIfExists('cash_registers');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('settings');
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

        // Roles & Permissions
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

        // Catalog: Categories, Brands, Units
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

        // Products
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
            $table->integer('minimum_stock')->default(5);
            $table->integer('current_stock')->default(0);
            $table->string('image')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('status')->default('active');
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('barcode');
            $table->index('sku');
            $table->index('status');
        });

        // Product Price History
        Schema::create('product_price_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->decimal('old_purchase_price', 12, 2)->default(0);
            $table->decimal('new_purchase_price', 12, 2)->default(0);
            $table->decimal('old_selling_price', 12, 2)->default(0);
            $table->decimal('new_selling_price', 12, 2)->default(0);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();
        });

        // Suppliers
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

        // Customers
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

        // Membership Types
        Schema::create('membership_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->integer('reward_points')->default(1);
            $table->decimal('minimum_purchase', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Members
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

        // Membership Point Logs
        Schema::create('membership_point_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->string('type'); // earned, redeemed, adjusted, expired, cancelled
            $table->integer('points');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });

        // Cash Registers / Shifts
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->string('shift_number')->unique()->nullable();
            $table->unsignedBigInteger('user_id');
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->decimal('cash_sales', 12, 2)->default(0);
            $table->decimal('cash_expenses', 12, 2)->default(0);
            $table->decimal('cash_refunds', 12, 2)->default(0);
            $table->decimal('cash_deposits', 12, 2)->default(0);
            $table->decimal('cash_withdrawals', 12, 2)->default(0);
            $table->decimal('expected_balance', 12, 2)->default(0);
            $table->decimal('actual_balance', 12, 2)->default(0);
            $table->decimal('difference', 12, 2)->default(0);
            $table->string('status')->default('open'); // open, closed
            $table->text('notes')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        // Sales
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('member_id')->nullable();
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('invoice_discount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('due_amount', 12, 2)->default(0);
            $table->decimal('change_amount', 12, 2)->default(0);
            $table->string('payment_method')->default('cash');
            $table->string('status')->default('completed'); // completed, partial, returned, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('invoice_number');
            $table->index('created_at');
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

        // Payments (Split / Multi-payment)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payable_type');
            $table->unsignedBigInteger('payable_id');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method'); // cash, card, bkash, nagad, rocket, bank_transfer
            $table->string('transaction_reference')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('payment_date');
            $table->timestamps();
        });

        // Held Sales (POS Cart Hold/Resume)
        Schema::create('held_sales', function (Blueprint $table) {
            $table->id();
            $table->string('reference_code')->unique();
            $table->unsignedBigInteger('cashier_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('member_id')->nullable();
            $table->string('member_number')->nullable();
            $table->json('cart_data');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Sales Returns
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number')->unique();
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->decimal('total_refund', 12, 2)->default(0);
            $table->string('payment_method')->default('cash');
            $table->string('reason')->nullable();
            $table->string('status')->default('completed');
            $table->timestamps();
        });

        Schema::create('sale_return_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_return_id');
            $table->unsignedBigInteger('sale_item_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('refund_subtotal', 12, 2);
            $table->timestamps();
        });

        // Purchases
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
            $table->string('status')->default('received'); // draft, confirmed, received, cancelled
            $table->text('notes')->nullable();
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

        // Purchase Returns
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number')->unique();
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('reason')->nullable();
            $table->string('status')->default('completed');
            $table->timestamps();
        });

        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_return_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        // Stock Adjustments
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_number')->unique();
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->string('reason')->default('Stock count adjustment'); // damage, expired, audit_discrepancy, general
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_adjustment_id');
            $table->unsignedBigInteger('product_id');
            $table->string('type'); // addition, subtraction
            $table->integer('quantity');
            $table->integer('previous_stock');
            $table->integer('new_stock');
            $table->timestamps();
        });

        // Stock Movements
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('type'); // sale, purchase, sale_return, purchase_return, adjustment, damage, expired
            $table->integer('quantity');
            $table->integer('previous_stock');
            $table->integer('new_stock');
            $table->string('reference')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
        });

        // Expense Categories & Expenses
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
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

        // Discounts & Promotions
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->unique()->nullable();
            $table->string('type'); // percentage, fixed, bogo
            $table->decimal('value', 12, 2)->default(0);
            $table->decimal('min_spend', 12, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Financial Ledger Entries
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('ledger_type'); // customer, supplier, cash, expense, sales, purchase
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('reference')->nullable();
            $table->text('description');
            $table->decimal('debit', 12, 2)->default(0);
            $table->decimal('credit', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->index(['ledger_type', 'entity_id']);
            $table->index('date');
        });

        // Settings
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Audit Logs
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

            $table->index(['module', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('ledger_entries');
        Schema::dropIfExists('held_sales');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('sale_return_items');
        Schema::dropIfExists('sale_returns');
        Schema::dropIfExists('purchase_return_items');
        Schema::dropIfExists('purchase_returns');
        Schema::dropIfExists('stock_adjustment_items');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('product_price_histories');
        Schema::dropIfExists('membership_point_logs');
        Schema::dropIfExists('cash_registers');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('settings');
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
