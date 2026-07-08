<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Merchant;
use App\Models\Category;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $electronics = Category::where('slug', 'electronics')->first();
        $groceries = Category::where('slug', 'groceries')->first();
        $hardware = Category::where('slug', 'hardware')->first();

        $merchant = Merchant::first(); // Your test merchant

        if (!$merchant) return;

        // Electronics
        Product::create([
            'merchant_id' => $merchant->id,
            'category_id' => $electronics->id,
            'name' => 'iPhone 15 Pro',
            'description' => 'Latest Apple flagship with A17 Pro chip.',
            'price' => 2500000,
            'stock_count' => 10,
            'is_available' => true,
        ]);

        Product::create([
            'merchant_id' => $merchant->id,
            'category_id' => $electronics->id,
            'name' => 'Samsung Galaxy S24',
            'description' => 'High performance Android smartphone.',
            'price' => 2100000,
            'stock_count' => 15,
            'is_available' => true,
        ]);

        // Hardware
        Product::create([
            'merchant_id' => $merchant->id,
            'category_id' => $hardware->id,
            'name' => 'Drill Machine 500W',
            'description' => 'Heavy duty power drill for all surfaces.',
            'price' => 185000,
            'stock_count' => 5,
            'is_available' => true,
        ]);

        // Groceries
        Product::create([
            'merchant_id' => $merchant->id,
            'category_id' => $groceries->id,
            'name' => 'Premium Basmati Rice 5kg',
            'description' => 'Long grain aromatic rice.',
            'price' => 25000,
            'stock_count' => 100,
            'is_available' => true,
        ]);
    }
}
