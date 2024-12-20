<?php

namespace Database\Seeders;

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
        DB::table('roles')->insert([
            ['role' => 'venue_coordinator', 'description' => 'Coordinator of the assigned venues.'],
            ['role' => 'event_coordinator', 'description' => 'Coordinator of the event.'],
        ]);
    }
}
