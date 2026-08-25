<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// ============================================================
// USERFACTORY (Pabrik Data User untuk Testing/Seeding)
// ============================================================
// Factory = "mesin pembuat data tiruan" untuk sebuah model.
// Dipakai saat seeding / test agar tidak menulis data satu-satu.
//
// Setiap pemanggilan User::factory()->create() menghasilkan
// 1 baris users dengan data acak sesuai definition() di bawah.
// (Kolon yang tidak disebut otomatis memakai nilai acak ini.)
// ============================================================

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),                       // nama acak
            'username' => fake()->unique()->userName(),     // username unik acak
            'email' => fake()->unique()->safeEmail(),       // email unik acak
            'role' => 'mahasiswa',                          // default peran mahasiswa
            'email_verified_at' => now(),                   // email dianggap sudah verifikasi
            'password' => static::$password ??= Hash::make('password'),
            // ??= : hash password dibuat SEKALI saja lalu dipakai ulang
            // untuk semua data hasil factory ini (hemat waktu).

            'remember_token' => Str::random(10),            // token "ingat saya"
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
