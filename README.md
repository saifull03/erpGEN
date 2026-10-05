# erpGEN - Supermarket POS + ERP System

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=flat&logo=tailwind-css)](https://tailwindcss.com)
[![AlpineJS](https://img.shields.io/badge/Alpine.js-3.x-77C1D2?style=flat&logo=alpinedotjs)](https://alpinejs.dev)
[![PHPUnit](https://img.shields.io/badge/Tests-58%20Passed%20(245%20Assertions)-brightgreen)](file:///c:/erpGEN/tests)
[![Catalog](https://img.shields.io/badge/Products-1%2C051%20Seeded-blue)](file:///c:/erpGEN/database/seeders/ProductSeeder.php)
[![Currency](https://img.shields.io/badge/Currency-BDT%20(৳)-blue)](#)

**erpGEN** is a complete, production-grade **Multi-Branch POS + Supermarket ERP System** designed specifically for supermarket chains, grocery stores, and retail supershops. Built with a clean Service-Oriented MVC architecture using **Laravel 11, Blade, Tailwind CSS, Alpine.js, and SQLite/MySQL**.

---

## 🌟 Core System Modules & Features

### 1. Multi-Branch & Multi-Warehouse Architecture
- **Multi-Outlet Support**: Manage multiple branch outlets (*Main Outlet HQ, Dhanmondi Express, Gulshan Flagship, Uttara Outlet*) with independent storage locations.
- **Dynamic Branch Switcher**: Super Admins and managers can seamlessly toggle between consolidated chain-wide HQ analytics or single-outlet context.
- **Inter-Branch Stock Transfers**: Full multi-item requisition, dispatch (shipment with stock deduction), receiving with destination warehouse increment, and cancellation workflows with comprehensive audit logs.
- **Branch-Wise User Assignment**: Cashiers and store staff are strictly linked to their physical branch.

### 2. Strict Cashier Access Controls & Branch Manager Password Authorization
- **POS-Only Terminal Lock**: Cashiers are automatically routed to the POS terminal upon login with branch-locked session security.
- **Universal Non-POS Route Interception**: Any cashier attempting to access administrative URLs (Dashboard, Products, Sales, Purchases, Settings, etc.) is intercepted by `EnsureCashierBranchAndRole` middleware.
- **Branch Manager Authorization**: Entering the active Branch Manager or Super Admin password temporarily unlocks a 15-minute administrative session with an on-screen timer and one-click exit lock.
- **In-POS Manager Override Modal**: Unlock manual discounts, order voids, or back-office access directly from the checkout screen without losing active cart items.

### 3. Super Administrator Universal Access & Full Management
- **Unrestricted Authority**: Super Admin (`role: super-admin`) bypasses all role restrictions, Gates, branch locks, and authorization requirements.
- **Full Management**: Super Admin can manage all products, staff roles, branches, warehouses, stock adjustments, financial ledgers, audit logs, and global settings.

### 4. 1,000+ Realistic Product Catalog & Stock Tracking
- **Extensive Seeded Catalog**: Over 1,051 realistic products across 10 categories (Beverages, Dairy, Bakery, Snacks, Pantry, Fresh Produce, Meat & Fish, Personal Care, Household & Cleaning, Baby Care).
- **Realistic Stock Distribution**: Active healthy inventory, realistic low-stock threshold triggers, out-of-stock items, and expiry alerts (7, 30, and 60 days).
- **Immutable Stock Auditing**: Every transaction creates automated before/after tracking in `stock_movements`.

### 5. High-Speed Point of Sale (POS) Terminal
- **Scanner & Keyboard-Driven Interface**: Lightning-fast cashier workflow with USB/Bluetooth barcode scanner integration and complete keyboard navigation:
  - `F2`: Focus Barcode Scanner / Live Product Search
  - `F4`: Jump Search Member / Customer Lookup
  - `F6`: Quick Invoice Discount Box
  - `F8`: Split & Multi-Payment Modal (Cash, Card, bKash, Nagad)
  - `F9`: Hold Active Cart (Multi-cart parking)
  - `F10`: Quick Cash Checkout
  - `F11`: Instant Thermal Receipt Reprint (Last Sale)
  - `ESC`: Close any active modal
- **Inline Fast Member Registration**: Register new members directly inside the POS terminal modal without leaving the checkout screen.
- **Instant Cash Tender & Change Calculator**: Real-time visual change banner with one-click denomination buttons (`[Exact]`, `[500]`, `[1000]`).
- **Loyalty Program**: ৳100 purchase = 1 membership point, automatically credited with tiered member discounts.

### 6. Dual Format Receipt & Invoice Printing
- **80mm & 58mm Thermal Receipts**: Monospace zero-margin print stylesheet optimized for thermal POS printers with width switcher.
- **A4 Commercial Invoice**: Full-page corporate invoice for wholesale or institutional orders.
- **Instant Reprint**: Cashiers can reprint the latest receipt with one click or by pressing `F11`.

### 7. Cash Drawer & Shift Management
- **Cash Drawer Sessions**: Starting float tracking &rarr; live cash sales, refunds, and petty expenses &rarr; physical counted cash reconciliation with variance detection.

### 8. Financial Ledger & Accounting
- **Unified Double-Entry Ledger**: Transaction statements for Customers, Suppliers, Sales, Purchases, Expenses, and Cash.
- **P&L Reporting**: Real-time Cost of Goods Sold (COGS) income statements.

---

## 🔐 Default Demo Accounts

All seeded accounts use password: **`password`**

| Role | Email | Access & Permissions |
| :--- | :--- | :--- |
| **Super Admin** | `admin@onestop.local` | Full unrestricted management of all branches, staff, products, finance, and settings |
| **Store Manager** | `manager@onestop.local` | Branch management, products, purchases, sales, inventory, cashier approvals |
| **Cashier** | `cashier@onestop.local` | POS terminal only, branch locked, customer checkout, shifts |
| **Inventory Manager** | `inventory@onestop.local` | Stock movements, warehouse transfers, adjustments, alerts |
| **Accountant** | `accountant@onestop.local` | General ledger, expenses, sales/purchase/P&L financial reports |

---

## 🧪 Automated Test Suite

```bash
php artisan test
```

```text
Tests:    54 passed (215 assertions)
Duration: ~10.9s
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
