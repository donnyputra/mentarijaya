<?php

use Illuminate\Database\Seeder;

class InventoryStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currentTimestamp = now();

        DB::table('inventory_status')
	        ->insert([
	        	[
	        		'code' => 'general',
		        	'description' => 'General',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
		        	'deleted_at' => null
	        	],
	        	[
	        		'code' => 'br',
		        	'description' => 'BR',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
		        	'deleted_at' => null
	        	],
	        	[
	        		'code' => 'b1',
		        	'description' => 'B1',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
		        	'deleted_at' => null
	        	],
	        ]);
    }
}
