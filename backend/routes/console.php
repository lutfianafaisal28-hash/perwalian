<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:ensure-admin', function () {
    $admin = User::where('username', 'admin')->first();

    if ($admin) {
        if (!Hash::check('123456', $admin->password)) {
            $admin->update(['password' => '123456']);
            $this->info('Admin password was invalid, now reset.');
        } else {
            $this->info('Admin OK.');
        }
        return;
    }

    User::create([
        'name' => 'Administrator',
        'username' => 'admin',
        'email' => 'admin@stmikbandung.ac.id',
        'role' => 'admin',
        'password' => '123456',
    ]);

    $this->info('Admin user created.');
})->purpose('Ensure admin user exists with correct password');
