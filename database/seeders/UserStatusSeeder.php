<?php

namespace Database\Seeders;

use App\Models\UserStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!UserStatus::exists()) {
            DB::table('user_status')->insert([
                ['name' => 'pending'],
                ['name' => 'active'],
                ['name' => 'blocked'],
                ['name' => 'deleted'],
            ]);
        }
    }
}
