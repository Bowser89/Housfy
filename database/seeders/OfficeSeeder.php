<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    /**
     * Offices records number to be created in database seeding process.
     *
     * @var int
     */
    const OFFICES_DEFAULT_NUMBER_SEEDS = 50;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Office::factory()
            ->times(self::OFFICES_DEFAULT_NUMBER_SEEDS)
            ->create();
    }
}
