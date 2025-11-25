<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $batchSize = config('seeder.batch_size');
        $total = config('seeder.total');
        for ($i = 0; $i < $total; $i += $batchSize) {
            $currentBatchSize = min($batchSize, $total - $i);
            $this->command->info("Seeding users: " . ($i + $currentBatchSize) . " of " . $total);
            $users = [];
            $password = bcrypt('password');
            $rememberToken = \Illuminate\Support\Str::random(10);
            for ($j = 0; $j < $currentBatchSize; $j++) {
                $users[] = [
                    'first_name' => fake()->firstName(),
                    'last_name' => fake()->lastName(),
                    'email' => fake()->unique()->safeEmail(),
                    'email_verified_at' => now(),
                    'password' => $password,
                    'remember_token' => $rememberToken,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('users')->insert($users);
        }
    }
}
