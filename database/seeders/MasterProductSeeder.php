<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterProduct;
use App\Models\Category;
use Illuminate\Support\Str;

class MasterProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Dairy & Eggs', 'slug' => 'dairy-eggs'],
            ['name' => 'Beverages', 'slug' => 'beverages'],
            ['name' => 'Grains & Flour', 'slug' => 'grains-flour'],
            ['name' => 'Cooking Essentials', 'slug' => 'cooking-essentials'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        $dairyId = Category::where('slug', 'dairy-eggs')->first()->id;
        $beveragesId = Category::where('slug', 'beverages')->first()->id;
        $grainsId = Category::where('slug', 'grains-flour')->first()->id;
        $cookingId = Category::where('slug', 'cooking-essentials')->first()->id;

        $masterProducts = [
            // GENERIC / UNBRANDED (Used for Search Hero Images)
            [
                'category_id' => $dairyId,
                'name' => 'Milk',
                'brand' => 'Generic',
                'unit' => 'Pack',
                'primary_image_url' => 'https://images.unsplash.com/photo-1550583724-125581cc255b?q=80&w=800&auto=format&fit=crop',
                'search_tags' => ['milk', 'fresh', 'dairy', 'maziwa'],
            ],
            [
                'category_id' => $beveragesId,
                'name' => 'Drinking Water',
                'brand' => 'Generic',
                'unit' => 'Bottle',
                'primary_image_url' => 'https://images.unsplash.com/photo-1523362628744-0c100150b504?q=80&w=800&auto=format&fit=crop',
                'search_tags' => ['water', 'maji', 'drink'],
            ],
            [
                'category_id' => $cookingId,
                'name' => 'Cooking Oil',
                'brand' => 'Generic',
                'unit' => 'Litre',
                'primary_image_url' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?q=80&w=800&auto=format&fit=crop',
                'search_tags' => ['oil', 'cooking', 'mafuta'],
            ],
            [
                'category_id' => $grainsId,
                'name' => 'Wheat Flour',
                'brand' => 'Generic',
                'unit' => 'kg',
                'primary_image_url' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?q=80&w=800&auto=format&fit=crop',
                'search_tags' => ['flour', 'ngano', 'wheat'],
            ],
            [
                'category_id' => $cookingId,
                'name' => 'Sugar',
                'brand' => 'Generic',
                'unit' => 'kg',
                'primary_image_url' => 'https://images.unsplash.com/photo-1581447100512-4213d283626d?q=80&w=800&auto=format&fit=crop',
                'search_tags' => ['sugar', 'sukari'],
            ],

            // BRANDED EXAMPLES (For Merchant Onboarding Selection)
            [
                'category_id' => $dairyId,
                'name' => 'Fresh Milk',
                'brand' => 'Asas',
                'unit' => '500ml',
                'primary_image_url' => 'https://images.unsplash.com/photo-1550583724-125581cc255b?q=80&w=200&auto=format&fit=crop',
                'search_tags' => ['milk', 'fresh', 'dairy', 'asas'],
            ],
            [
                'category_id' => $dairyId,
                'name' => 'UHT Milk',
                'brand' => 'Azam',
                'unit' => '1L',
                'primary_image_url' => 'https://images.unsplash.com/photo-1563636619-e910f6401b9d?q=80&w=200&auto=format&fit=crop',
                'search_tags' => ['milk', 'uht', 'dairy', 'azam'],
            ],
        ];

        foreach ($masterProducts as $product) {
            $product['slug'] = Str::slug($product['brand'] . '-' . $product['name'] . '-' . $product['unit']);
            MasterProduct::updateOrCreate(['slug' => $product['slug']], $product);
        }
    }
}
