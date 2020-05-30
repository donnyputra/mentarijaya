<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();
        $currentTimestamp = now();
        
        DB::table('users')
            ->insert(
                [
                    'name' => 'System Administrator',
                    'username' => 'admin',
                    'email' => 'admin@admin.com',
                    'password' => bcrypt('admin'),
                    'created_at' => $currentTimestamp,
                    'updated_at' => $currentTimestamp,
                ]
            );
    }
}
