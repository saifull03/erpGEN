# OneStop POS + ERP Database Architecture

The OneStop database is designed with third normal form (3NF) relational integrity, decimal precision for financial correctness, indexed query performance, foreign key cascades, and soft deletes for master data.

---

## Core Schema Overview

### 1. Master & Access Control
- `roles`: `id`, `name`, `slug`, `description`
- `permissions`: `id`, `name`, `slug`, `module`
- `permission_role`: pivot table linking roles and permissions
- `users`: `id`, `role_id`, `name`, `email`, `phone`, `password`, `status` (`active`/`inactive`), `remember_token`, timestamps

### 2. Product Catalog & Inventory Master
- `categories`: `id`, `name`, `slug`, `parent_id`, `status`
- `brands`: `id`, `name`, `slug`, `status`
- `units`: `id`, `name`, `short_name`, `status`
- `products`:
  - `id`, `sku` (indexed), `barcode` (indexed), `name`, `slug`, `category_id`, `brand_id`, `unit_id`
  - `purchase_price` (`DECIMAL(12,2)`), `selling_price` (`DECIMAL(12,2)`), `wholesale_price`, `discount_price`
  - `tax_rate`, `minimum_stock`, `current_stock` (`INT`), `image`, `expiry_date`, `batch_number`, `status`
  - `softDeletes()`, `timestamps()`
- `product_price_histories`: tracks `old_purchase_price`, `new_purchase_price`, `old_selling_price`, `new_selling_price`, `changed_by`, `reason`

### 3. CRM & Membership Loyalty
- `customers`: `id`, `customer_id` (unique), `name`, `phone`, `email`, `address`, `date_of_birth`, `opening_balance`, `current_balance`, `status`
- `membership_types`: `id`, `name`, `discount_percentage`, `reward_points`, `min_spend`, `validity_days`, `is_active`
- `members`:
  - `id`, `membership_id` (unique), `member_number` (unique, searchable in POS), `customer_id`, `membership_type_id`
  - `name`, `phone`, `email`, `join_date`, `expiry_date`, `points`, `total_purchase_amount`, `total_transactions`, `status`
- `membership_point_logs`: `id`, `member_id`, `type` (`earned`, `redeemed`, `adjusted`, `expired`, `cancelled`), `points`, `reference`, `notes`, `user_id`

### 4. POS Terminal & Sales Transactions
- `cash_registers` (Cashier Shifts): `id`, `shift_number`, `user_id`, `opening_balance`, `cash_sales`, `cash_expenses`, `cash_refunds`, `expected_balance`, `actual_balance`, `difference`, `status` (`open`/`closed`), `opened_at`, `closed_at`
- `held_sales`: `id`, `reference_code`, `user_id`, `customer_id`, `member_id`, `member_number`, `cart_data` (`JSON`), `status`
- `sales`:
  - `id`, `invoice_number` (unique), `user_id`, `customer_id`, `member_id`, `shift_id`
  - `subtotal`, `discount_amount`, `invoice_discount`, `tax_amount`, `grand_total`, `paid_amount`, `due_amount`, `change_amount`
  - `payment_method`, `status` (`completed`, `partial`, `returned`), `notes`
- `sale_items`: `id`, `sale_id`, `product_id`, `quantity`, `unit_price`, `discount`, `tax`, `subtotal`
- `payments`: `id`, `sale_id`, `amount`, `payment_method`, `transaction_reference`, `user_id`, `payment_date`
- `sale_returns`: `id`, `return_number`, `sale_id`, `customer_id`, `user_id`, `shift_id`, `total_refund`, `payment_method`, `reason`
- `sale_return_items`: `id`, `sale_return_id`, `sale_item_id`, `product_id`, `quantity`, `unit_price`, `refund_subtotal`

### 5. Procurement & Vendor Relations
- `suppliers`: `id`, `supplier_id` (unique), `company_name`, `contact_person`, `phone`, `email`, `address`, `opening_balance`, `current_payable_balance`, `status`
- `purchases`: `id`, `purchase_invoice_number`, `supplier_id`, `user_id`, `date`, `subtotal`, `discount_amount`, `tax_amount`, `total`, `paid`, `due`, `status` (`draft`, `confirmed`, `received`)
- `purchase_items`: `id`, `purchase_id`, `product_id`, `quantity`, `unit_price`, `discount`, `tax`, `subtotal`
- `purchase_returns`: `id`, `return_number`, `purchase_id`, `supplier_id`, `user_id`, `total_refund`, `reason`
- `purchase_return_items`: `id`, `purchase_return_id`, `purchase_item_id`, `product_id`, `quantity`, `unit_price`, `refund_subtotal`

### 6. Inventory Movements & Stock Adjustments
- `stock_adjustments`: `id`, `adjustment_number`, `user_id`, `date`, `type` (`addition`, `subtraction`, `damage`, `expiry`), `notes`
- `stock_adjustment_items`: `id`, `stock_adjustment_id`, `product_id`, `quantity`, `unit_cost`
- `stock_movements`:
  - `id`, `product_id`, `type` (`sale`, `purchase`, `sale_return`, `purchase_return`, `adjustment`, `damaged`, `expired`)
  - `quantity`, `previous_stock`, `new_stock`, `reference`, `user_id`, `notes`, `timestamps`

### 7. Accounts, Expenses & Audit Trail
- `expense_categories`: `id`, `name`, `slug`, `description`
- `expenses`: `id`, `expense_id` (unique), `category`, `description`, `amount`, `payment_method`, `date`, `reference`, `created_by`
- `promotions`: `id`, `title`, `code`, `type` (`percentage`, `fixed`, `bogo`), `value`, `min_spend`, `start_date`, `end_date`, `is_active`
- `ledger_entries`:
  - `id`, `ledger_type` (`customer`, `supplier`, `expense`, `sales`, `purchase`, `cash`)
  - `entity_id`, `reference`, `description`, `debit` (`DECIMAL(12,2)`), `credit` (`DECIMAL(12,2)`), `running_balance`, `date`, `user_id`
- `settings`: `id`, `key` (unique), `value`
- `audit_logs`: `id`, `user_id`, `action`, `module`, `record_id`, `old_value` (`JSON`), `new_value` (`JSON`), `ip_address`, `timestamps`

---

## Decimal & Money Rules
All money fields use `DECIMAL(12, 2)` to eliminate floating-point drift. No calculations rely on Javascript precision; all sums, member discounts, taxes, and change math are recalculated server-side inside database transactions (`DB::transaction`).
