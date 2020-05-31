<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currentTimestamp = now();

        DB::table('category')
	        ->insert([
	        	[
	        		'code' => 'A',
		        	'description' => 'Anting',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
	        	],
	        	[
	        		'code' => 'C',
		        	'description' => 'Cincin',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
                ],
                [
	        		'code' => 'CK',
		        	'description' => 'Cincin Anak',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
                ],
                [
	        		'code' => 'GL',
		        	'description' => 'Gelang',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
                ],
                [
	        		'code' => 'K',
		        	'description' => 'Kalung',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
                ],
                [
	        		'code' => 'L',
		        	'description' => 'Liontin',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
                ],
                [
	        		'code' => 'PT',
		        	'description' => 'Putih',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
                ],
                [
	        		'code' => 'W',
		        	'description' => 'Giwang',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
	        	],
	        ]);
    }
}
