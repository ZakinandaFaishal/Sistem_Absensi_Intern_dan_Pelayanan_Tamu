<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('users:backfill-dinas-id {--dry-run : Only show how many rows would be updated}', function () {
    $baseQuery = DB::table('users')
        ->join('locations', 'locations.id', '=', 'users.internship_location_id')
        ->where('users.role', 'intern')
        ->whereNull('users.dinas_id')
        ->whereNotNull('users.internship_location_id')
        ->whereNotNull('locations.dinas_id');

    $count = (clone $baseQuery)->count();
    $this->info("Candidates: {$count}");

    if ((bool) $this->option('dry-run')) {
        return 0;
    }

    $affected = (clone $baseQuery)->update([
        'users.dinas_id' => DB::raw('locations.dinas_id'),
    ]);

    $this->info("Updated rows: {$affected}");
    return 0;
})->purpose('Backfill users.dinas_id for intern users from internship location dinas_id');
