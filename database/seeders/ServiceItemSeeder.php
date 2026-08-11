<?php

namespace Database\Seeders;

use App\Models\ServiceItem;
use Illuminate\Database\Seeder;

class ServiceItemSeeder extends Seeder
{
    /**
     * The official per-item price list. Prices are placeholders in Naira (NGN)
     * and should be replaced with the university's approved rates.
     */
    public function run(): void
    {
        $items = [
            ['name' => 'Shirt / T-shirt', 'service' => 'wash_iron', 'unit_price' => 200],
            ['name' => 'Trousers / Jeans', 'service' => 'wash_iron', 'unit_price' => 250],
            ['name' => 'Native wear (per piece)', 'service' => 'wash_iron', 'unit_price' => 350],
            ['name' => 'Suit / Blazer', 'service' => 'dry_clean', 'unit_price' => 700],
            ['name' => 'Dress / Gown', 'service' => 'wash_iron', 'unit_price' => 400],
            ['name' => 'Bedsheet', 'service' => 'wash', 'unit_price' => 300],
            ['name' => 'Duvet / Blanket', 'service' => 'wash', 'unit_price' => 800],
            ['name' => 'Towel', 'service' => 'wash', 'unit_price' => 150],
            ['name' => 'Underwear / Socks (per piece)', 'service' => 'wash', 'unit_price' => 100],
            ['name' => 'Ironing only (per piece)', 'service' => 'iron', 'unit_price' => 100],
        ];

        foreach ($items as $i => $item) {
            ServiceItem::updateOrCreate(
                ['name' => $item['name']],
                array_merge($item, ['is_active' => true, 'sort_order' => $i]),
            );
        }
    }
}
