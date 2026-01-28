<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use App\Mail\InternAccountCreated;
use App\Models\User;

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

Artisan::command('users:verify-email {email : Email address to mark as verified}', function () {
    $email = Str::lower(trim((string) $this->argument('email')));

    /** @var \App\Models\User|null $user */
    $user = User::query()->where('email', $email)->first();
    if (!$user) {
        $this->error('User not found for email: ' . $email);
        return 1;
    }

    if ($user->email_verified_at !== null) {
        $this->info('Already verified.');
        $this->line('User ID: ' . $user->id);
        return 0;
    }

    $user->email_verified_at = now();
    $user->save();

    $this->info('Email marked as verified.');
    $this->line('User ID: ' . $user->id);
    return 0;
})->purpose('Mark a user email as verified (useful when accounts are created by admin and verified middleware is enabled)');

Artisan::command('mail:test-intern-account {--to= : Recipient email address} {--mailer= : Mailer name override (e.g. smtp, log)} {--base-url= : Base URL to put in the email (e.g. https://xxxx.trycloudflare.com)} {--name= : Recipient name} {--username= : Username to include} {--password= : Temporary password to include} {--created-by= : Creator name to include} {--create-user : Also create a real intern user in the database so login works} {--reset-existing : If user already exists, also reset their password to the provided one}', function () {
    $to = (string) ($this->option('to') ?: 'test@localhost');
    $mailer = (string) ($this->option('mailer') ?: '');

    $recipientName = (string) ($this->option('name') ?: 'Intern Test');
    $username = (string) ($this->option('username') ?: 'intern.test');
    $temporaryPassword = (string) ($this->option('password') ?: 'PasswordSementara123');
    $createdByName = (string) ($this->option('created-by') ?: 'System');

    $baseUrlOpt = trim((string) ($this->option('base-url') ?: ''));
    if ($baseUrlOpt !== '') {
        $loginUrl = rtrim($baseUrlOpt, '/') . '/login';
    } else {
        $loginUrl = (string) (config('app.url') ? rtrim((string) config('app.url'), '/') . '/login' : url('/login'));
    }

    if ((bool) $this->option('create-user')) {
        $existing = User::query()->where('email', $to)->first();
        if ($existing) {
            if (! (bool) $this->option('reset-existing')) {
                $this->error('User already exists. To avoid sending a wrong password, rerun with --reset-existing to reset the password, or omit --create-user.');
                $this->line('User ID: ' . $existing->id);
                return 1;
            }

            $existing->password = $temporaryPassword;
            $existing->must_change_password = true;
            $existing->role = $existing->role ?: 'intern';
            $existing->active = true;
            if ($existing->email_verified_at === null) {
                $existing->email_verified_at = now();
            }
            $existing->save();

            $this->info('Existing user password reset so login works.');
            $this->line('User ID: ' . $existing->id);
        } else {
            // generate unique NIK 16 digits
            do {
                $nik = str_pad((string) random_int(0, 9999999999999999), 16, '0', STR_PAD_LEFT);
            } while (User::query()->where('nik', $nik)->exists());

            $finalUsername = $username;
            if (User::query()->where('username', Str::lower($finalUsername))->exists()) {
                $finalUsername = Str::lower($finalUsername) . '.' . Str::lower(Str::random(4));
            }

            $created = User::query()->create([
                'name' => $recipientName,
                'nik' => $nik,
                'phone' => '0000000000',
                'username' => Str::lower($finalUsername),
                'email' => Str::lower($to),
                'email_verified_at' => now(),
                'role' => 'intern',
                'active' => true,
                'intern_status' => 'aktif',
                'must_change_password' => true,
                'epikir_letter_token' => null,
                'password' => $temporaryPassword,
            ]);

            // ensure email template uses the final username
            $username = (string) $created->username;

            $this->info('New intern user created so login works.');
            $this->line('User ID: ' . $created->id);
            $this->line('NIK: ' . $created->nik);
            $this->line('Username: ' . $created->username);
        }
    }

    try {
        $mailable = new InternAccountCreated(
            recipientName: $recipientName,
            username: $username,
            email: $to,
            temporaryPassword: $temporaryPassword,
            loginUrl: $loginUrl,
            createdByName: $createdByName,
        );

        if ($mailer !== '') {
            Mail::mailer($mailer)->to($to)->send($mailable);
        } else {
            Mail::to($to)->send($mailable);
        }

        $this->info('Test email sent (or logged) successfully.');
        $this->line("To: {$to}");
        if ($mailer !== '') {
            $this->line("Mailer: {$mailer}");
        }
        return 0;
    } catch (Throwable $e) {
        $this->error('Failed to send test email: ' . $e->getMessage());
        return 1;
    }
})->purpose('Send a test InternAccountCreated email (useful for local SMTP catchers like Mailpit/MailHog or MAIL_MAILER=log)');
