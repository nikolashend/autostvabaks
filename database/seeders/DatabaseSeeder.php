<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdmin();

        $this->call([
            PageSeeder::class,
            FaqSeeder::class,
            TestimonialSeeder::class,
        ]);
    }

    /** First administrator. Set ADMIN_EMAIL / ADMIN_PASSWORD in .env to control the credentials. */
    private function seedAdmin(): void
    {
        if (User::query()->exists()) {
            return;
        }

        $email = env('ADMIN_EMAIL', 'admin@autostvabaks.ee');
        $password = env('ADMIN_PASSWORD') ?: Str::password(16, symbols: false);

        User::create([
            'name' => 'Admin',
            'email' => $email,
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        $this->command?->warn("Admin panel: /admin  |  login: {$email}  |  password: {$password}");
    }
}
