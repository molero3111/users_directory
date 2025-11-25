<?php

return [
    // Number of records to insert per batch
    'batch_size' => env('SEEDER_BATCH_SIZE', 12000),
    // Total number of records to insert
    'total' => env('SEEDER_TOTAL', 1000000),
];
