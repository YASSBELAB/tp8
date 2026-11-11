<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Category::factory(5)->create();

        Product::factory(20)->create();

        Client::factory(10)->create();

        Order::factory(15)->create();

        OrderItem::factory(30)->create();
    }
}
