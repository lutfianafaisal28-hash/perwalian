<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class EnsureAdmin extends Command
{
    protected $signature = 'app:ensure-admin';

    protected $description = 'Ensure admin user exists with correct password';

    public function handle(): int
    {
        $admin = User::where('username', 'admin')->first();

        if ($admin) {
            if (!Hash::check('123456', $admin->password)) {
                $admin->update(['password' => '123456']);
                $this->info('Admin password reset successfully.');
            } else {
                $this->info('Admin user exists and password is correct.');
            }
            return self::SUCCESS;
        }

        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@stmikbandung.ac.id',
            'role' => 'admin',
            'password' => '123456',
        ]);

        $this->info('Admin user created successfully.');
        return self::SUCCESS;
    }
}
