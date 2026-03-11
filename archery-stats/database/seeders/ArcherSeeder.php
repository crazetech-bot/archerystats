<?php

namespace Database\Seeders;

use App\Models\Archer;
use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ArcherSeeder extends Seeder
{
    public function run(): void
    {
        $club = Club::first() ?? Club::create([
            'name'          => 'Selangor Archery Club',
            'location'      => 'Shah Alam, Selangor',
            'contact_email' => 'info@sac.my',
            'active'        => true,
        ]);

        // Create a super_admin user for login
        if (! User::where('email', 'admin@archery.my')->exists()) {
            User::create([
                'name'     => 'System Admin',
                'email'    => 'admin@archery.my',
                'password' => Hash::make('password'),
                'role'     => 'super_admin',
                'club_id'  => null,
            ]);
        }

        $archers = [
            [
                'name'          => 'Muhammad Haziq bin Zainudin',
                'email'         => 'haziq@example.my',
                'dob'           => '1998-03-15',
                'gender'        => 'male',
                'phone'         => '012-3456789',
                'team'          => 'Selangor State Team',
                'state'         => 'Selangor',
                'address_line'  => 'No. 12, Jalan Bahagia 3, Taman Bahagia',
                'postcode'      => '40150',
                'address_state' => 'Selangor',
                'divisions'     => ['Recurve', 'Barebow'],
                'classification'=> '1st Class',
            ],
            [
                'name'          => 'Nurul Ain binti Razali',
                'email'         => 'nuraini@example.my',
                'dob'           => '2001-07-22',
                'gender'        => 'female',
                'phone'         => '013-4567890',
                'team'          => 'KL Youth Squad',
                'state'         => 'Kuala Lumpur',
                'address_line'  => 'Unit 5-3, Residensi Maju, Jalan Bukit Bintang',
                'postcode'      => '55100',
                'address_state' => 'Kuala Lumpur',
                'divisions'     => ['Recurve'],
                'classification'=> 'Bowman',
            ],
            [
                'name'          => 'Rajendran s/o Subramaniam',
                'email'         => 'raja@example.my',
                'dob'           => '1990-11-08',
                'gender'        => 'male',
                'phone'         => '016-7891234',
                'team'          => 'Perak Masters',
                'state'         => 'Perak',
                'address_line'  => 'No. 8, Lorong Damai, Taman Damai Jaya',
                'postcode'      => '30100',
                'address_state' => 'Perak',
                'divisions'     => ['Compound', 'Traditional'],
                'classification'=> 'Grand Bowman',
            ],
            [
                'name'          => 'Siti Hajar binti Abdullah',
                'email'         => 'sitihajar@example.my',
                'dob'           => '2005-01-30',
                'gender'        => 'female',
                'phone'         => '014-5678901',
                'team'          => 'Johor Junior',
                'state'         => 'Johor',
                'address_line'  => 'No. 45, Jalan Aman, Taman Aman',
                'postcode'      => '81300',
                'address_state' => 'Johor',
                'divisions'     => ['Barebow'],
                'classification'=> null,
            ],
            [
                'name'          => 'Lee Chun Kiat',
                'email'         => 'lee.ck@example.my',
                'dob'           => '1995-09-12',
                'gender'        => 'male',
                'phone'         => '011-2345678',
                'team'          => 'Penang Archery Association',
                'state'         => 'Pulau Pinang',
                'address_line'  => '88-A, Jalan Penang, Georgetown',
                'postcode'      => '10000',
                'address_state' => 'Pulau Pinang',
                'divisions'     => ['Recurve', 'Compound'],
                'classification'=> '2nd Class',
            ],
        ];

        foreach ($archers as $data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make('password'),
                'role'     => 'archer',
                'club_id'  => $club->id,
            ]);

            Archer::create([
                'user_id'        => $user->id,
                'club_id'        => $club->id,
                'date_of_birth'  => $data['dob'],
                'gender'         => $data['gender'],
                'phone'          => $data['phone'],
                'team'           => $data['team'],
                'state'          => $data['state'],
                'country'        => 'Malaysia',
                'address_line'   => $data['address_line'],
                'postcode'       => $data['postcode'],
                'address_state'  => $data['address_state'],
                'divisions'      => $data['divisions'],
                'classification' => $data['classification'] ?? null,
                'active'         => true,
            ]);
        }
    }
}
