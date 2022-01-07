<?php

use Illuminate\Database\Seeder;
use App\Product;
class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=ProductsTableSeeder
     * @return void
     */
    public function run()
    {
        $details = [
            [
                'name'=>'Orange',
                'available_stock'=> 10
            ],
            [
                'name'=>'Apple',
                'available_stock'=> 10
            ]
        ];

        foreach ($details as $detail) {
            Product::create($detail);
        }
    }
}
