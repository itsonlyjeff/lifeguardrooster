<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         User::factory(30)->create();

        User::factory()->create([
            'name' => 'Jeffrey van Reenen',
            'email' => 'jeffrey92.hrb@gmail.com',
            'password' => Hash::make('Kikie1'),
            'email_verified_at' => now(),
            'is_sys_admin' => true,
        ]);
    }
}
