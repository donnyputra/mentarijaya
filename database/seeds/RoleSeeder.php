<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('roles')->truncate();
        $currentTimestamp = now();
        
        DB::table('roles')
            ->insert([
                [
                    'name' => 'admin',
                    'created_at' => $currentTimestamp,
                    'updated_at' => $currentTimestamp,
                ],
                [
                    'name' => 'employee',
                    'created_at' => $currentTimestamp,
                    'updated_at' => $currentTimestamp,
                ]
            ]);
    }
}
