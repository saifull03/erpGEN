<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\ExpenseCategory;
use App\Models\Member;
use App\Models\MembershipType;
use App\Models\Product;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        // 1. Users
        $superAdminRole = Role::query()->where('slug', 'super-admin')->first();
        $adminRole = Role::query()->where('slug', 'admin')->first();
        $cashierRole = Role::query()->where('slug', 'cashier')->first();
        $invRole = Role::query()->where('slug', 'inventory-manager')->first();
        $accRole = Role::query()->where('slug', 'accountant')->first();

        User::query()->updateOrCreate(
            ['email' => 'admin@onestop.local'],
            [
                'name' => 'OneStop Super Admin',
                'role_id' => $superAdminRole?->id,
                'phone' => '+8801700000001',
                'status' => 'active',
                'password' => bcrypt('password'),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'manager@onestop.local'],
            [
                'name' => 'Store Manager',
                'role_id' => $adminRole?->id,
                'phone' => '+8801700000002',
                'status' => 'active',
                'password' => bcrypt('password'),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'cashier@onestop.local'],
            [
                'name' => 'Sadia Cashier',
                'role_id' => $cashierRole?->id,
                'phone' => '+8801700000003',
                'status' => 'active',
                'password' => bcrypt('password'),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'inventory@onestop.local'],
            [
                'name' => 'Rahim Inventory',
                'role_id' => $invRole?->id,
                'phone' => '+8801700000004',
                'status' => 'active',
                'password' => bcrypt('password'),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'accountant@onestop.local'],
            [
                'name' => 'Kamal Accountant',
                'role_id' => $accRole?->id,
                'phone' => '+8801700000005',
                'status' => 'active',
                'password' => bcrypt('password'),
            ]
        );

        // 2. Units
        $units = [
            ['name' => 'Piece', 'short_name' => 'pcs'],
            ['name' => 'Kilogram', 'short_name' => 'kg'],
            ['name' => 'Gram', 'short_name' => 'g'],
            ['name' => 'Liter', 'short_name' => 'L'],
            ['name' => 'Bottle', 'short_name' => 'btl'],
            ['name' => 'Packet', 'short_name' => 'pkt'],
            ['name' => 'Box', 'short_name' => 'box'],
            ['name' => 'Dozen', 'short_name' => 'dzn'],
        ];
        foreach ($units as $u) {
            Unit::query()->updateOrCreate(['name' => $u['name']], $u);
        }

        // 3. Brands
        $brands = [
            ['name' => 'Nestle', 'slug' => 'nestle'],
            ['name' => 'Unilever', 'slug' => 'unilever'],
            ['name' => 'Pran', 'slug' => 'pran'],
            ['name' => 'Aarong Dairy', 'slug' => 'aarong-dairy'],
            ['name' => 'Radhuni', 'slug' => 'radhuni'],
            ['name' => 'Fresh', 'slug' => 'fresh'],
            ['name' => 'Square Consumer', 'slug' => 'square-consumer'],
            ['name' => 'Teer', 'slug' => 'teer'],
        ];
        foreach ($brands as $b) {
            Brand::query()->updateOrCreate(['slug' => $b['slug']], $b);
        }

        // 4. Categories
        $categories = [
            ['name' => 'Beverages', 'slug' => 'beverages'],
            ['name' => 'Dairy & Eggs', 'slug' => 'dairy-eggs'],
            ['name' => 'Bakery & Snacks', 'slug' => 'bakery-snacks'],
            ['name' => 'Meat & Fish', 'slug' => 'meat-fish'],
            ['name' => 'Rice & Grains', 'slug' => 'rice-grains'],
            ['name' => 'Cooking Essentials & Spices', 'slug' => 'cooking-essentials'],
            ['name' => 'Household & Cleaning', 'slug' => 'household-cleaning'],
            ['name' => 'Personal Care', 'slug' => 'personal-care'],
        ];
        foreach ($categories as $c) {
            Category::query()->updateOrCreate(['slug' => $c['slug']], $c);
        }

        // 5. Products
        $catBeverage = Category::query()->where('slug', 'beverages')->first();
        $catDairy = Category::query()->where('slug', 'dairy-eggs')->first();
        $catRice = Category::query()->where('slug', 'rice-grains')->first();
        $catCooking = Category::query()->where('slug', 'cooking-essentials')->first();
        $catSnacks = Category::query()->where('slug', 'bakery-snacks')->first();

        $brandNestle = Brand::query()->where('slug', 'nestle')->first();
        $brandPran = Brand::query()->where('slug', 'pran')->first();
        $brandAarong = Brand::query()->where('slug', 'aarong-dairy')->first();
        $brandFresh = Brand::query()->where('slug', 'fresh')->first();
        $brandTeer = Brand::query()->where('slug', 'teer')->first();
        $brandRadhuni = Brand::query()->where('slug', 'radhuni')->first();

        $unitPcs = Unit::query()->where('name', 'Piece')->first();
        $unitKg = Unit::query()->where('name', 'Kilogram')->first();
        $unitL = Unit::query()->where('name', 'Liter')->first();
        $unitPkt = Unit::query()->where('name', 'Packet')->first();

        $products = [
            [
                'sku' => 'PRD-0001',
                'barcode' => '890103000001',
                'name' => 'Nescafe Classic Instant Coffee 100g Jar',
                'slug' => 'nescafe-classic-100g',
                'category_id' => $catBeverage?->id,
                'brand_id' => $brandNestle?->id,
                'unit_id' => $unitPcs?->id,
                'purchase_price' => 380.00,
                'selling_price' => 450.00,
                'wholesale_price' => 420.00,
                'discount_price' => 440.00,
                'minimum_stock' => 5,
                'current_stock' => 45,
                'expiry_date' => now()->addMonths(12)->toDateString(),
                'status' => 'active',
                'description' => 'Premium 100% pure instant coffee from Nestle.',
            ],
            [
                'sku' => 'PRD-0002',
                'barcode' => '890103000002',
                'name' => 'Aarong Pasteurized Full Cream Milk 1L',
                'slug' => 'aarong-milk-1l',
                'category_id' => $catDairy?->id,
                'brand_id' => $brandAarong?->id,
                'unit_id' => $unitL?->id,
                'purchase_price' => 85.00,
                'selling_price' => 95.00,
                'wholesale_price' => 90.00,
                'discount_price' => 95.00,
                'minimum_stock' => 10,
                'current_stock' => 80,
                'expiry_date' => now()->addDays(5)->toDateString(),
                'status' => 'active',
                'description' => 'Fresh pasteurized full cream liquid milk.',
            ],
            [
                'sku' => 'PRD-0003',
                'barcode' => '890103000003',
                'name' => 'Teer Fortified Soybean Oil 5L Can',
                'slug' => 'teer-soybean-oil-5l',
                'category_id' => $catCooking?->id,
                'brand_id' => $brandTeer?->id,
                'unit_id' => $unitL?->id,
                'purchase_price' => 820.00,
                'selling_price' => 890.00,
                'wholesale_price' => 860.00,
                'discount_price' => 880.00,
                'minimum_stock' => 8,
                'current_stock' => 35,
                'expiry_date' => now()->addMonths(9)->toDateString(),
                'status' => 'active',
                'description' => '100% pure refined vitamin-enriched soybean oil.',
            ],
            [
                'sku' => 'PRD-0004',
                'barcode' => '890103000004',
                'name' => 'Fresh Miniket Premium Rice 5kg Bag',
                'slug' => 'fresh-miniket-rice-5kg',
                'category_id' => $catRice?->id,
                'brand_id' => $brandFresh?->id,
                'unit_id' => $unitKg?->id,
                'purchase_price' => 370.00,
                'selling_price' => 420.00,
                'wholesale_price' => 400.00,
                'discount_price' => 415.00,
                'minimum_stock' => 10,
                'current_stock' => 50,
                'expiry_date' => now()->addMonths(18)->toDateString(),
                'status' => 'active',
                'description' => 'Clean, sorted premium Miniket aromatic white rice.',
            ],
            [
                'sku' => 'PRD-0005',
                'barcode' => '890103000005',
                'name' => 'Radhuni Pure Turmeric Powder 200g Pouch',
                'slug' => 'radhuni-turmeric-200g',
                'category_id' => $catCooking?->id,
                'brand_id' => $brandRadhuni?->id,
                'unit_id' => $unitPkt?->id,
                'purchase_price' => 80.00,
                'selling_price' => 95.00,
                'wholesale_price' => 88.00,
                'discount_price' => 92.00,
                'minimum_stock' => 15,
                'current_stock' => 60,
                'expiry_date' => now()->addMonths(14)->toDateString(),
                'status' => 'active',
                'description' => 'Finely grounded natural yellow turmeric.',
            ],
            [
                'sku' => 'PRD-0006',
                'barcode' => '890103000006',
                'name' => 'Pran Frooto Mango Juice Drink 1L Bottle',
                'slug' => 'pran-frooto-mango-1l',
                'category_id' => $catBeverage?->id,
                'brand_id' => $brandPran?->id,
                'unit_id' => $unitL?->id,
                'purchase_price' => 90.00,
                'selling_price' => 110.00,
                'wholesale_price' => 100.00,
                'discount_price' => 110.00,
                'minimum_stock' => 12,
                'current_stock' => 4, // low stock test
                'expiry_date' => now()->addMonths(6)->toDateString(),
                'status' => 'active',
                'description' => 'Refreshing mango fruit drink.',
            ],
        ];

        foreach ($products as $prd) {
            Product::query()->updateOrCreate(['sku' => $prd['sku']], $prd);
        }

        // 6. Suppliers
        $suppliers = [
            [
                'supplier_id' => 'SUP-0001',
                'company_name' => 'Meghna Group & Teer Distribution',
                'contact_person' => 'Md. Faruq Ahmed',
                'phone' => '+8801819000001',
                'email' => 'distribution@meghnagroup.com',
                'address' => 'Tejgaon Industrial Area, Dhaka',
                'opening_balance' => 0,
                'current_payable_balance' => 12500.00,
                'status' => 'active',
            ],
            [
                'supplier_id' => 'SUP-0002',
                'company_name' => 'Pran-RFL Super Distribution',
                'contact_person' => 'Tanvir Hasan',
                'phone' => '+8801819000002',
                'email' => 'sales@pranfoods.net',
                'address' => 'Middle Badda, Dhaka-1212',
                'opening_balance' => 0,
                'current_payable_balance' => 8400.00,
                'status' => 'active',
            ],
        ];
        foreach ($suppliers as $s) {
            Supplier::query()->updateOrCreate(['supplier_id' => $s['supplier_id']], $s);
        }

        // 7. Customers
        $customers = [
            [
                'customer_id' => 'CUS-1001',
                'name' => 'Arif Chowdhury',
                'phone' => '+8801711000001',
                'email' => 'arif.chowdhury@gmail.com',
                'address' => 'Banani, Dhaka',
                'date_of_birth' => '1988-04-12',
                'opening_balance' => 0,
                'current_balance' => 0,
                'status' => 'active',
            ],
            [
                'customer_id' => 'CUS-1002',
                'name' => 'Nusrat Jahan',
                'phone' => '+8801711000002',
                'email' => 'nusrat.jahan@yahoo.com',
                'address' => 'Uttara Sector 4, Dhaka',
                'date_of_birth' => '1992-09-25',
                'opening_balance' => 0,
                'current_balance' => 0,
                'status' => 'active',
            ],
        ];
        foreach ($customers as $c) {
            Customer::query()->updateOrCreate(['customer_id' => $c['customer_id']], $c);
        }

        // 8. Membership Types
        $membershipTypes = [
            [
                'name' => 'Standard',
                'discount_percentage' => 0,
                'reward_points' => 1,
                'minimum_purchase' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Silver',
                'discount_percentage' => 5,
                'reward_points' => 2,
                'minimum_purchase' => 500,
                'is_active' => true,
            ],
            [
                'name' => 'Gold',
                'discount_percentage' => 10,
                'reward_points' => 5,
                'minimum_purchase' => 1000,
                'is_active' => true,
            ],
            [
                'name' => 'Platinum',
                'discount_percentage' => 15,
                'reward_points' => 10,
                'minimum_purchase' => 2500,
                'is_active' => true,
            ],
        ];
        foreach ($membershipTypes as $mt) {
            MembershipType::query()->updateOrCreate(['name' => $mt['name']], $mt);
        }

        // 9. Members
        $goldTier = MembershipType::query()->where('name', 'Gold')->first();
        $silverTier = MembershipType::query()->where('name', 'Silver')->first();
        $cust1 = Customer::query()->where('customer_id', 'CUS-1001')->first();
        $cust2 = Customer::query()->where('customer_id', 'CUS-1002')->first();

        if ($cust1 && $goldTier) {
            Member::query()->updateOrCreate(
                ['member_number' => 'M-1001'],
                [
                    'membership_id' => 'MEM-2026-0001',
                    'customer_id' => $cust1->id,
                    'membership_type_id' => $goldTier->id,
                    'name' => $cust1->name,
                    'phone' => $cust1->phone,
                    'email' => $cust1->email,
                    'address' => $cust1->address,
                    'date_of_birth' => $cust1->date_of_birth,
                    'join_date' => now()->subMonths(6)->toDateString(),
                    'expiry_date' => now()->addMonths(6)->toDateString(),
                    'points' => 280,
                    'total_purchase_amount' => 15400.00,
                    'total_transactions' => 14,
                    'status' => 'active',
                ]
            );
        }

        if ($cust2 && $silverTier) {
            Member::query()->updateOrCreate(
                ['member_number' => 'M-1002'],
                [
                    'membership_id' => 'MEM-2026-0002',
                    'customer_id' => $cust2->id,
                    'membership_type_id' => $silverTier->id,
                    'name' => $cust2->name,
                    'phone' => $cust2->phone,
                    'email' => $cust2->email,
                    'address' => $cust2->address,
                    'date_of_birth' => $cust2->date_of_birth,
                    'join_date' => now()->subMonths(2)->toDateString(),
                    'expiry_date' => now()->addMonths(10)->toDateString(),
                    'points' => 95,
                    'total_purchase_amount' => 4750.00,
                    'total_transactions' => 5,
                    'status' => 'active',
                ]
            );
        }

        // 10. Expense Categories
        $expCategories = [
            ['name' => 'Electricity Bill', 'slug' => 'electricity', 'description' => 'Monthly utility power bills'],
            ['name' => 'Shop Rent', 'slug' => 'rent', 'description' => 'Commercial floor premises rental'],
            ['name' => 'Staff Salary', 'slug' => 'salary', 'description' => 'Monthly staff wages'],
            ['name' => 'Transport & Freight', 'slug' => 'transport', 'description' => 'Goods shipment transport'],
            ['name' => 'Maintenance & Repairs', 'slug' => 'maintenance', 'description' => 'AC and equipment servicing'],
            ['name' => 'Packaging & Bags', 'slug' => 'packaging', 'description' => 'Shopping bags and labels'],
        ];
        foreach ($expCategories as $ec) {
            ExpenseCategory::query()->updateOrCreate(['slug' => $ec['slug']], $ec);
        }

        // 11. Settings
        $settings = [
            'store_name' => 'OneStop Supermarket',
            'store_logo' => '',
            'currency_symbol' => '৳',
            'currency_code' => 'BDT',
            'store_address' => 'House 12, Road 4, Dhanmondi, Dhaka-1205, Bangladesh',
            'store_phone' => '+880 1700-000000',
            'store_email' => 'contact@onestop.local',
            'invoice_prefix' => 'INV-',
            'tax_rate' => '0',
            'loyalty_points_per_hundred' => '1',
            'low_stock_threshold' => '5',
            'receipt_footer' => 'Thank you for shopping at OneStop Supermarket! Please visit again.',
        ];
        foreach ($settings as $key => $val) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $val]);
        }
    }
}
