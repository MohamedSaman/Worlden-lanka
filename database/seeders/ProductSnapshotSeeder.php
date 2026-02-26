<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalesItem;
use App\Models\ProductDetail;

class ProductSnapshotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salesItems = SalesItem::with('product')->whereNull('product_name')->get();
        
        $count = 0;
        foreach ($salesItems as $item) {
            if ($item->product) {
                $item->update([
                    'product_name' => $item->product->product_name
                ]);
                $count++;
            }
        }
        
        $this->command->info("Updated {$count} sales items with product names.");
    }
}
