<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Compte administrateur principal
        User::updateOrCreate(
            ['email' => 'admin@investment-platform.local'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password123'), // à changer plus tard
                'role' => 'admin',
                'is_fictional' => false,
                'kyc_status' => 'verified',
                'preferred_language' => 'fr',
                'email_verified_at' => now(),
            ]
        );

        // Utilisateurs fictifs (visibles dans l'appli, ex: classement/leaderboard)
        $fictionalUsers = [
            ['name' => 'Aïcha Ndiaye', 'email' => 'aicha.ndiaye@demo.local'],
            ['name' => 'Kwame Boateng', 'email' => 'kwame.boateng@demo.local'],
            ['name' => 'Fatima El Amrani', 'email' => 'fatima.elamrani@demo.local'],
            ['name' => 'Chidi Okafor', 'email' => 'chidi.okafor@demo.local'],
            ['name' => 'Amina Traoré', 'email' => 'amina.traore@demo.local'],
        ];

        foreach ($fictionalUsers as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'is_fictional' => true,
                    'kyc_status' => 'verified',
                    'preferred_language' => 'fr',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}