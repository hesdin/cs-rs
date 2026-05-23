<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@cs-rs.test'],
            [
                'name' => 'Admin RS',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $this->call([
            ChatbotSettingSeeder::class,
            FaqCategorySeeder::class,
            FaqSeeder::class,
            DoctorSeeder::class,
        ]);
    }
}
