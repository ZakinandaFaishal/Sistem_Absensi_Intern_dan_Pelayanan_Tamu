<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Dinas;
use App\Models\Setting;
use App\Models\User;
use App\Support\AppSettings;
use Illuminate\Support\Facades\Schema;
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

        $superAdmin = User::query()->where('email', 'test@example.com')->first();
        if (!$superAdmin) {
            $superAdmin = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'role' => 'super_admin',
            ]);
        } else {
            $superAdmin->forceFill([
                'role' => 'super_admin',
                'active' => true,
            ])->save();
        }

        // Pastikan hanya ada 1 super admin.
        // Semua user lain yang masih ber-role super_admin akan diturunkan rolenya.
        User::query()
            ->where('role', 'super_admin')
            ->where('id', '!=', $superAdmin->id)
            ->get()
            ->each(function (User $u) {
                // admin_dinas wajib punya dinas_id, jadi fallback ke intern bila kosong.
                $newRole = !empty($u->dinas_id) ? 'admin_dinas' : 'intern';
                $u->forceFill(['role' => $newRole])->save();
            });

        $defaultDinas = null;
        if (Schema::hasTable('dinas')) {
            $defaultDinas = Dinas::query()->updateOrCreate(
                ['code' => 'KOMINFO'],
                ['name' => 'Diskominfo Kab. Magelang', 'code' => 'KOMINFO']
            );
        }

        // Default rules (can be edited in Admin UI).
        // Office coordinates: Diskominfo Kab. Magelang (from provided Google Maps link).
        Setting::setValue(AppSettings::OFFICE_LAT, Setting::getValue(AppSettings::OFFICE_LAT) ?? '-7.5920462');
        Setting::setValue(AppSettings::OFFICE_LNG, Setting::getValue(AppSettings::OFFICE_LNG) ?? '110.2185363');

        $officeLat = Setting::getValue(AppSettings::OFFICE_LAT);
        $officeLng = Setting::getValue(AppSettings::OFFICE_LNG);

        Location::query()->updateOrCreate(
            ['code' => 'KOMINFO'],
            [
                'name' => 'Diskominfo Kab. Magelang',
                'code' => 'KOMINFO',
                'dinas_id' => (Schema::hasColumn('locations', 'dinas_id') && $defaultDinas) ? $defaultDinas->id : null,
                'lat' => $officeLat !== null && $officeLat !== '' ? (float) $officeLat : null,
                'lng' => $officeLng !== null && $officeLng !== '' ? (float) $officeLng : null,
            ]
        );

        Setting::setValue(AppSettings::RADIUS_M, Setting::getValue(AppSettings::RADIUS_M) ?? '50');
        Setting::setValue(AppSettings::MAX_ACCURACY_M, Setting::getValue(AppSettings::MAX_ACCURACY_M) ?? '50');

        Setting::setValue(AppSettings::CHECKIN_START, Setting::getValue(AppSettings::CHECKIN_START) ?? '08:00');
        Setting::setValue(AppSettings::CHECKIN_END, Setting::getValue(AppSettings::CHECKIN_END) ?? '12:00');
        Setting::setValue(AppSettings::CHECKOUT_START, Setting::getValue(AppSettings::CHECKOUT_START) ?? '13:00');
        Setting::setValue(AppSettings::CHECKOUT_END, Setting::getValue(AppSettings::CHECKOUT_END) ?? '16:30');

        Setting::setValue(
            AppSettings::CERTIFICATE_DEFAULT_SIGNATORY_NAME,
            Setting::getValue(AppSettings::CERTIFICATE_DEFAULT_SIGNATORY_NAME) ?? 'Kepala Dinas'
        );
        Setting::setValue(
            AppSettings::CERTIFICATE_DEFAULT_SIGNATORY_TITLE,
            Setting::getValue(AppSettings::CERTIFICATE_DEFAULT_SIGNATORY_TITLE) ?? 'Kepala Dinas Komunikasi dan Informatika'
        );
    }
}
