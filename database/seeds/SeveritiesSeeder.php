<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Severity;

class SeveritiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Severity::firstOrCreate([
            'name' => 'Info',
        ]);
        Severity::firstOrCreate([
            'name' => 'Low',
        ]);
        Severity::firstOrCreate([
            'name' => 'Medium',
        ]);
        Severity::firstOrCreate([
            'name' => 'High',
        ]);
        Severity::firstOrCreate([
            'name' => 'Critical',
        ]);
    }
}
