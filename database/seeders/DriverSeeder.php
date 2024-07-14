<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Document;
use App\Models\Driver;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 25; $i++) {
            Driver::factory()
                ->has(Document::factory()->count(rand(0, 3)))
                ->has(Address::factory())
                ->create();
        }
    }
}
