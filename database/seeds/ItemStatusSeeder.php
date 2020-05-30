<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('item_status')->truncate();
    	$currentTimestamp = now();

        DB::table('item_status')
	        ->insert([
	        	[
	        		'code' => 'new',
		        	'description' => 'New',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
		        	'deleted_at' => null
	        	],
	        	[
	        		'code' => 'instock',
		        	'description' => 'In Stock',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
		        	'deleted_at' => null
	        	],
	        	[
	        		'code' => 'sold',
		        	'description' => 'Sold',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
		        	'deleted_at' => null
	        	],
	        ]);
    }
}
