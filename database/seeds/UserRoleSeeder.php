<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Role;
use App\User;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('user_roles')->truncate();
        $currentTimestamp = now();

        $userAdmin = User::where('username', '=', 'admin')->first();
        $roleAdmin = Role::where('name', '=', 'admin')->first();
        
        DB::table('user_roles')
            ->insert([
                [
                    'user_id' => $userAdmin['id'],
                    'role_id' => $roleAdmin['id'],
                    'created_at' => $currentTimestamp,
                    'updated_at' => $currentTimestamp,
                ]
            ]);
    }
}
