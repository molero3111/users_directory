<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressesTableSeeder extends Seeder
{
    public function run(): void
    {
        $batchSize = config('seeder.batch_size');
        $total = config('seeder.total');
        for ($i = 0; $i < $total; $i += $batchSize) {
            $currentBatchSize = min($batchSize, $total - $i);
            $this->command->info("Seeding addresses: " . ($i + $currentBatchSize) . " of " . $total);
            $startId = $i + 1;
            $endId = $i + $currentBatchSize;
            $addresses = [];
            for ($userId = $startId; $userId <= $endId; $userId++) {
                $addresses[] = [
                    'user_id' => $userId,
                    'country' => fake()->country(),
                    'city' => fake()->city(),
                    'post_code' => fake()->postcode(),
                    'street' => fake()->streetAddress(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('addresses')->insert($addresses);
        }
    }
}
