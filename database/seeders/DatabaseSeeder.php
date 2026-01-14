<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Setting;
use App\Models\User;
use App\Support\AppSettings;
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

        // Default rules (can be edited in Admin UI).
        // Office coordinates: Diskominfo Kab. Magelang (from provided Google Maps link).
        Setting::setValue(AppSettings::OFFICE_LAT, Setting::getValue(AppSettings::OFFICE_LAT) ?? '-7.5920462');
        Setting::setValue(AppSettings::OFFICE_LNG, Setting::getValue(AppSettings::OFFICE_LNG) ?? '110.2185363');

        Setting::setValue(AppSettings::RADIUS_M, Setting::getValue(AppSettings::RADIUS_M) ?? '50');
        Setting::setValue(AppSettings::MAX_ACCURACY_M, Setting::getValue(AppSettings::MAX_ACCURACY_M) ?? '50');

        Setting::setValue(AppSettings::CHECKIN_START, Setting::getValue(AppSettings::CHECKIN_START) ?? '08:00');
        Setting::setValue(AppSettings::CHECKIN_END, Setting::getValue(AppSettings::CHECKIN_END) ?? '12:00');
        Setting::setValue(AppSettings::CHECKOUT_START, Setting::getValue(AppSettings::CHECKOUT_START) ?? '13:00');
        Setting::setValue(AppSettings::CHECKOUT_END, Setting::getValue(AppSettings::CHECKOUT_END) ?? '16:30');

        Setting::setValue(AppSettings::SCORE_POINTS_PER_ATTENDANCE, Setting::getValue(AppSettings::SCORE_POINTS_PER_ATTENDANCE) ?? '4');
        Setting::setValue(AppSettings::SCORE_MAX, Setting::getValue(AppSettings::SCORE_MAX) ?? '100');
    }
}
