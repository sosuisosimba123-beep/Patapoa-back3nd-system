<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManualOrderSimulationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a Simulation Merchant
        $user = User::updateOrCreate(
            ['phone' => '0000000000'],
            [
                'name' => 'Global Simulation Merchant',
                'email' => 'simulation@patapoa.com',
                'password' => Hash::make('password123'),
                'user_type' => 'merchant',
                'is_active' => true,
                'is_verified' => true,
            ]
        );

        $merchant = Merchant::updateOrCreate(
            ['user_id' => $user->id],
            [
                'store_name' => 'Global Simulation Store',
                'description' => 'A special store for manual order simulation, visible everywhere.',
                'address' => 'Anywhere, Tanzania',
                'city' => 'Simulation City',
                'latitude' => -6.7766, // Default to Dar if needed, but we'll try to make it global
                'longitude' => 39.2415,
                'is_verified' => true,
                'is_online' => true,
                'rating' => 5.0,
            ]
        );

        $user->wallet()->updateOrCreate(
            ['wallet_type' => 'merchant'],
            ['balance' => 0, 'currency' => 'TZS']
        );

        // 2. Ensure a category exists
        $category = Category::updateOrCreate(
            ['slug' => 'simulation'],
            ['name' => 'Simulation Items']
        );

        // 3. Create a Dummy Product
        Product::updateOrCreate(
            ['merchant_id' => $merchant->id, 'name' => 'Simulation Dummy Product'],
            [
                'category_id' => $category->id,
                'description' => 'A dummy product for manual testing. Can be ordered from any location.',
                'price' => 1000,
                'stock_count' => 9999,
                'is_available' => true,
                'is_featured' => true,
                'images' => ['https://via.placeholder.com/500?text=Simulation+Product'],
            ]
        );
    }
}
