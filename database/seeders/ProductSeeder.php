<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Piatos', 'category_id' => 1, 'price' => 12000, 'stock' => 120, 'unit' => 'Bks'],
            ['name' => 'Pulpen', 'category_id' => 2, 'price' => 18000, 'stock' => 33, 'unit' => 'Pcs'],
            ['name' => 'Paracetamol', 'category_id' => 3, 'price' => 15000, 'stock' => 32, 'unit' => 'Strip'],
            ['name' => 'Le Mineral', 'category_id' => 4, 'price' => 15000, 'stock' => 3, 'unit' => 'Botol'],
            ['name' => 'Tehpucuk', 'category_id' => 4, 'price' => 5000, 'stock' => 120, 'unit' => 'Botol'],
            ['name' => 'Roma Kelapa', 'category_id' => 1, 'price' => 8000, 'stock' => 81, 'unit' => 'Bks'],
            ['name' => 'Kecap', 'category_id' => 5, 'price' => 8000, 'stock' => 32, 'unit' => 'Btl'],
        ];

        foreach ($products as $product) {
            \App\Models\Product::create([
                'category_id' => $product['category_id'],
                'name' => $product['name'],
                'unit' => $product['unit'],
                'purchase_price' => $product['price'] * 0.7,
                'selling_price' => $product['price'],
                'stock' => $product['stock'],
                'min_stock' => 10,
            ]);
        }
    }
}
