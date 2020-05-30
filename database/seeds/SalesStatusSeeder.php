<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalesStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('sales_status')->truncate();
        $currentTimestamp = now();

        DB::table('sales_status')
	        ->insert([
	        	[
        			'code' => 'submitted',
		        	'description' => 'Submitted',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
		        	'deleted_at' => null
	        	],
	        	[
	        		'code' => 'completed',
		        	'description' => 'Completed',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
		        	'deleted_at' => null
	        	]
	        ]);
    }
}
