<?php

use Illuminate\Database\Seeder;

class AllocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currentTimestamp = now();

        DB::table('allocation')
	        ->insert([
	        	[
	        		'code' => 'ETL',
		        	'description' => 'Etalase',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
	        	],
	        	[
	        		'code' => 'STORAGE',
		        	'description' => 'Penyimpanan Dalam',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
                ],
                [
	        		'code' => 'OL-BL',
		        	'description' => 'Online - Bukalapak',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
                ],
                [
	        		'code' => 'OL-TP',
		        	'description' => 'Online - Tokopedia',
		        	'created_at' => $currentTimestamp,
		        	'updated_at' => $currentTimestamp,
                ],
	        ]);
    }
}
