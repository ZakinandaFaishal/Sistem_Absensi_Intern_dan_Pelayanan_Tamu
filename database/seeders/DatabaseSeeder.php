<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $admin = User::query()->where('email', 'test@example.com')->first();
        if (!$admin) {
            $admin = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'role' => 'admin',
            ]);
        } else {
            $admin->forceFill([
                'role' => 'admin',
                'active' => true,
            ])->save();
        }

        Location::query()->firstOrCreate(
            ['code' => 'KOMINFO'],
            ['name' => 'Kominfo', 'code' => 'KOMINFO']
        );
    }
}
