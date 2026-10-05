<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Categories
        $categoriesData = [
            ['name' => 'Beverages', 'slug' => 'beverages'],
            ['name' => 'Dairy & Eggs', 'slug' => 'dairy-eggs'],
            ['name' => 'Bakery & Snacks', 'slug' => 'bakery-snacks'],
            ['name' => 'Meat & Fish', 'slug' => 'meat-fish'],
            ['name' => 'Rice & Grains', 'slug' => 'rice-grains'],
            ['name' => 'Cooking Essentials & Spices', 'slug' => 'cooking-essentials'],
            ['name' => 'Household & Cleaning', 'slug' => 'household-cleaning'],
            ['name' => 'Personal Care', 'slug' => 'personal-care'],
            ['name' => 'Fresh Produce & Fruits', 'slug' => 'fresh-produce'],
            ['name' => 'Frozen & Instant Foods', 'slug' => 'frozen-foods'],
        ];

        $categories = [];
        foreach ($categoriesData as $c) {
            $categories[$c['slug']] = Category::query()->updateOrCreate(['slug' => $c['slug']], $c);
        }

        // 2. Ensure Brands
        $brandsData = [
            ['name' => 'Nestle', 'slug' => 'nestle'],
            ['name' => 'Unilever', 'slug' => 'unilever'],
            ['name' => 'Pran', 'slug' => 'pran'],
            ['name' => 'Aarong Dairy', 'slug' => 'aarong-dairy'],
            ['name' => 'Radhuni', 'slug' => 'radhuni'],
            ['name' => 'Fresh', 'slug' => 'fresh'],
            ['name' => 'Square Consumer', 'slug' => 'square-consumer'],
            ['name' => 'Teer', 'slug' => 'teer'],
            ['name' => 'ACI Pure', 'slug' => 'aci-pure'],
            ['name' => 'Kazi Farms', 'slug' => 'kazi-farms'],
            ['name' => 'Golden Harvest', 'slug' => 'golden-harvest'],
            ['name' => 'Marico', 'slug' => 'marico'],
            ['name' => 'Ispahani', 'slug' => 'ispahani'],
            ['name' => 'Akij Food & Beverage', 'slug' => 'akij-food'],
            ['name' => 'Bombay Sweets', 'slug' => 'bombay-sweets'],
            ['name' => 'Reckitt Benckiser', 'slug' => 'reckitt-benckiser'],
            ['name' => 'Colgate-Palmolive', 'slug' => 'colgate-palmolive'],
            ['name' => 'General Supermarket', 'slug' => 'general-supermarket'],
        ];

        $brands = [];
        foreach ($brandsData as $b) {
            $brands[$b['slug']] = Brand::query()->updateOrCreate(['slug' => $b['slug']], $b);
        }

        // 3. Ensure Units
        $unitsData = [
            ['name' => 'Piece', 'short_name' => 'pcs'],
            ['name' => 'Kilogram', 'short_name' => 'kg'],
            ['name' => 'Gram', 'short_name' => 'g'],
            ['name' => 'Liter', 'short_name' => 'L'],
            ['name' => 'Bottle', 'short_name' => 'btl'],
            ['name' => 'Packet', 'short_name' => 'pkt'],
            ['name' => 'Box', 'short_name' => 'box'],
            ['name' => 'Dozen', 'short_name' => 'dzn'],
            ['name' => 'Can', 'short_name' => 'can'],
            ['name' => 'Roll', 'short_name' => 'roll'],
        ];

        $units = [];
        foreach ($unitsData as $u) {
            $units[$u['short_name']] = Unit::query()->updateOrCreate(['name' => $u['name']], $u);
        }

        // 4. Item Catalog Generator Templates
        $catalogTemplates = [
            'beverages' => [
                'cat' => 'beverages',
                'items' => [
                    ['Nescafe Classic Instant Coffee', ['50g Jar' => [200, 240, 'pcs', 'nestle'], '100g Jar' => [380, 450, 'pcs', 'nestle'], '200g Jar' => [720, 840, 'pcs', 'nestle'], '50g Refill Pouch' => [180, 215, 'pkt', 'nestle']]],
                    ['Nescafe Gold Rich Aroma Coffee', ['100g Glass Jar' => [650, 780, 'pcs', 'nestle'], '200g Glass Jar' => [1200, 1450, 'pcs', 'nestle']]],
                    ['Nescafe 3-in-1 Creamy Latte', ['Pack of 12' => [160, 195, 'box', 'nestle'], 'Pack of 24' => [320, 380, 'box', 'nestle'], 'Single Sachet 20g' => [15, 18, 'pcs', 'nestle']]],
                    ['Brooke Bond Taaza Black Tea', ['200g Pouch' => [110, 130, 'pkt', 'unilever'], '400g Pouch' => [210, 240, 'pkt', 'unilever'], '500g Jar' => [280, 320, 'pcs', 'unilever'], '1kg Family Pack' => [520, 590, 'pkt', 'unilever']]],
                    ['Ispahani Mirzapore Best Leaf Tea', ['200g Pack' => [115, 135, 'pkt', 'ispahani'], '400g Pack' => [220, 250, 'pkt', 'ispahani'], '500g Pack' => [275, 310, 'pkt', 'ispahani'], '100 Tea Bags Box' => [190, 230, 'box', 'ispahani']]],
                    ['Pran Frooto Mango Juice', ['250ml Bottle' => [25, 30, 'btl', 'pran'], '500ml Bottle' => [45, 55, 'btl', 'pran'], '1L Bottle' => [90, 110, 'btl', 'pran'], '2L Family Bottle' => [170, 205, 'btl', 'pran']]],
                    ['Pran Junior Apple Juice Drink', ['200ml Tetra Pack' => [22, 28, 'pkt', 'pran'], '1L Tetra Pack' => [95, 120, 'pkt', 'pran']]],
                    ['Akij Mojo Cola Drink', ['250ml Can' => [30, 35, 'can', 'akij-food'], '500ml Pet Bottle' => [35, 40, 'btl', 'akij-food'], '1L Pet Bottle' => [65, 75, 'btl', 'akij-food'], '2L Family Pet' => [110, 130, 'btl', 'akij-food']]],
                    ['Akij Lemu Clear Lemon Soda', ['250ml Can' => [28, 35, 'can', 'akij-food'], '500ml Bottle' => [32, 40, 'btl', 'akij-food'], '1L Bottle' => [62, 75, 'btl', 'akij-food']]],
                    ['Akij Speed Energy Drink', ['250ml Can' => [32, 40, 'can', 'akij-food'], '250ml Pet Bottle' => [28, 35, 'btl', 'akij-food']]],
                    ['Fresh Drinking Mineral Water', ['500ml Bottle' => [12, 16, 'btl', 'fresh'], '1L Bottle' => [20, 25, 'btl', 'fresh'], '2L Bottle' => [30, 38, 'btl', 'fresh'], '5L Jar' => [65, 80, 'btl', 'fresh']]],
                    ['Pran Drinking Natural Mineral Water', ['500ml Bottle' => [12, 16, 'btl', 'pran'], '1.5L Bottle' => [25, 32, 'btl', 'pran'], '5L Can' => [65, 80, 'btl', 'pran']]],
                    ['Tang Instant Drink Powder Orange', ['500g Pouch' => [280, 330, 'pkt', 'general-supermarket'], '1kg Container' => [540, 640, 'box', 'general-supermarket'], '2kg Jar' => [1050, 1220, 'box', 'general-supermarket']]],
                    ['Tang Instant Drink Powder Mango', ['500g Pouch' => [280, 330, 'pkt', 'general-supermarket'], '1kg Container' => [540, 640, 'box', 'general-supermarket']]],
                    ['Tang Instant Drink Powder Lemon', ['500g Pouch' => [280, 330, 'pkt', 'general-supermarket'], '1kg Container' => [540, 640, 'box', 'general-supermarket']]],
                    ['Horlicks Classic Malt Health Drink', ['500g Jar' => [370, 430, 'pcs', 'unilever'], '1kg Jar' => [710, 810, 'pcs', 'unilever'], '500g Refill Pack' => [340, 395, 'pkt', 'unilever']]],
                    ['Horlicks Chocolate Delight Health Drink', ['500g Jar' => [385, 450, 'pcs', 'unilever'], '1kg Jar' => [730, 840, 'pcs', 'unilever']]],
                    ['Milo Active Go Chocolate Malt Powder', ['400g Tin' => [360, 420, 'can', 'nestle'], '1kg Pouch' => [780, 900, 'pkt', 'nestle']]],
                    ['Nestle Coffee Mate Creamer', ['200g Jar' => [230, 275, 'pcs', 'nestle'], '450g Jar' => [480, 560, 'pcs', 'nestle'], '1kg Pouch' => [920, 1080, 'pkt', 'nestle']]],
                    ['Pran Lychee Drink Nectar', ['170ml Cup' => [15, 20, 'pcs', 'pran'], '1L Tetra Pack' => [90, 110, 'pkt', 'pran']]],
                ],
            ],
            'dairy_eggs' => [
                'cat' => 'dairy-eggs',
                'items' => [
                    ['Aarong Pasteurized Full Cream Milk', ['500ml Poly Pack' => [45, 50, 'pkt', 'aarong-dairy'], '1L Poly Pack' => [85, 95, 'L', 'aarong-dairy'], '1L Bottle' => [90, 100, 'btl', 'aarong-dairy']]],
                    ['Aarong Toned Low Fat Milk', ['500ml Pack' => [44, 48, 'pkt', 'aarong-dairy'], '1L Pack' => [82, 92, 'L', 'aarong-dairy']]],
                    ['Pran Dairy UHT Long Life Full Cream Milk', ['500ml Tetra Pack' => [48, 55, 'pkt', 'pran'], '1L Tetra Pack' => [92, 105, 'L', 'pran']]],
                    ['Aarong Salted Table Butter', ['100g Foil Pack' => [105, 125, 'pcs', 'aarong-dairy'], '200g Foil Pack' => [195, 230, 'pcs', 'aarong-dairy'], '500g Brick' => [480, 550, 'pcs', 'aarong-dairy']]],
                    ['Aarong Unsalted Pure Butter', ['200g Pack' => [200, 235, 'pcs', 'aarong-dairy'], '500g Pack' => [490, 560, 'pcs', 'aarong-dairy']]],
                    ['Aarong Pure Premium Ghee (Clarified Butter)', ['200g Glass Jar' => [320, 380, 'pcs', 'aarong-dairy'], '400g Glass Jar' => [620, 720, 'pcs', 'aarong-dairy'], '900g Tin Can' => [1350, 1550, 'can', 'aarong-dairy']]],
                    ['Pran Premium Pure Cow Milk Ghee', ['200g Jar' => [310, 365, 'pcs', 'pran'], '400g Jar' => [600, 695, 'pcs', 'pran'], '900g Jar' => [1300, 1490, 'pcs', 'pran']]],
                    ['Aarong Fresh Paneer (Cottage Cheese)', ['250g Block' => [180, 215, 'pcs', 'aarong-dairy'], '500g Block' => [350, 410, 'pcs', 'aarong-dairy']]],
                    ['Aarong Natural Sweet Curd (Misti Doi)', ['500g Clay Pot' => [140, 170, 'pcs', 'aarong-dairy'], '1kg Pot' => [260, 310, 'pcs', 'aarong-dairy']]],
                    ['Aarong Sour Curd (Tok Doi)', ['500g Plastic Cup' => [100, 120, 'pcs', 'aarong-dairy'], '1kg Plastic Tub' => [190, 230, 'pcs', 'aarong-dairy']]],
                    ['Nestle Everyday Dairy Whitener Milk Powder', ['200g Pouch' => [180, 210, 'pkt', 'nestle'], '500g Pouch' => [420, 470, 'pkt', 'nestle'], '1kg Pouch' => [820, 920, 'pkt', 'nestle']]],
                    ['Fresh Instant Full Cream Milk Powder', ['400g Pouch' => [360, 410, 'pkt', 'fresh'], '1kg Pouch' => [810, 905, 'pkt', 'fresh']]],
                    ['Aarong Dairy Laban Probiotic Drink', ['250ml Bottle' => [35, 42, 'btl', 'aarong-dairy'], '500ml Bottle' => [65, 78, 'btl', 'aarong-dairy']]],
                    ['Farm Fresh Grade-A Brown Eggs', ['6 Pcs Egg Carton' => [70, 80, 'box', 'fresh'], '12 Pcs (1 Dozen) Carton' => [135, 155, 'dzn', 'fresh'], '30 Pcs Egg Tray' => [330, 375, 'box', 'fresh']]],
                    ['Kazi Farms Organic Free Range Eggs', ['6 Pcs Box' => [85, 98, 'box', 'kazi-farms'], '12 Pcs Box' => [165, 190, 'dzn', 'kazi-farms']]],
                    ['Nestle Milkmaid Sweetened Condensed Milk', ['390g Tin Can' => [165, 195, 'can', 'nestle']]],
                    ['Fresh Sweetened Condensed Milk', ['390g Tin' => [145, 170, 'can', 'fresh']]],
                    ['Danish Sweetened Condensed Milk', ['397g Tin' => [140, 165, 'can', 'general-supermarket']]],
                ],
            ],
            'rice_grains' => [
                'cat' => 'rice-grains',
                'items' => [
                    ['Fresh Premium Miniket Rice', ['5kg Bag' => [370, 420, 'kg', 'fresh'], '10kg Bag' => [730, 830, 'kg', 'fresh'], '25kg Sack' => [1800, 2050, 'kg', 'fresh'], '50kg Sack' => [3550, 3990, 'kg', 'fresh']]],
                    ['Fresh Premium Nazirshail Rice', ['5kg Bag' => [390, 445, 'kg', 'fresh'], '10kg Bag' => [760, 870, 'kg', 'fresh'], '25kg Sack' => [1880, 2120, 'kg', 'fresh']]],
                    ['Fresh Premium Katari Bhog Rice', ['5kg Bag' => [410, 465, 'kg', 'fresh'], '10kg Bag' => [800, 910, 'kg', 'fresh']]],
                    ['Pran Premium Chinigura Aromatic Rice', ['1kg Pouch' => [135, 160, 'pkt', 'pran'], '2kg Pouch' => [265, 310, 'pkt', 'pran'], '5kg Bag' => [650, 750, 'kg', 'pran']]],
                    ['ACI Pure Chinigura Aromatic Polao Rice', ['1kg Pack' => [140, 165, 'pkt', 'aci-pure'], '2kg Pack' => [275, 320, 'pkt', 'aci-pure'], '5kg Pack' => [670, 770, 'kg', 'aci-pure']]],
                    ['Fortune Biryani Special Basmati Rice', ['1kg Bag' => [290, 340, 'pkt', 'general-supermarket'], '5kg Bag' => [1400, 1650, 'kg', 'general-supermarket']]],
                    ['Daawat Everyday Long Grain Basmati Rice', ['1kg Pouch' => [230, 275, 'pkt', 'general-supermarket'], '5kg Bag' => [1120, 1320, 'kg', 'general-supermarket']]],
                    ['Teer Fortified Whole Wheat Atta', ['1kg Poly Pack' => [58, 68, 'pkt', 'teer'], '2kg Poly Pack' => [110, 130, 'pkt', 'teer'], '5kg Bag' => [270, 315, 'kg', 'teer']]],
                    ['Fresh Fortified Whole Wheat Atta', ['1kg Pack' => [58, 68, 'pkt', 'fresh'], '2kg Pack' => [110, 130, 'pkt', 'fresh'], '5kg Pack' => [270, 315, 'kg', 'fresh']]],
                    ['ACI Pure Chakki Fresh Atta', ['1kg Pack' => [59, 70, 'pkt', 'aci-pure'], '2kg Pack' => [112, 132, 'pkt', 'aci-pure']]],
                    ['Teer Premium All Purpose Flour (Maida)', ['1kg Pack' => [65, 75, 'pkt', 'teer'], '2kg Pack' => [125, 145, 'pkt', 'teer'], '5kg Bag' => [305, 355, 'kg', 'teer']]],
                    ['Fresh Premium Refined Maida', ['1kg Pack' => [65, 75, 'pkt', 'fresh'], '2kg Pack' => [125, 145, 'pkt', 'fresh']]],
                    ['Fresh Pure Semolina (Suji)', ['500g Packet' => [42, 50, 'pkt', 'fresh'], '1kg Packet' => [80, 95, 'pkt', 'fresh']]],
                    ['Teer Pure Semolina (Suji)', ['500g Packet' => [42, 50, 'pkt', 'teer'], '1kg Packet' => [80, 95, 'pkt', 'teer']]],
                    ['Fresh Red Lentils (Deshi Masoor Dal)', ['500g Pack' => [72, 85, 'pkt', 'fresh'], '1kg Pack' => [140, 165, 'pkt', 'fresh'], '2kg Pack' => [275, 320, 'pkt', 'fresh']]],
                    ['ACI Pure Premium Masoor Dal', ['1kg Pack' => [145, 170, 'pkt', 'aci-pure'], '2kg Pack' => [285, 330, 'pkt', 'aci-pure']]],
                    ['Fresh Mung Dal (Yellow Moong)', ['500g Pack' => [85, 100, 'pkt', 'fresh'], '1kg Pack' => [165, 195, 'pkt', 'fresh']]],
                    ['Fresh Chana Dal (Split Chickpeas)', ['500g Pack' => [62, 75, 'pkt', 'fresh'], '1kg Pack' => [120, 145, 'pkt', 'fresh']]],
                    ['Fresh Chickpeas Whole (Chola Boot)', ['500g Pack' => [60, 72, 'pkt', 'fresh'], '1kg Pack' => [115, 140, 'pkt', 'fresh']]],
                    ['Fresh Green Peas Dried (Motor Dal)', ['500g Pack' => [48, 58, 'pkt', 'fresh'], '1kg Pack' => [92, 112, 'pkt', 'fresh']]],
                    ['Quaker Rolled White Oats', ['500g Jar' => [240, 285, 'pcs', 'general-supermarket'], '1kg Jar' => [460, 540, 'pcs', 'general-supermarket'], '800g Refill Pouch' => [360, 425, 'pkt', 'general-supermarket']]],
                    ['Kelloggs Whole Corn Flakes', ['250g Box' => [190, 230, 'box', 'general-supermarket'], '475g Box' => [350, 415, 'box', 'general-supermarket'], '875g Family Box' => [620, 720, 'box', 'general-supermarket']]],
                ],
            ],
            'cooking_essentials' => [
                'cat' => 'cooking-essentials',
                'items' => [
                    ['Teer Fortified Refined Soybean Oil', ['1L Bottle' => [170, 185, 'L', 'teer'], '2L Bottle' => [335, 365, 'L', 'teer'], '3L Bottle' => [500, 545, 'L', 'teer'], '5L Can' => [820, 890, 'L', 'teer']]],
                    ['Fresh Fortified Pure Soybean Oil', ['1L Bottle' => [170, 185, 'L', 'fresh'], '2L Bottle' => [335, 365, 'L', 'fresh'], '5L Can' => [820, 890, 'L', 'fresh']]],
                    ['Rupchanda Fortified Soybean Oil', ['1L Bottle' => [172, 188, 'L', 'square-consumer'], '2L Bottle' => [340, 370, 'L', 'square-consumer'], '5L Can' => [825, 895, 'L', 'square-consumer']]],
                    ['Radhuni Pure Mustard Oil (Kachi Ghani)', ['250ml Bottle' => [85, 100, 'btl', 'radhuni'], '500ml Bottle' => [165, 190, 'btl', 'radhuni'], '1L Bottle' => [310, 360, 'btl', 'radhuni'], '2L Bottle' => [610, 700, 'btl', 'radhuni']]],
                    ['Teer Pure Mustard Oil', ['250ml Bottle' => [82, 95, 'btl', 'teer'], '500ml Bottle' => [160, 185, 'btl', 'teer'], '1L Bottle' => [305, 350, 'btl', 'teer']]],
                    ['Fresh Pure Sunflower Cooking Oil', ['1L Bottle' => [260, 310, 'L', 'fresh'], '2L Bottle' => [510, 600, 'L', 'fresh'], '5L Can' => [1250, 1450, 'L', 'fresh']]],
                    ['Radhuni Pure Turmeric Powder (Haldi)', ['100g Pouch' => [42, 50, 'pkt', 'radhuni'], '200g Pouch' => [80, 95, 'pkt', 'radhuni'], '500g Jar' => [195, 230, 'pcs', 'radhuni'], '1kg Pouch' => [380, 440, 'pkt', 'radhuni']]],
                    ['Fresh Pure Turmeric Powder', ['100g Pouch' => [40, 48, 'pkt', 'fresh'], '200g Pouch' => [78, 92, 'pkt', 'fresh'], '500g Pouch' => [190, 220, 'pkt', 'fresh']]],
                    ['Radhuni Pure Red Chilli Powder', ['100g Pouch' => [58, 70, 'pkt', 'radhuni'], '200g Pouch' => [110, 130, 'pkt', 'radhuni'], '500g Jar' => [270, 315, 'pcs', 'radhuni']]],
                    ['Fresh Hot Red Chilli Powder', ['100g Pouch' => [55, 68, 'pkt', 'fresh'], '200g Pouch' => [108, 128, 'pkt', 'fresh']]],
                    ['Radhuni Pure Coriander Powder (Dhania)', ['100g Pouch' => [38, 45, 'pkt', 'radhuni'], '200g Pouch' => [72, 85, 'pkt', 'radhuni'], '500g Pouch' => [175, 205, 'pkt', 'radhuni']]],
                    ['Radhuni Pure Cumin Powder (Jeera)', ['100g Pouch' => [120, 145, 'pkt', 'radhuni'], '200g Pouch' => [235, 280, 'pkt', 'radhuni'], '500g Jar' => [580, 680, 'pcs', 'radhuni']]],
                    ['Radhuni Meat Curry Masala Mix', ['100g Pouch' => [65, 80, 'pkt', 'radhuni'], '200g Box' => [130, 155, 'box', 'radhuni']]],
                    ['Radhuni Biryani Masala Mix', ['40g Box' => [50, 60, 'box', 'radhuni'], '80g Box' => [95, 115, 'box', 'radhuni']]],
                    ['Radhuni Roast Masala Mix', ['35g Box' => [45, 55, 'box', 'radhuni'], '70g Box' => [85, 105, 'box', 'radhuni']]],
                    ['Radhuni Fish Curry Masala Mix', ['50g Box' => [40, 50, 'box', 'radhuni'], '100g Box' => [75, 90, 'box', 'radhuni']]],
                    ['Radhuni Haleem Masala Mix with Lentils', ['200g Box' => [75, 90, 'box', 'radhuni']]],
                    ['Radhuni Garam Masala Powder', ['50g Pouch' => [90, 110, 'pkt', 'radhuni'], '100g Jar' => [180, 215, 'pcs', 'radhuni']]],
                    ['Fresh Super Refined Cane Sugar', ['1kg Packet' => [125, 140, 'pkt', 'fresh'], '2kg Packet' => [245, 275, 'pkt', 'fresh'], '5kg Bag' => [610, 685, 'kg', 'fresh']]],
                    ['Teer Pure Crystal Sugar', ['1kg Packet' => [125, 140, 'pkt', 'teer'], '2kg Packet' => [245, 275, 'pkt', 'teer']]],
                    ['Fresh Vacuum Refined Pure Iodized Salt', ['1kg Packet' => [35, 45, 'pkt', 'fresh'], '2kg Packet' => [68, 85, 'pkt', 'fresh']]],
                    ['ACI Pure Super Vacuum Iodized Salt', ['1kg Packet' => [36, 45, 'pkt', 'aci-pure'], '2kg Packet' => [70, 88, 'pkt', 'aci-pure']]],
                    ['Pran Pure White Distilled Vinegar', ['500ml Bottle' => [45, 55, 'btl', 'pran'], '1L Bottle' => [80, 95, 'btl', 'pran']]],
                    ['Ahmed Pure Dark Soy Sauce', ['300ml Bottle' => [75, 90, 'btl', 'general-supermarket'], '650ml Bottle' => [145, 175, 'btl', 'general-supermarket']]],
                    ['Maggi Rich Tomato Ketchup', ['400g Bottle' => [115, 140, 'btl', 'nestle'], '1kg Spout Pouch' => [250, 295, 'pkt', 'nestle']]],
                    ['Pran Sweet & Sour Tomato Sauce', ['350g Bottle' => [80, 95, 'btl', 'pran'], '1kg Pouch' => [190, 230, 'pkt', 'pran']]],
                    ['Pran Hot & Spicy Green Chilli Sauce', ['330g Bottle' => [85, 105, 'btl', 'pran']]],
                ],
            ],
            'bakery_snacks' => [
                'cat' => 'bakery-snacks',
                'items' => [
                    ['Pran Potata Spicy Crunchy Biscuit', ['100g Packet' => [28, 35, 'pkt', 'pran'], 'Box of 12 Packets' => [320, 390, 'box', 'pran']]],
                    ['Pran All-Time Premium Milk Bread', ['400g Pack' => [50, 60, 'pkt', 'pran'], '600g Jumbo Pack' => [75, 90, 'pkt', 'pran']]],
                    ['Fresh Premium White Sandwich Bread', ['400g Pack' => [50, 60, 'pkt', 'fresh']]],
                    ['Pran All-Time Crispy Toast Rusk', ['350g Family Pack' => [60, 75, 'pkt', 'pran'], '150g Snack Pack' => [28, 35, 'pkt', 'pran']]],
                    ['Pran All-Time Dry Cake', ['200g Box' => [70, 85, 'box', 'pran'], '350g Box' => [115, 140, 'box', 'pran']]],
                    ['Square Ruchi Chanachur Hot & Spicy', ['150g Packet' => [40, 50, 'pkt', 'square-consumer'], '300g Packet' => [75, 90, 'pkt', 'square-consumer'], '500g Jar' => [135, 160, 'pcs', 'square-consumer']]],
                    ['Square Ruchi Chanachur BBQ Flavoured', ['150g Packet' => [42, 52, 'pkt', 'square-consumer'], '300g Packet' => [78, 95, 'pkt', 'square-consumer']]],
                    ['Square Ruchi Fried Moong Dal Snack', ['100g Packet' => [30, 38, 'pkt', 'square-consumer'], '200g Packet' => [58, 70, 'pkt', 'square-consumer']]],
                    ['Bombay Sweets Potato Crackers Original', ['22g Packet' => [12, 15, 'pkt', 'bombay-sweets'], '45g Packet' => [22, 28, 'pkt', 'bombay-sweets'], '100g Mega Pack' => [45, 55, 'pkt', 'bombay-sweets']]],
                    ['Bombay Sweets Ring Chips Tangy Tomato', ['25g Packet' => [12, 15, 'pkt', 'bombay-sweets'], '50g Packet' => [24, 30, 'pkt', 'bombay-sweets']]],
                    ['Bombay Sweets Mr. Twist Spicy Curl Chips', ['25g Packet' => [12, 15, 'pkt', 'bombay-sweets'], '60g Packet' => [28, 35, 'pkt', 'bombay-sweets']]],
                    ['Oreo Original Chocolate Vanilla Sandwich Cookies', ['120g Pack' => [45, 55, 'pkt', 'general-supermarket'], '240g Twin Pack' => [85, 105, 'pkt', 'general-supermarket'], 'Pack of 12 Mini' => [180, 220, 'box', 'general-supermarket']]],
                    ['Cadbury Dairy Milk Chocolate Bar', ['24g Bar' => [30, 35, 'pcs', 'general-supermarket'], '50g Bar' => [60, 70, 'pcs', 'general-supermarket'], '130g Silk Bar' => [190, 230, 'pcs', 'general-supermarket']]],
                    ['KitKat 4-Finger Crisp Wafer Bar', ['38g Bar' => [45, 55, 'pcs', 'nestle'], 'Pack of 6 Bars' => [250, 300, 'box', 'nestle']]],
                    ['Sunfeast Dark Fantasy Choco Fills', ['75g Box' => [42, 50, 'box', 'general-supermarket'], '300g Family Pack' => [160, 195, 'box', 'general-supermarket']]],
                    ['Britannia Little Hearts Sugar Glazed Biscuits', ['75g Pack' => [32, 40, 'pkt', 'general-supermarket'], '150g Pack' => [60, 75, 'pkt', 'general-supermarket']]],
                    ['Britannia Good Day Butter Cookies', ['100g Pack' => [35, 45, 'pkt', 'general-supermarket'], '200g Pack' => [68, 85, 'pkt', 'general-supermarket'], '600g Tin Box' => [280, 340, 'box', 'general-supermarket']]],
                    ['Britannia Good Day Cashew Cookies', ['100g Pack' => [38, 48, 'pkt', 'general-supermarket'], '200g Pack' => [72, 90, 'pkt', 'general-supermarket']]],
                    ['Pran Butter Salted Cookies', ['120g Pack' => [30, 40, 'pkt', 'pran'], '300g Tin' => [120, 150, 'box', 'pran']]],
                    ['Pran Energy Plus Glucose Biscuits', ['100g Pack' => [15, 20, 'pkt', 'pran'], '250g Family Pack' => [35, 45, 'pkt', 'pran']]],
                ],
            ],
            'household_cleaning' => [
                'cat' => 'household-cleaning',
                'items' => [
                    ['Surf Excel Easy Wash Detergent Powder', ['500g Packet' => [100, 120, 'pkt', 'unilever'], '1kg Packet' => [195, 230, 'pkt', 'unilever'], '2kg Bag with Bucket Free' => [380, 445, 'pkt', 'unilever'], '5kg Giant Bag' => [920, 1080, 'kg', 'unilever']]],
                    ['Surf Excel Matic Front Load Detergent Powder', ['1kg Box' => [290, 345, 'box', 'unilever'], '2kg Box' => [560, 660, 'box', 'unilever']]],
                    ['Surf Excel Matic Top Load Detergent Liquid', ['1L Bottle' => [260, 310, 'btl', 'unilever'], '2L Refill' => [480, 570, 'btl', 'unilever']]],
                    ['Rin Advanced Detergent Powder', ['500g Pack' => [70, 85, 'pkt', 'unilever'], '1kg Pack' => [135, 160, 'pkt', 'unilever'], '2kg Pack' => [260, 305, 'pkt', 'unilever']]],
                    ['Wheel 2-in-1 Clean & Green Detergent Powder', ['500g Pack' => [50, 62, 'pkt', 'unilever'], '1kg Pack' => [95, 115, 'pkt', 'unilever']]],
                    ['Square Super White Detergent Bar Soap', ['130g Bar' => [25, 32, 'pcs', 'square-consumer'], 'Pack of 4 Bars' => [95, 120, 'pkt', 'square-consumer']]],
                    ['Wheel Lemon Laundry Bar Soap', ['125g Bar' => [22, 28, 'pcs', 'unilever'], '250g Double Bar' => [42, 52, 'pcs', 'unilever']]],
                    ['Vim Dishwash Liquid Lemon Gel', ['250ml Bottle' => [75, 90, 'btl', 'unilever'], '500ml Bottle' => [140, 165, 'btl', 'unilever'], '1L Economical Bottle' => [265, 315, 'btl', 'unilever'], '750ml Refill Pouch' => [180, 215, 'pkt', 'unilever']]],
                    ['Vim Dishwash Lemon Bar Soap', ['100g Bar' => [18, 22, 'pcs', 'unilever'], '300g Bar with Scrubber' => [48, 60, 'pcs', 'unilever']]],
                    ['Harpic Power Plus 10X Toilet Cleaner Original', ['500ml Bottle' => [110, 135, 'btl', 'reckitt-benckiser'], '750ml Bottle' => [155, 185, 'btl', 'reckitt-benckiser'], '1L Bottle' => [195, 235, 'btl', 'reckitt-benckiser']]],
                    ['Harpic Power Plus Toilet Cleaner Rose Fresh', ['500ml Bottle' => [110, 135, 'btl', 'reckitt-benckiser'], '750ml Bottle' => [155, 185, 'btl', 'reckitt-benckiser']]],
                    ['Harpic Bathroom Cleaner Trigger Spray Lemon', ['500ml Spray Bottle' => [160, 195, 'btl', 'reckitt-benckiser']]],
                    ['Lizol 3-in-1 Disinfectant Surface Floor Cleaner Citrus', ['500ml Bottle' => [125, 150, 'btl', 'reckitt-benckiser'], '1L Bottle' => [230, 275, 'btl', 'reckitt-benckiser'], '2L Bottle' => [430, 510, 'btl', 'reckitt-benckiser']]],
                    ['Lizol Surface Floor Cleaner Lavender Fresh', ['500ml Bottle' => [125, 150, 'btl', 'reckitt-benckiser'], '1L Bottle' => [230, 275, 'btl', 'reckitt-benckiser']]],
                    ['Colin Advanced Glass & Surface Cleaner Spray', ['500ml Trigger Spray' => [135, 165, 'btl', 'reckitt-benckiser'], '500ml Refill Pack' => [95, 115, 'pkt', 'reckitt-benckiser']]],
                    ['Dettol Antiseptic Disinfectant Liquid', ['100ml Bottle' => [75, 90, 'btl', 'reckitt-benckiser'], '250ml Bottle' => [165, 195, 'btl', 'reckitt-benckiser'], '500ml Bottle' => [310, 365, 'btl', 'reckitt-benckiser'], '1L Bottle' => [580, 680, 'btl', 'reckitt-benckiser']]],
                    ['Savlon Antiseptic Liquid', ['112ml Bottle' => [65, 78, 'btl', 'aci-pure'], '250ml Bottle' => [140, 168, 'btl', 'aci-pure'], '500ml Bottle' => [265, 310, 'btl', 'aci-pure']]],
                    ['Fresh 2-Ply Kitchen Towel Rolls', ['Pack of 2 Rolls' => [110, 135, 'roll', 'fresh'], 'Pack of 4 Rolls' => [210, 255, 'roll', 'fresh']]],
                    ['Fresh Ultra Soft Toilet Tissue Rolls', ['Pack of 4 Rolls' => [90, 110, 'roll', 'fresh'], 'Pack of 8 Rolls' => [170, 205, 'roll', 'fresh']]],
                    ['Bashundhara Premium Facial Tissue Box', ['100 Pulls Box' => [45, 55, 'box', 'general-supermarket'], '150 Pulls Box' => [65, 78, 'box', 'general-supermarket']]],
                    ['Heavy Duty Heavy Garbage Bags (Medium 30L)', ['Roll of 30 Bags' => [120, 150, 'roll', 'general-supermarket']]],
                    ['Heavy Duty Extra Large Garbage Bags (60L)', ['Roll of 20 Bags' => [150, 185, 'roll', 'general-supermarket']]],
                ],
            ],
            'personal_care' => [
                'cat' => 'personal-care',
                'items' => [
                    ['Lifebuoy Total 10 Germ Protection Soap', ['100g Bar' => [45, 55, 'pcs', 'unilever'], '150g Mega Bar' => [65, 78, 'pcs', 'unilever'], 'Pack of 4 (100g)' => [170, 205, 'pkt', 'unilever']]],
                    ['Lifebuoy Lemon Fresh Cool Soap', ['100g Bar' => [45, 55, 'pcs', 'unilever'], 'Pack of 4' => [170, 205, 'pkt', 'unilever']]],
                    ['Lifebuoy Care with Milk Cream Soap', ['100g Bar' => [45, 55, 'pcs', 'unilever']]],
                    ['Lux Velvet Touch Jasmine & Almond Oil Soap', ['100g Bar' => [50, 60, 'pcs', 'unilever'], '150g Bar' => [72, 85, 'pcs', 'unilever'], 'Pack of 3 (150g)' => [205, 245, 'pkt', 'unilever']]],
                    ['Lux Soft Glow Rose Fragrance Soap', ['100g Bar' => [50, 60, 'pcs', 'unilever'], '150g Bar' => [72, 85, 'pcs', 'unilever']]],
                    ['Dettol Original Germ Defence Bar Soap', ['75g Bar' => [42, 50, 'pcs', 'reckitt-benckiser'], '125g Bar' => [68, 80, 'pcs', 'reckitt-benckiser'], 'Pack of 3 (125g)' => [195, 230, 'pkt', 'reckitt-benckiser']]],
                    ['Dettol Skincare with Moisturizers Soap', ['125g Bar' => [68, 80, 'pcs', 'reckitt-benckiser']]],
                    ['Dove White Beauty Cream Bar Soap', ['75g Bar' => [75, 90, 'pcs', 'unilever'], '100g Bar' => [98, 118, 'pcs', 'unilever'], '135g Bar' => [130, 155, 'pcs', 'unilever']]],
                    ['Dove Daily Moisture Nourishing Shampoo', ['180ml Bottle' => [190, 230, 'btl', 'unilever'], '340ml Bottle' => [340, 410, 'btl', 'unilever'], '650ml Pump Bottle' => [620, 740, 'btl', 'unilever']]],
                    ['Dove Intense Repair Damage Therapy Shampoo', ['180ml Bottle' => [195, 235, 'btl', 'unilever'], '340ml Bottle' => [350, 420, 'btl', 'unilever']]],
                    ['Sunsilk Thick & Long Shampoo Black Shine', ['180ml Bottle' => [175, 210, 'btl', 'unilever'], '375ml Bottle' => [320, 385, 'btl', 'unilever'], '650ml Pump' => [540, 650, 'btl', 'unilever']]],
                    ['Sunsilk Stunning Black Shine Shampoo', ['180ml Bottle' => [175, 210, 'btl', 'unilever'], '375ml Bottle' => [320, 385, 'btl', 'unilever']]],
                    ['Clear Men Cool Sport Menthol Anti-Dandruff Shampoo', ['180ml Bottle' => [195, 235, 'btl', 'unilever'], '350ml Bottle' => [360, 430, 'btl', 'unilever']]],
                    ['Head & Shoulders Smooth & Silky Anti-Dandruff', ['180ml Bottle' => [210, 250, 'btl', 'general-supermarket'], '340ml Bottle' => [380, 455, 'btl', 'general-supermarket']]],
                    ['Parachute 100% Pure Coconut Hair Oil', ['100ml Bottle' => [75, 90, 'btl', 'marico'], '200ml Bottle' => [140, 168, 'btl', 'marico'], '500ml Bottle' => [320, 380, 'btl', 'marico']]],
                    ['Parachute Advansed Aloe Vera Enriched Coconut Oil', ['150ml Bottle' => [130, 155, 'btl', 'marico'], '250ml Bottle' => [205, 245, 'btl', 'marico']]],
                    ['Unilever Pepsodent Germi Check 12H Cavity Protection', ['100g Tube' => [70, 85, 'pcs', 'unilever'], '200g Tube' => [120, 145, 'pcs', 'unilever']]],
                    ['Pepsodent Expert Protection Complete Gum Care', ['140g Tube' => [135, 165, 'pcs', 'unilever']]],
                    ['Colgate Strong Teeth Calcium Boost Toothpaste', ['100g Tube' => [75, 90, 'pcs', 'colgate-palmolive'], '200g Saver Pack' => [130, 155, 'pcs', 'colgate-palmolive']]],
                    ['Colgate MaxFresh Peppermint Cooling Crystals', ['150g Tube' => [145, 175, 'pcs', 'colgate-palmolive']]],
                    ['Oral-B CrossAction Pro-Health Toothbrush', ['Soft Bristle 1 Pc' => [65, 80, 'pcs', 'general-supermarket'], 'Medium 1 Pc' => [65, 80, 'pcs', 'general-supermarket'], 'Pack of 3' => [180, 220, 'pkt', 'general-supermarket']]],
                    ['Dettol Original Germ Defence Liquid Handwash', ['200ml Pump' => [110, 135, 'btl', 'reckitt-benckiser'], '175ml Refill Pouch' => [70, 85, 'pkt', 'reckitt-benckiser'], '1L Refill Can' => [340, 410, 'btl', 'reckitt-benckiser']]],
                    ['Lifebuoy Total 10 Antibacterial Handwash', ['200ml Pump' => [95, 115, 'btl', 'unilever'], '180ml Refill' => [65, 78, 'pkt', 'unilever']]],
                    ['Square Meril Protective Lip Care Petroleum Jelly', ['50g Jar' => [50, 65, 'pcs', 'square-consumer'], '100g Jar' => [90, 115, 'pcs', 'square-consumer']]],
                    ['Vaseline Pure Skin Original Healing Jelly', ['50g Jar' => [75, 95, 'pcs', 'unilever'], '100g Jar' => [135, 165, 'pcs', 'unilever']]],
                    ['Nivea Soft Light Moisturizing Cream', ['100ml Jar' => [190, 230, 'pcs', 'general-supermarket'], '200ml Jar' => [340, 410, 'pcs', 'general-supermarket'], '300ml Family Tub' => [480, 580, 'pcs', 'general-supermarket']]],
                    ['Gillette Fusion ProGlide Shaving Gel', ['200ml Can' => [360, 430, 'can', 'general-supermarket']]],
                    ['Gillette Mach3 Turbo Razor Blades', ['Pack of 2 Cartridges' => [350, 420, 'pkt', 'general-supermarket'], 'Pack of 4 Cartridges' => [650, 780, 'pkt', 'general-supermarket']]],
                ],
            ],
            'meat_fish' => [
                'cat' => 'meat-fish',
                'items' => [
                    ['Fresh Hygienic Clean Dressed Broiler Chicken', ['1kg Whole Cut' => [190, 220, 'kg', 'fresh'], '2kg Family Cut' => [375, 435, 'kg', 'fresh']]],
                    ['Fresh Cleaned Boneless Chicken Breast Fillet', ['500g Tray' => [180, 215, 'pkt', 'fresh'], '1kg Tray' => [350, 420, 'kg', 'fresh']]],
                    ['Fresh Chicken Drumsticks (Skinless)', ['500g Pack' => [165, 195, 'pkt', 'fresh'], '1kg Pack' => [320, 380, 'kg', 'fresh']]],
                    ['Fresh Premium Dressed Sonali Deshi Chicken', ['800g-1kg Bird' => [280, 330, 'kg', 'fresh'], '1.2kg Bird' => [340, 395, 'kg', 'fresh']]],
                    ['Kazi Farms Fresh Dressed Broiler Chicken', ['1kg Pack' => [195, 225, 'kg', 'kazi-farms']]],
                    ['Fresh Farm Raised Beef Bone-In Curry Cut', ['1kg Pack' => [680, 780, 'kg', 'fresh'], '2kg Pack' => [1340, 1540, 'kg', 'fresh']]],
                    ['Fresh Premium Beef Boneless Steak Cut', ['1kg Pack' => [820, 950, 'kg', 'fresh']]],
                    ['Fresh Mutton / Goat Meat Curry Cut', ['1kg Pack' => [980, 1150, 'kg', 'fresh']]],
                    ['Fresh Sweetwater Rui Fish Cut & Cleaned', ['1kg Pack' => [340, 390, 'kg', 'fresh'], '2kg Medium Fish Cut' => [660, 760, 'kg', 'fresh']]],
                    ['Fresh Sweetwater Catla Fish Curry Cut', ['1kg Pack' => [350, 410, 'kg', 'fresh'], '2kg Large Cut' => [690, 800, 'kg', 'fresh']]],
                    ['Fresh Padma River Hilsa (Ilish) Fish', ['800g Whole Fish' => [1100, 1300, 'pcs', 'fresh'], '1kg-1.2kg Whole Fish' => [1650, 1950, 'pcs', 'fresh'], '1.5kg Giant Fish' => [2400, 2800, 'pcs', 'fresh']]],
                    ['Fresh Cleaned Tiger Prawns (Galda Chingri)', ['500g Pack' => [480, 560, 'pkt', 'fresh'], '1kg Pack' => [940, 1100, 'kg', 'fresh']]],
                    ['Fresh Cleaned Small White Shrimp (Gura Chingri)', ['500g Pack' => [280, 340, 'pkt', 'fresh'], '1kg Pack' => [540, 650, 'kg', 'fresh']]],
                    ['Fresh Telapia Fish Cleaned Whole', ['1kg (3-4 Pcs)' => [190, 230, 'kg', 'fresh']]],
                    ['Fresh Pangas Fish Curry Cut', ['1kg Pack' => [160, 195, 'kg', 'fresh']]],
                ],
            ],
            'frozen_foods' => [
                'cat' => 'frozen-foods',
                'items' => [
                    ['Kazi Farms Kitchen Crispy Chicken Nuggets', ['250g Pack' => [160, 195, 'pkt', 'kazi-farms'], '500g Economy Pack' => [300, 360, 'pkt', 'kazi-farms'], '1kg Party Pack' => [580, 690, 'kg', 'kazi-farms']]],
                    ['Golden Harvest Crispy Chicken Nuggets', ['250g Pack' => [155, 190, 'pkt', 'golden-harvest'], '500g Pack' => [295, 355, 'pkt', 'golden-harvest']]],
                    ['Kazi Farms Kitchen Chicken Sausages Plain', ['300g Pack' => [175, 210, 'pkt', 'kazi-farms'], '600g Pack' => [335, 400, 'pkt', 'kazi-farms']]],
                    ['Golden Harvest Smoked Chicken Sausages', ['300g Pack' => [180, 215, 'pkt', 'golden-harvest']]],
                    ['Kazi Farms Kitchen Crispy Chicken Popcorn', ['250g Pack' => [170, 205, 'pkt', 'kazi-farms'], '500g Pack' => [320, 385, 'pkt', 'kazi-farms']]],
                    ['Golden Harvest Plain Handmade Paratha', ['5 Pcs Pack' => [90, 110, 'pkt', 'golden-harvest'], '10 Pcs Family Pack' => [170, 205, 'pkt', 'golden-harvest'], '20 Pcs Mega Pack' => [320, 385, 'pkt', 'golden-harvest']]],
                    ['Kazi Farms Kitchen Whole Wheat Paratha', ['10 Pcs Pack' => [185, 225, 'pkt', 'kazi-farms']]],
                    ['Golden Harvest Crispy Chicken Samucha', ['10 Pcs Pack' => [130, 160, 'pkt', 'golden-harvest'], '20 Pcs Pack' => [245, 295, 'pkt', 'golden-harvest']]],
                    ['Golden Harvest Spicy Chicken Singara', ['10 Pcs Pack' => [120, 150, 'pkt', 'golden-harvest'], '20 Pcs Pack' => [230, 280, 'pkt', 'golden-harvest']]],
                    ['Kazi Farms Kitchen Chicken Spring Rolls', ['10 Pcs Pack' => [140, 170, 'pkt', 'kazi-farms']]],
                    ['Golden Harvest Sweet Green Peas (Frozen)', ['250g Pack' => [65, 80, 'pkt', 'golden-harvest'], '500g Pack' => [120, 150, 'pkt', 'golden-harvest']]],
                    ['Golden Harvest French Fries Crinkle Cut', ['500g Pack' => [135, 165, 'pkt', 'golden-harvest'], '1kg Pack' => [250, 305, 'pkt', 'golden-harvest']]],
                    ['Maggi 2-Minute Masala Instant Noodles', ['Single 62g Pack' => [18, 22, 'pkt', 'nestle'], '4-in-1 Value Pack' => [70, 85, 'box', 'nestle'], '8-in-1 Family Pack' => [135, 165, 'box', 'nestle'], '12-in-1 Mega Pack' => [195, 240, 'box', 'nestle']]],
                    ['Maggi Fusian Spicy Garlic Instant Noodles', ['Single 73g Pack' => [25, 30, 'pkt', 'nestle'], '4-Pack' => [95, 115, 'box', 'nestle']]],
                    ['Pran Mr. Noodles Magic Masala', ['Single 60g Pack' => [16, 20, 'pkt', 'pran'], '4-Pack' => [62, 76, 'box', 'pran'], '8-Pack' => [120, 148, 'box', 'pran']]],
                    ['Pran Mr. Noodles Korean Super Spicy Ramen', ['Single 120g Pack' => [45, 55, 'pkt', 'pran'], 'Pack of 4' => [170, 210, 'box', 'pran']]],
                    ['Knorr Classic Thick Chicken Corn Soup', ['40g Pouch' => [40, 50, 'pkt', 'unilever']]],
                    ['Knorr Hot & Sour Chicken Vegetable Soup', ['45g Pouch' => [40, 50, 'pkt', 'unilever']]],
                    ['Knorr Classic Thai Soup Mix', ['42g Pouch' => [42, 52, 'pkt', 'unilever']]],
                ],
            ],
            'fresh_produce' => [
                'cat' => 'fresh-produce',
                'items' => [
                    ['Fresh Local Diamond Potatoes (Alu)', ['1kg Net' => [35, 45, 'kg', 'fresh'], '2kg Net' => [68, 88, 'kg', 'fresh'], '5kg Bag' => [165, 215, 'kg', 'fresh'], '10kg Sack' => [320, 420, 'kg', 'fresh']]],
                    ['Fresh Red Local Onions (Deshi Peyaj)', ['1kg Net' => [75, 95, 'kg', 'fresh'], '2kg Net' => [145, 185, 'kg', 'fresh'], '5kg Bag' => [350, 450, 'kg', 'fresh']]],
                    ['Fresh Imported Sweet Red Onions', ['1kg Net' => [60, 78, 'kg', 'fresh'], '5kg Bag' => [290, 375, 'kg', 'fresh']]],
                    ['Fresh Chinese White Garlic (Roshun)', ['500g Pack' => [85, 110, 'pkt', 'fresh'], '1kg Net' => [165, 210, 'kg', 'fresh']]],
                    ['Fresh Deshi Garlic (Deshi Roshun)', ['500g Pack' => [95, 125, 'pkt', 'fresh'], '1kg Net' => [185, 240, 'kg', 'fresh']]],
                    ['Fresh Clean Ginger (Deshi Ada)', ['500g Pack' => [90, 115, 'pkt', 'fresh'], '1kg Net' => [175, 225, 'kg', 'fresh']]],
                    ['Fresh Round Red Tomatoes (Shobji Tomato)', ['500g Pack' => [35, 48, 'pkt', 'fresh'], '1kg Net' => [65, 90, 'kg', 'fresh'], '2kg Pack' => [125, 175, 'kg', 'fresh']]],
                    ['Fresh Deshi Green Chili (Kacha Morich)', ['200g Pack' => [25, 35, 'pkt', 'fresh'], '500g Pack' => [55, 75, 'pkt', 'fresh'], '1kg Net' => [100, 140, 'kg', 'fresh']]],
                    ['Fresh Green Coriander Leaves (Dhania Pata)', ['100g Bunch' => [15, 22, 'pkt', 'fresh'], '250g Bunch' => [32, 45, 'pkt', 'fresh']]],
                    ['Fresh Green Lemon (Kagoji Lebu)', ['Pack of 4 Pcs' => [25, 35, 'pkt', 'fresh'], 'Pack of 12 (1 Dozen)' => [70, 95, 'dzn', 'fresh']]],
                    ['Fresh Deshi Cucumber (Shosha)', ['500g Net' => [25, 35, 'pkt', 'fresh'], '1kg Net' => [48, 65, 'kg', 'fresh']]],
                    ['Fresh Hybrid Carrot (Gajor)', ['500g Net' => [35, 48, 'pkt', 'fresh'], '1kg Net' => [65, 90, 'kg', 'fresh']]],
                    ['Fresh Crisp Green Capsicum (Bell Pepper)', ['250g Pack' => [45, 60, 'pkt', 'fresh'], '500g Pack' => [85, 115, 'pkt', 'fresh']]],
                    ['Fresh Imported Royal Gala Red Apples', ['1kg Pack (approx 5-6 pcs)' => [260, 320, 'kg', 'general-supermarket'], '2kg Box' => [510, 625, 'box', 'general-supermarket']]],
                    ['Fresh Imported Fuji Sweet Apples', ['1kg Pack' => [270, 330, 'kg', 'general-supermarket'], '2kg Pack' => [530, 645, 'box', 'general-supermarket']]],
                    ['Fresh Valencia Sweet Juicy Oranges (Malta)', ['1kg Pack' => [220, 275, 'kg', 'general-supermarket'], '2kg Pack' => [430, 535, 'box', 'general-supermarket']]],
                    ['Fresh Imported Green Pears (Nashpati)', ['1kg Pack' => [280, 345, 'kg', 'general-supermarket']]],
                    ['Fresh Seedless Red Grapes (Angur)', ['500g Box' => [190, 240, 'box', 'general-supermarket'], '1kg Box' => [370, 465, 'box', 'general-supermarket']]],
                    ['Fresh Seedless Sweet Green Grapes', ['500g Box' => [170, 215, 'box', 'general-supermarket'], '1kg Box' => [330, 415, 'box', 'general-supermarket']]],
                    ['Fresh Deshi Sagar Sweet Bananas (Kola)', ['4 Pcs Pack' => [32, 42, 'pkt', 'fresh'], '12 Pcs (1 Dozen)' => [90, 120, 'dzn', 'fresh']]],
                    ['Fresh Sweet Green Papaya (Kacha Pepe)', ['1kg Whole' => [35, 48, 'kg', 'fresh']]],
                    ['Fresh Ripe Papaya Sweet (Paka Pepe)', ['1.5kg-2kg Fruit' => [120, 160, 'pcs', 'fresh']]],
                ],
            ],
        ];

        // 5. Expand and synthesize to 1,000+ items with variety
        $allProducts = [];
        $skuCounter = 1;
        $barcodeBase = 890103000000;

        // Modifiers / Flavors / Editions to dynamically generate rich SKU variations
        $modifiers = [
            'Standard',
            'Value Pack',
            'Special Edition',
            'Export Quality',
            'Club Pack',
            'Promo Bundle',
            'Family Saver',
        ];

        $stockDistributions = [
            ['stock' => 0, 'weight' => 5],     // out of stock (5%)
            ['stock' => 3, 'weight' => 10],    // low stock (10%)
            ['stock' => 25, 'weight' => 50],   // normal healthy stock (50%)
            ['stock' => 75, 'weight' => 25],   // high stock (25%)
            ['stock' => 150, 'weight' => 10],  // wholesale surplus stock (10%)
        ];

        // First pass: generate directly from all catalog template combinations
        foreach ($catalogTemplates as $catKey => $group) {
            $catModel = $categories[$group['cat']] ?? null;

            foreach ($group['items'] as [$baseName, $variants]) {
                foreach ($variants as $variantName => [$pPrice, $sPrice, $unitShort, $brandSlug]) {
                    $brandModel = $brands[$brandSlug] ?? $brands['general-supermarket'];
                    $unitModel = $units[$unitShort] ?? $units['pcs'];

                    $fullName = "{$baseName} {$variantName}";
                    $sku = sprintf('PRD-%04d', $skuCounter);
                    $barcode = (string) ($barcodeBase + $skuCounter);

                    $wsPrice = round($pPrice * 1.08, 2);
                    $discPrice = round($sPrice * 0.96, 2);
                    if ($discPrice < $pPrice) {
                        $discPrice = $sPrice;
                    }

                    // Stock calculation
                    $rand = rand(1, 100);
                    if ($rand <= 5) {
                        $stock = 0;
                    } elseif ($rand <= 15) {
                        $stock = rand(1, 4); // low stock
                    } elseif ($rand <= 65) {
                        $stock = rand(15, 60);
                    } else {
                        $stock = rand(65, 180);
                    }

                    // Expiry distribution (some expiring in 7 days, 30 days, 60 days, 1-2 years)
                    $expRand = rand(1, 100);
                    if ($expRand <= 4) {
                        $expiry = now()->addDays(rand(2, 6))->toDateString();
                    } elseif ($expRand <= 10) {
                        $expiry = now()->addDays(rand(12, 28))->toDateString();
                    } elseif ($expRand <= 20) {
                        $expiry = now()->addDays(rand(35, 58))->toDateString();
                    } else {
                        $expiry = now()->addMonths(rand(4, 24))->toDateString();
                    }

                    $allProducts[] = [
                        'sku' => $sku,
                        'barcode' => $barcode,
                        'name' => $fullName,
                        'slug' => Str::slug($fullName . '-' . $skuCounter),
                        'category_id' => $catModel?->id,
                        'brand_id' => $brandModel?->id,
                        'unit_id' => $unitModel?->id,
                        'purchase_price' => $pPrice,
                        'selling_price' => $sPrice,
                        'wholesale_price' => $wsPrice,
                        'discount_price' => $discPrice,
                        'tax_rate' => rand(0, 5) == 5 ? 5.00 : 0.00,
                        'minimum_stock' => rand(5, 15),
                        'current_stock' => $stock,
                        'expiry_date' => $expiry,
                        'batch_number' => 'BAT-' . date('Y') . '-' . str_pad($skuCounter % 500, 4, '0', STR_PAD_LEFT),
                        'status' => 'active',
                        'description' => "High-quality {$fullName} sourced for retail & wholesale inventory.",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $skuCounter++;
                }
            }
        }

        // Secondary Pass: Generate up to 1,050 items by creating realistic departmental variations
        $addonAttributes = [
            'Premium Grade',
            'Organic Select',
            'Super Saver',
            'Classic Edition',
            'Export Quality',
            'Family Pack',
            'Economy Refill',
            'Gold Roast',
            'Low Sodium',
            'Extra Virgin',
            'Fortified Plus',
            'Pure Natural',
            'Crunchy Crisps',
            'Instant Ready',
            'Rich Aroma',
            'Traditional Blend',
        ];

        $categoriesList = array_values($categories);
        $brandsList = array_values($brands);
        $unitsList = array_values($units);

        $templateCount = count($allProducts);
        $index = 0;

        while ($skuCounter <= 1050) {
            $baseSeed = $allProducts[$index % $templateCount];
            $addon = $addonAttributes[($skuCounter + $index) % count($addonAttributes)];
            $variantNo = (int) floor($skuCounter / $templateCount) + 1;

            $newName = "{$baseSeed['name']} ({$addon} v{$variantNo})";
            $sku = sprintf('PRD-%04d', $skuCounter);
            $barcode = (string) ($barcodeBase + $skuCounter);

            $pPrice = round($baseSeed['purchase_price'] * (1 + (($skuCounter % 15) - 7) / 100), 2);
            if ($pPrice <= 5) $pPrice = 10;
            $sPrice = round($pPrice * 1.18, 2);
            $wsPrice = round($pPrice * 1.08, 2);
            $discPrice = round($sPrice * 0.96, 2);

            // Stock calculation
            $rand = rand(1, 100);
            if ($rand <= 5) {
                $stock = 0;
            } elseif ($rand <= 15) {
                $stock = rand(1, 5);
            } elseif ($rand <= 65) {
                $stock = rand(15, 60);
            } else {
                $stock = rand(65, 180);
            }

            // Expiry distribution
            $expRand = rand(1, 100);
            if ($expRand <= 4) {
                $expiry = now()->addDays(rand(2, 6))->toDateString();
            } elseif ($expRand <= 10) {
                $expiry = now()->addDays(rand(12, 28))->toDateString();
            } elseif ($expRand <= 20) {
                $expiry = now()->addDays(rand(35, 58))->toDateString();
            } else {
                $expiry = now()->addMonths(rand(4, 24))->toDateString();
            }

            $allProducts[] = [
                'sku' => $sku,
                'barcode' => $barcode,
                'name' => $newName,
                'slug' => Str::slug($newName . '-' . $skuCounter),
                'category_id' => $baseSeed['category_id'],
                'brand_id' => $baseSeed['brand_id'],
                'unit_id' => $baseSeed['unit_id'],
                'purchase_price' => $pPrice,
                'selling_price' => $sPrice,
                'wholesale_price' => $wsPrice,
                'discount_price' => $discPrice,
                'tax_rate' => rand(0, 5) == 5 ? 5.00 : 0.00,
                'minimum_stock' => rand(5, 15),
                'current_stock' => $stock,
                'expiry_date' => $expiry,
                'batch_number' => 'BAT-' . date('Y') . '-' . str_pad($skuCounter % 500, 4, '0', STR_PAD_LEFT),
                'status' => 'active',
                'description' => "Commercial stock for {$newName}.",
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $skuCounter++;
            $index++;
        }

        // Insert / Update in chunks for maximum database speed and transaction safety
        $chunks = array_chunk($allProducts, 150);
        foreach ($chunks as $chunk) {
            Product::query()->upsert(
                $chunk,
                ['sku'],
                [
                    'barcode',
                    'name',
                    'slug',
                    'category_id',
                    'brand_id',
                    'unit_id',
                    'purchase_price',
                    'selling_price',
                    'wholesale_price',
                    'discount_price',
                    'tax_rate',
                    'minimum_stock',
                    'current_stock',
                    'expiry_date',
                    'batch_number',
                    'status',
                    'description',
                    'updated_at',
                ]
            );
        }
    }
}
