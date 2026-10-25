<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = env('ADMIN_NAME');
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        if (
            ! is_string($name) ||
            trim($name) === '' ||
            ! is_string($email) ||
            ! filter_var($email, FILTER_VALIDATE_EMAIL) ||
            ! is_string($password) ||
            strlen($password) < 12
        ) {
            throw new RuntimeException(
                'Set ADMIN_NAME, a valid ADMIN_EMAIL, and an ADMIN_PASSWORD of at least 12 characters in .env.'
            );
        }

        Admin::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'is_active' => true,
            ]
        );
    }
}
