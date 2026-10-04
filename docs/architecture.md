# OneStop POS + ERP Architecture

OneStop is engineered with a clean, decoupled MVC + Service Layer architecture designed for transactional robustness, inventory accuracy, and low-latency POS throughput.

---

## High-Level Architecture Diagram

```
[ Browser / POS Barcode Scanner / Keyboard Shortcuts ]
                         │
                         ▼
             [ Laravel HTTP Middleware ]
             (Auth, CheckRole, CSRF)
                         │
                         ▼
              [ Thin Controllers ]
    (POS, Sale, Product, Purchase, Inventory,
     Customer, Member, Shift, Expense, Report)
                         │
                         ▼
           [ Dedicated Service Layer ]
  ┌─────────────────────────────────────────────────┐
  │ • SaleService        • PurchaseService          │
  │ • ReturnService      • InventoryService         │
  │ • ShiftService       • LedgerService            │
  │ • ReportService      • AuditService             │
  └─────────────────────────────────────────────────┘
                         │ (DB::transaction & Lock)
                         ▼
                 [ Eloquent Models ]
                         │
                         ▼
                  [ MySQL Engine ]
```

---

## Architectural Principles

### 1. Thin Controllers, Rich Services
Controllers in OneStop do not contain business logic or raw database mutators. They validate incoming request payloads and dispatch to specialized service classes in `app/Services/`.

### 2. Database Transactions & Pessimistic Locking
High-concurrency POS checkouts utilize `DB::transaction()` and pessimistic locking (`lockForUpdate()`) on product stock to ensure that simultaneous cashier barcode scans never sell phantom or oversold inventory.

### 3. Immutable Stock Movement Trail
Stock counts are **never** silently updated. Every addition or deduction generates an immutable row in `stock_movements` with:
- Previous Stock
- Quantity Delta
- New Stock
- Movement Reason (Sale, Purchase, Return, Adjustment, Damage, Expiry)
- User & Timestamp

### 4. Double-Entry Running Ledger
The `LedgerService` automatically posts double-entry transaction records to the `ledger_entries` table for:
- Sales receipts & dues
- Supplier procurement & settlements
- Store overhead expenses
- Shift cash drawer inflows/outflows

### 5. Multi-Role Authorization
Middleware `CheckRole` validates route access against 5 enterprise supermarket roles:
- **Super Admin**: Full unrestricted access
- **Admin / Store Manager**: Catalog, purchases, suppliers, sales, reports, expenses, employees
- **Cashier**: POS terminal, barcode scan, customer lookup, member discount, payments, returns, active shift cash
- **Inventory Manager**: Stock adjustments, purchase receiving, units/brands/categories, stock valuation & alerts
- **Accountant**: General ledger, sales/purchase/expense reports, profit & loss, vendor & customer dues
