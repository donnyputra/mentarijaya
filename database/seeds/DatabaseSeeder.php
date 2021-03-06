<?php

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
        // $this->call(UsersTableSeeder::class);
        $this->call(ItemStatusSeeder::class);
        $this->call(SalesStatusSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserRoleSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(AllocationSeeder::class);
        $this->call(InventoryStatusSeeder::class);
    }
}
