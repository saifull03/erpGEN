# OneStop - Supermarket POS + ERP System

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=flat&logo=tailwind-css)](https://tailwindcss.com)
[![AlpineJS](https://img.shields.io/badge/Alpine.js-3.x-77C1D2?style=flat&logo=alpinedotjs)](https://alpinejs.dev)
[![PHPUnit](https://img.shields.io/badge/Tests-40%20Passed%20(146%20Assertions)-brightgreen)](file:///c:/erpGEN/tests)
[![Currency](https://img.shields.io/badge/Currency-BDT%20(৳)-blue)](#)

A complete, production-grade **POS + ERP system** designed specifically for supermarkets, grocery stores, and supershops. Built with a clean Service-Oriented MVC architecture using **Laravel, Blade, Tailwind CSS, Alpine.js, and MySQL**.

---

## 🌟 Core System Modules & Features

### 1. High-Speed Point of Sale (POS) Terminal
- **Scanner & Keyboard-Driven Interface**: Lightning-fast cashier workflow with USB/Bluetooth barcode scanner integration and complete keyboard navigation:
  - `F2`: Focus Barcode Scanner / Live Product Search
  - `F4`: Jump Search Member / Customer Lookup
  - `F6`: Quick Invoice Discount Box
  - `F8`: Split & Multi-Payment Modal (Cash, Card, bKash, Nagad)
  - `F9`: Hold Active Cart (Multi-cart parking)
  - `F10`: Quick Cash Checkout
  - `F11`: Instant Thermal Receipt Reprint (Last Sale)
  - `ESC`: Close any active modal
- **Live Autocomplete Jump Search (F4)**: Search members instantly by card number, customer ID, phone, or name with debounced jump results showing tier badge, discount rate, and reward points.
- **Inline Fast Member Registration**: Register new members directly inside the POS terminal modal without leaving the checkout screen or resetting the active cart.
- **Instant Cash Tender & Change Calculator**: 
  - Dynamic cash tender input with one-click quick denomination buttons (`[Exact]`, `[500]`, `[1000]`).
  - Real-time visual change indicator (e.g. Customer pays ৳1,000 on a ৳200 bill &rarr; displays high-contrast green banner: `Change to Return: ৳ 800.00`).
  - Shortage and exact-tender validation alerts.
- **Cart Hold & Multi-Cart Parking**: Park multiple busy customer carts (`Hold #001`, `Hold #002`) preserving customer, membership, items, and quantities.
- **Split & Multi-Payment**: Mix multiple tenders (Cash, Card, bKash, Nagad) on a single invoice.

### 2. Dual Format Receipt & Invoice Printing
- **80mm & 58mm Thermal Receipts**:
  - Monospace zero-margin print stylesheet optimized for thermal POS printers (Epson, Xprinter, Rongta, Bixolon, Citizen).
  - High-contrast typography with itemized breakdown, VAT breakdown, cashier details, customer points, cash tendered, and change returned.
  - On-screen paper width switcher (`80mm` / `58mm`).
- **A4 Commercial Invoice**: Full-page corporate invoice for wholesale or institutional orders.
- **Instant Reprint**: Cashiers can reprint the latest receipt with one click or by pressing `F11`.

### 3. Membership & Customer Loyalty System
- **Tiers & Benefits**: Configurable membership tiers (Standard, Silver, Gold, Platinum) with percentage discounts and point accrual ratios.
- **Instant Loyalty Points**: Automatically credits loyalty points on completed sales and logs every transaction in `membership_point_logs`.
- **Digital Printable Cards**: Generate scannable barcode membership cards for members.

### 4. Comprehensive Inventory & Stock Movement Auditing
- **Product Management**: SKU, Barcode, Categories, Brands, Units, Purchase/Selling/Wholesale prices, Tax/VAT rates, Batch numbers, Expiry dates, Images, and Min/Max stock thresholds.
- **Immutable Stock Trail**: Every addition, reduction, sale, purchase, or return creates an unalterable record in `stock_movements` with before/after quantities and audit references.
- **Price History Logs**: Automatically records historical purchase and selling price revisions with user attribution.
- **Inventory Alerts**: Live notifications for out-of-stock items, low-stock thresholds, and items expiring within 7, 30, and 60 days.

### 5. Purchasing & Supplier Management
- **Purchase Orders (PO)**: Multi-item receiving with automated inventory increments and cost accounting.
- **Supplier Ledger**: Double-entry ledger tracking purchases, payments, and outstanding vendor payables.
- **Purchase Returns**: Return damaged or expired stock to suppliers with credit balance adjustments.

### 6. Sales Returns & Itemized Refunds
- **Itemized Return Flow**: Return selected items with quantity validation (cannot return more than originally purchased).
- **Automated Reversals**: Restores stock, decrements loyalty points, logs cash refunds in the active shift, and posts ledger adjustments.

### 7. Cash Drawer & Shift Management
- **Cash Drawer Sessions**: Cashier opens shift with starting float &rarr; system tracks live cash sales, refunds, and petty expenses &rarr; cashier enters physical counted cash on close.
- **Reconciliation**: Automated variance detection with overage/shortage calculation.

### 8. Financial Ledger & Accounting
- **Unified Double-Entry Ledger**: Transaction statements for Customers, Suppliers, Sales, Purchases, Expenses, and Cash.
- **Operational Expenses**: Utility bills, salaries, rent, maintenance with category breakdowns.
- **P&L Reporting**: Real-time Cost of Goods Sold (COGS) income statements.

### 9. Role-Based Access Control (RBAC) & Audit Logs
- **5 Built-in User Roles**: Super Admin, Admin/Store Manager, Cashier, Inventory Manager, Accountant.
- **Audit Trails**: Security tracking with user IP, action, module, record ID, and before/after JSON payloads.

---

## 🚀 Installation & Setup

### 1. System Requirements
- **PHP**: 8.2+ with `pdo_mysql`, `bcmath`, `mbstring`, `fileinfo`
- **Database**: MySQL 8.0+ / MariaDB 10.5+
- **Composer**: 2.x
- **Node.js**: 18+ & NPM

### 2. Setup Commands

```bash
# 1. Clone repository and install dependencies
composer install
npm install

# 2. Configure Environment
cp .env.example .env
php artisan key:generate

# 3. Configure Database in .env
# DB_DATABASE=onestop_erp
# DB_USERNAME=root
# DB_PASSWORD=

# 4. Migrate and Seed Default Demo Data
php artisan migrate:fresh --seed

# 5. Build Assets & Launch Development Server
npm run build
php artisan serve
```

Access the system in your browser at **`http://localhost:8000`**.

---

## 🔐 Default Demo Accounts

All seeded accounts use password: **`password`**

| Role | Email | Access & Permissions |
| :--- | :--- | :--- |
| **Super Admin** | `admin@onestop.local` | Full unrestricted system access, audit logs, and global settings |
| **Store Manager** | `manager@onestop.local` | Products, purchases, sales, customers, reports, staff management |
| **Cashier** | `cashier@onestop.local` | POS terminal, customer/member checkout, returns, shifts |
| **Inventory Manager** | `inventory@onestop.local` | Stock movements, adjustments, alerts, purchases |
| **Accountant** | `accountant@onestop.local` | General ledger, expenses, sales/purchase/P&L financial reports |

---

## 🧪 Automated Test Suite

The application includes an automated Feature and Unit test suite covering authentication, POS cart operations, JSON and multi-tender payments, loyalty point logs, stock deductions, purchases, returns, cashier shifts, and ledgers.

```bash
php artisan test
```

```text
Tests:    40 passed (146 assertions)
Duration: ~5.6s
```

---

## 📁 Technical Architecture & Documentation

Detailed engineering documentation is located in the [`docs/`](file:///c:/erpGEN/docs) directory:
- [Database Architecture & Schema (`docs/database.md`)](file:///c:/erpGEN/docs/database.md)
- [System Architecture & Services (`docs/architecture.md`)](file:///c:/erpGEN/docs/architecture.md)
- [Module Functional Specifications (`docs/modules.md`)](file:///c:/erpGEN/docs/modules.md)
- [Production Deployment Guide (`docs/deployment.md`)](file:///c:/erpGEN/docs/deployment.md)

---

## 🛡️ License
Proprietary supermarket management software built for **OneStop Supermarket**.
