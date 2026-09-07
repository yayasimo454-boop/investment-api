<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FictionalCongoleseUserSeeder extends Seeder
{
    public function run(): void
    {
        $firstNames = [
            'Grace', 'Merveille', 'Divine', 'Josué', 'Trésor', 'Bénédicte', 'Christian',
            'Patrick', 'Nathalie', 'Emmanuel', 'Blaise', 'Chantal', 'Ferdinand', 'Aline',
            'Jonathan', 'Rachel', 'Guelor', 'Espoir', 'Prisca', 'Yannick', 'Élodie',
            'Serge', 'Nadège', 'Junior', 'Gloire', 'Fiston', 'Grâce', 'Franck', 'Sylvie',
            'Freddy', 'Bijoux', 'Cédric', 'Ornella', 'Dieudonné', 'Rebecca', 'Willy',
            'Cynthia', 'Prince', 'Doriane', 'Éric', 'Vanessa', 'Landry', 'Naomie',
            'Bertin', 'Clarisse', 'Arsène', 'Deborah', 'Guyto', 'Sarah', 'Mike',
            'Fadhili', 'Bienvenu', 'Louange', 'Exaucé', 'Précieuse', 'Kevin', 'Ketsia',
            'Olivier', 'Jenny', 'Rodrigue', 'Anny', 'Sylvain', 'Peniel', 'Wilfried',
            'Docile', 'Aimé', 'Cheryl', 'Godé', 'Pathy', 'Bahati', 'Nsimba', 'Kabuya',
        ];

        $lastNames = [
            'Mukendi', 'Kabongo', 'Tshisekedi', 'Mbuyi', 'Kalonji', 'Ilunga', 'Ngoyi',
            'Kasongo', 'Mwamba', 'Kabeya', 'Mputu', 'Tshimanga', 'Kayembe', 'Mulumba',
            'Ntumba', 'Nkulu', 'Bapfuka', 'Kanku', 'Muyaya', 'Lumbala', 'Kabamba',
            'Mbala', 'Nzau', 'Tshibangu', 'Kamanda', 'Mbayo', 'Ndaya', 'Kalala',
            'Mukamba', 'Kazadi', 'Bilonda', 'Nsele', 'Kanyinda', 'Musenga', 'Kalombo',
            'Ntambwe', 'Kabasele', 'Mbombo', 'Tshiala', 'Kanyama', 'Mavinga', 'Nzuzi',
            'Kimoni', 'Lukusa', 'Mbuyamba', 'Kadima', 'Ngalula', 'Tshinyama', 'Kanda',
            'Mande', 'Kalubi', 'Bope', 'Mukalay', 'Kabuya', 'Nzeza', 'Kanyeba',
        ];

        $totalUsers = 1000;
        $vipCertifiedCount = 100;

        $badgeIndexes = collect(range(0, $totalUsers - 1))
            ->shuffle()
            ->take($vipCertifiedCount)
            ->flip();

        $hashedPassword = Hash::make('password123');
        $now = now();
        $batch = [];
        $batchSize = 200;

        for ($i = 0; $i < $totalUsers; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $fullName = "$firstName $lastName";

            $email = Str::slug($firstName . '.' . $lastName, '.') . $i . '@demo.local';

            $badge = 'none';
            if (isset($badgeIndexes[$i])) {
                $badge = collect(['vip', 'certified'])->random();
            }

            $batch[] = [
                'name' => $fullName,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => 'user',
                'is_fictional' => true,
                'badge' => $badge,
                'kyc_status' => 'verified',
                'preferred_language' => 'fr',
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($batch) >= $batchSize) {
                DB::table('users')->insert($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('users')->insert($batch);
        }
    }
}