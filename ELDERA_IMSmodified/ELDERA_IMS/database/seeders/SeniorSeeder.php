<?php

namespace Database\Seeders;

use App\Models\Senior;
use App\Models\Barangay;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SeniorSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('en_PH');
        $barangays = Barangay::all();
        $maritalStatuses = ['Single', 'Married', 'Widowed', 'Separated', 'Others'];
        $genders = ['Male', 'Female'];
        $religions = ['Roman Catholic', 'Iglesia ni Cristo', 'Evangelical', 'Baptist', 'Methodist', 'Seventh Day Adventist', 'Islam', 'Buddhism', 'Jehovah\'s Witness', 'Born Again Christian', 'Aglipayan', 'None', 'Others'];
        $employments = ['Retired Government Employee', 'Retired Teacher', 'Retired Nurse', 'Retired Engineer', 'Retired', 'Self-employed', 'None'];

        for ($i = 0; $i < 100; $i++) {
            $barangay = $barangays->random();
            $gender = $faker->randomElement($genders);
            $birthDate = $faker->dateTimeBetween('-100 years', '-60 years');
            $age = now()->diffInYears($birthDate);

            Senior::create([
                'osca_id' => now()->year . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'last_name' => $faker->lastName(),
                'first_name' => $faker->firstName($gender),
                'middle_name' => $faker->firstName($gender),
                'name_extension' => $faker->optional(0.1)->randomElement(['Jr.', 'Sr.', 'II', 'III']),
                'region' => 'Region I',
                'province' => 'Pangasinan',
                'city' => 'Lingayen',
                'barangay' => $barangay->name,
                'residence' => $faker->streetAddress(),
                'street' => $faker->streetName(),
                'date_of_birth' => $birthDate,
                'birth_place' => $faker->city() . ', ' . $faker->state(),
                'marital_status' => $faker->randomElement($maritalStatuses),
                'sex' => $gender,
                'contact_number' => $faker->phoneNumber(),
                'email' => $faker->optional(0.3)->email(),
                'religion' => $faker->randomElement($religions),
                'ethnic_origin' => 'Filipino',
                'language' => $faker->randomElement(['Tagalog, English', 'Ilocano, Tagalog', 'Pangasinan, Tagalog', 'English, Tagalog']),
                'gsis_sss' => $faker->optional(0.7)->numerify('##-#######-#'),
                'tin' => $faker->optional(0.6)->numerify('###-###-###-###'),
                'philhealth' => $faker->optional(0.8)->numerify('##-##########-#'),
                'sc_association' => $faker->optional(0.5)->randomElement(['Lingayen SC Association', 'Pangasinan SC Club', 'Golden Years Club', 'Senior Citizens Group']),
                'other_govt_id' => $faker->optional(0.4)->randomElement(['Voter ID: ' . $faker->numerify('########'), 'UMID: ' . $faker->numerify('########')]),
                'can_travel' => $faker->boolean(70),
                'employment' => $faker->randomElement($employments),
                'has_pension' => $faker->boolean(30),
                'status' => $faker->randomElement(['active', 'active', 'active', 'active', 'deceased']), // 80% active, 20% deceased
            ]);
        }
    }
}
