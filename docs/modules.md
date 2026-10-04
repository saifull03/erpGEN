# OneStop Module Documentation

This document describes the operational modules included in the OneStop Supermarket POS + ERP suite.

---

## 1. Point of Sale (POS) Module
- **Hardware Integration**: Standard USB / Bluetooth Barcode Scanners emit keystrokes directly into the quick scanner input.
- **Keyboard Shortcuts**:
  - `F2`: Focus product search & scanner
  - `F4`: Open customer / member selection modal
  - `F6`: Apply custom invoice discount
  - `F8`: Trigger payment modal
  - `F9`: Hold current active cart
  - `F10`: Quick complete cash checkout
  - `ESC`: Dismiss open modal dialogs
- **Cart Hold System**: Cashiers can hold multiple customer carts (`Hold #001`, `Hold #002`) with customer and member associations preserved, process other sales, and resume or delete held carts on demand.
- **Split Payment & Change Calculator**: Supports Cash, Debit/Credit Card, bKash, and Nagad split payments. Auto-calculates change returned to the customer.
- **Receipt Outputs**: Supports direct 80mm thermal receipt printing (`/sales/{id}/thermal`) and full-page A4 commercial invoices (`/sales/{id}/invoice`).

---

## 2. Inventory & Product Catalog Management
- **Product Details**: SKU, Barcode, Category, Brand, Unit, Purchase Price, Selling Price, Wholesale Price, VAT/Tax %, Min Stock, Current Stock, Expiry Date, Batch Number, Description.
- **Price History Audit**: Tracks historical price modifications with timestamps, user ID, old price, new price, and reasoning.
- **Stock Movement Log**: Real-time auditing for all stock changes.
- **Stock & Expiry Alerts**: Real-time monitoring for out-of-stock items, low-stock thresholds, and items nearing expiration within 7, 30, and 60 days.

---

## 3. Membership & Customer Loyalty System
- **Tiers & Benefits**: Configurable membership tiers (Standard, Silver, Gold, Platinum) with percentage discounts and point accrual ratios.
- **POS Lookup**: Cashiers search member card number or phone directly in the POS to automatically apply tier discounts and accrue loyalty points.
- **Printable Member Card**: Generates a card with barcode for physical customer checkout.
- **Point Audit Trail**: Every point earned, redeemed, or reversed is logged in `membership_point_logs`.

---

## 4. Procurement & Supplier Relations
- **Purchase Orders**: Multi-item purchase receiving with quantity, unit cost, discount, and tax.
- **Stock Ingestion**: Automatically increases stock on receipt and creates supplier ledger payables.
- **Purchase Returns**: Process returns to vendors with automated stock reduction and supplier balance credit.
- **Supplier Ledgers**: Real-time transaction statements with running balances.

---

## 5. Sales Returns & Refunds
- **Invoice Search**: Lookup original invoice by invoice number.
- **Itemized Return**: Select individual items and quantities (validates that returned quantity cannot exceed original purchase).
- **Automated Reversals**: Increases inventory, decrements member loyalty points, updates shift cash drawer, and posts ledger adjustments.

---

## 6. Cash Management & Shifts
- **Shift Lifecycle**: Cashier opens shift with an opening cash float &rarr; processes cash sales, cash refunds, and petty expenses &rarr; closes shift with physical counted cash.
- **Reconciliation**: Automatically calculates expected drawer cash vs. actual cash count, flagging shortages or surpluses.

---

## 7. Financial Accounting & Ledger
- **General Ledger**: Unified financial ledger categorized by Customer, Supplier, Sales, Purchases, Expenses, and Cash.
- **Expense Tracking**: Category-wise operational overhead (Utilities, Rent, Salaries, Maintenance).
- **P&L Reporting**: Real-time Cost of Goods Sold (COGS) gross and net profit statements.

---

## 8. Reports & Business Intelligence
- **Sales Analytics**: Daily, cashier-wise, category-wise, and payment-method breakdowns.
- **Procurement Reports**: Supplier procurement volume and outstanding dues.
- **Inventory Valuation**: Current asset value at purchase cost vs. expected retail realization.
- **Audit Logs**: Activity audit trails with JSON change payloads and client IP tracking.
