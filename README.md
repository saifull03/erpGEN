# OneStop - Supermarket POS + ERP System

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=flat&logo=tailwind-css)](https://tailwindcss.com)
[![PHPUnit](https://img.shields.io/badge/Tests-100%25%20Passed-brightgreen)](file:///c:/erpGEN/tests)
[![Currency](https://img.shields.io/badge/Currency-BDT%20(৳)-blue)](#)

A complete, production-grade **POS + ERP system** designed specifically for supermarkets, grocery stores, and supershops. Built with a clean Service-Oriented MVC architecture using **Laravel, Blade, Tailwind CSS, Alpine.js, and MySQL**.

---

## 🌟 Key Features

### 1. High-Speed Point of Sale (POS)
- **Keyboard & Scanner Optimized**: USB/Bluetooth barcode scanner integration with rapid keyboard hotkeys:
  - `F2`: Product Search & Barcode Input
  - `F4`: Customer & Member Lookup
  - `F6`: Invoice Discount Modal
  - `F8`: Payment Breakdown Modal
  - `F9`: Hold Active Cart
  - `F10`: Quick Cash Checkout
  - `ESC`: Close Modals
- **Cart Hold & Resume**: Multi-cart parking (`Hold #001`, `Hold #002`) preserving customer, membership, and items.
- **Split Payments**: Multi-method tender (Cash, Card, bKash, Nagad) with exact change calculation.
- **Receipt Printing**: Dual format output:
  - Standard **80mm Thermal Receipt** (`/sales/{id}/thermal`)
  - Full-page **A4 Commercial Invoice** (`/sales/{id}/invoice`)

### 2. Comprehensive Inventory & Stock Movement Auditing
- **Product Management**: SKU, Barcode, Categories, Brands, Units, Purchase/Selling/Wholesale prices, VAT/Tax rates, Batch numbers, Expiry dates, Images, and Min/Max thresholds.
- **Immutable Stock Trail**: Every addition, reduction, sale, purchase, or return creates a detailed record in `stock_movements` with before/after counts and movement reasons.
- **Price History**: Tracks historical purchase and selling price updates with reasons and author timestamps.
- **Inventory Alerts**: Real-time alerts for out-of-stock items, low-stock thresholds, and items expiring within 7, 30, and 60 days.

### 3. Membership & Customer Loyalty System
- **Tiers & Benefits**: Configurable membership tiers (Standard, Silver, Gold, Platinum) with percentage discounts and point accrual rules.
- **Instant POS Lookup**: Search by Member ID, Card Number, or Phone to auto-apply tier discounts and award loyalty points.
- **Printable Member Cards**: Printable digital ID card with scannable barcode.
- **Point Logs**: Immutable transaction logs for all earned, redeemed, or reversed points.

### 4. Procurement & Supplier Relations
- **Purchase Orders**: Multi-item receiving with automated inventory increments.
- **Supplier Ledger**: Double-entry ledger tracking purchases, payments, and outstanding vendor payables.
- **Purchase Returns**: Return damaged or expired stock to suppliers with balance credits.

### 5. Sales Returns & Refunds
- **Itemized Return Flow**: Return selected items with quantity validation (cannot return more than originally purchased).
- **Automated Reversals**: Restores stock, decrements loyalty points, logs cash refunds in the active shift, and posts ledger adjustments.

### 6. Cash & Shift Management
- **Cash Drawer Sessions**: Cashier opens shift with starting float &rarr; system tracks live cash sales, refunds, and petty expenses &rarr; cashier enters physical counted cash on close.
- **Reconciliation**: Automated variance detection (shortage/overage).

### 7. Financial Ledger & Accounting
- **Unified Double-Entry Ledger**: Transaction statements for Customers, Suppliers, Sales, Purchases, Expenses, and Cash.
- **Operational Expenses**: Utility bills, salaries, rent, maintenance with category breakdowns.
- **P&L Reporting**: Real-time Cost of Goods Sold (COGS) income statements.

### 8. Role-Based Access Control (RBAC) & Audit Logs
- **5 Built-in User Roles**: Super Admin, Admin/Store Manager, Cashier, Inventory Manager, Accountant.
- **Audit Trails**: Security tracking with user IP, action, module, record ID, and before/after JSON payloads.

---

## 🚀 Quick Start & Installation

### 1. Requirements
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

Access the system at **`http://localhost:8000`**.

---

## 🔐 Default Demo Credentials

All seeded accounts use password: **`password`**

| Role | Email | Permissions / Access |
| :--- | :--- | :--- |
| **Super Admin** | `admin@onestop.local` | Full unrestricted system access & settings |
| **Store Manager** | `manager@onestop.local` | Products, purchases, sales, customers, reports, staff |
| **Cashier** | `cashier@onestop.local` | POS terminal, customer/member checkout, returns, shifts |
| **Inventory Manager** | `inventory@onestop.local` | Stock movements, adjustments, alerts, purchases |
| **Accountant** | `accountant@onestop.local` | General ledger, expenses, sales/purchase/P&L reports |

---

## 🧪 Running Automated Tests

The application includes an end-to-end Feature and Unit test suite covering authentication, POS sales, loyalty point awards, purchases, returns, shifts, and ledgers.

```bash
php artisan test
```

---

## 📁 Documentation

Detailed documentation is available in the [`docs/`](file:///c:/erpGEN/docs) directory:
- [Database Architecture & Schema (`docs/database.md`)](file:///c:/erpGEN/docs/database.md)
- [System Architecture & Services (`docs/architecture.md`)](file:///c:/erpGEN/docs/architecture.md)
- [Module Functional Specifications (`docs/modules.md`)](file:///c:/erpGEN/docs/modules.md)
- [Production Deployment Guide (`docs/deployment.md`)](file:///c:/erpGEN/docs/deployment.md)

---

## 🛡️ License
Proprietary software built for **OneStop Supermarket**.
