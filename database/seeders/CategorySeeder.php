<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Snack', 'description' => 'Makanan ringan'],
            ['name' => 'Alat Tulis Kantor', 'description' => 'ATK'],
            ['name' => 'Medicine', 'description' => 'Obat-obatan'],
            ['name' => 'Minuman', 'description' => 'Minuman ringan'],
            ['name' => 'Lainnya', 'description' => 'Barang lainnya'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
