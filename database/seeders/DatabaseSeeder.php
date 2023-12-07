<?php

namespace Database\Seeders;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Database\Factories\AffiliateFactory;
use Database\Factories\MerchantFactory;
use Database\Factories\OrderFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       User::factory(10)->create();
       Order::factory(10)->create();
       Merchant::factory(10)->create();
       Affiliate::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // $this->call([
        //     UserFactory::class,
        //     OrderFactory::class,
        //     MerchantFactory::class,
        //     AffiliateFactory::class
        // ]);
    }   
}
