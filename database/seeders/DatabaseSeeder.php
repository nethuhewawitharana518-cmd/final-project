<?php

namespace Database\Seeders;

use App\Models\FoodCategory;
use App\Models\User;
use App\Models\Business;
use App\Models\Subscription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin user
        User::create([
            'name'              => 'Platform Admin',
            'email'             => 'nthewawitharana@gmail.com',
            'password'          => Hash::make('NhTp1234'),
            'role'              => 'admin',
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);

        // 2. Demo customer with mandatory home address
        User::create([
            'name'              => 'Amara Silva',
            'email'             => 'customer@demo.lk',
            'password'          => Hash::make('Demo@1234'),
            'role'              => 'customer',
            'status'            => 'active',
            'email_verified_at' => now(),
            'phone'             => '0771234567',
            'home_address'      => '456 Temple Road, Trincomalee',
        ]);

        // 5. Food categories
        $categories = [
            ['name' => 'Meals',        'icon' => 'fa-utensils',   'slug' => 'meals'],
            ['name' => 'Rice & Curry', 'icon' => 'fa-bowl-rice',  'slug' => 'rice-curry'],
            ['name' => 'Bakery Items', 'icon' => 'fa-bread-slice','slug' => 'bakery'],
            ['name' => 'Desserts',     'icon' => 'fa-cake-candles','slug' => 'desserts'],
            ['name' => 'Beverages',    'icon' => 'fa-mug-hot',    'slug' => 'beverages'],
            ['name' => 'Fast Food',    'icon' => 'fa-burger',     'slug' => 'fast-food'],
            ['name' => 'Vegetarian',   'icon' => 'fa-leaf',       'slug' => 'vegetarian'],
            ['name' => 'Seafood',      'icon' => 'fa-fish',       'slug' => 'seafood'],
            ['name' => 'Other',        'icon' => 'fa-bowl-food',  'slug' => 'other'],
        ];

        foreach ($categories as $cat) {
            FoodCategory::create($cat);
        }

        // Call FoodSeeder to seed authentic Sri Lankan dishes
        $this->call(FoodSeeder::class);

        $this->command->info('✅ Seeder complete. Admin: nthewawitharana@gmail.com / NhTp1234');
    }
}
