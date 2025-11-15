<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Senior;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ComprehensiveSeniorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing seniors (handle foreign key constraints) for current driver
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Senior::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } else {
            DB::statement('TRUNCATE TABLE seniors RESTART IDENTITY CASCADE');
        }

        // Sample data arrays
        $firstNames = [
            'Maria', 'Jose', 'Juan', 'Ana', 'Pedro', 'Carmen', 'Antonio', 'Rosa', 'Manuel', 'Teresa',
            'Francisco', 'Isabel', 'Carlos', 'Dolores', 'Miguel', 'Pilar', 'Rafael', 'Concepcion', 'Jose', 'Mercedes',
            'Luis', 'Josefa', 'Angel', 'Francisca', 'Fernando', 'Dolores', 'Javier', 'Maria', 'Sergio', 'Carmen',
            'Alberto', 'Pilar', 'Roberto', 'Teresa', 'Eduardo', 'Isabel', 'Victor', 'Rosa', 'Alfonso', 'Mercedes',
            'Ricardo', 'Josefa', 'Ramon', 'Concepcion', 'Jorge', 'Francisca', 'Mario', 'Dolores', 'Enrique', 'Pilar'
        ];

        $lastNames = [
            'Santos', 'Garcia', 'Rodriguez', 'Lopez', 'Martinez', 'Gonzalez', 'Perez', 'Sanchez', 'Ramirez', 'Cruz',
            'Flores', 'Rivera', 'Gomez', 'Diaz', 'Reyes', 'Morales', 'Herrera', 'Jimenez', 'Ruiz', 'Torres',
            'Aguilar', 'Vargas', 'Ramos', 'Mendoza', 'Castillo', 'Romero', 'Moreno', 'Alvarez', 'Mendez', 'Gutierrez',
            'Hernandez', 'Silva', 'Vega', 'Rojas', 'Castro', 'Ortiz', 'Delgado', 'Molina', 'Navarro', 'Guerrero',
            'Ramos', 'Medina', 'Cortes', 'Herrera', 'Vargas', 'Jimenez', 'Ruiz', 'Torres', 'Aguilar', 'Ramos'
        ];

        $barangays = [
            'aliwekwek', 'baay', 'balangobong', 'balococ', 'bantayan', 'basing', 'capandanan', 'domalandan-center',
            'domalandan-east', 'domalandan-west', 'dorongan', 'dulag', 'estanza', 'lasip', 'libsong-east', 'libsong-west',
            'malawa', 'malimpuec', 'maniboc', 'matalava', 'naguelguel', 'namolan', 'pangapisan-north', 'pangapisan-sur',
            'poblacion', 'quibaol', 'rosario', 'sabangan', 'talogtog', 'tonton', 'tumbar', 'wawa'
        ];

        // Generate 100 comprehensive senior records
        for ($i = 1; $i <= 100; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $gender = rand(0, 1) ? 'Male' : 'Female';
            
            // Generate birth date (60-100 years old)
            $age = rand(60, 100);
            $birthYear = date('Y') - $age;
            $birthMonth = rand(1, 12);
            $birthDay = rand(1, 28);
            $dateOfBirth = Carbon::create($birthYear, $birthMonth, $birthDay);

            // Generate OSCA ID
            $oscaId = date('Y') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);

            Senior::create([
                // Section I - Basic Information
                'osca_id' => $oscaId,
                'last_name' => $lastName,
                'first_name' => $firstName,
                'middle_name' => $firstNames[array_rand($firstNames)],
                'name_extension' => rand(0, 1) ? ['Jr.', 'Sr.', 'II', 'III'][array_rand(['Jr.', 'Sr.', 'II', 'III'])] : null,
                'region' => 'Region I',
                'province' => 'Pangasinan',
                'city' => 'Lingayen',
                'barangay' => $barangays[array_rand($barangays)],
                'residence' => 'Zone ' . rand(1, 10) . ', Purok ' . rand(1, 5),
                'street' => rand(0, 1) ? 'Street ' . rand(1, 50) : null,
                'date_of_birth' => $dateOfBirth,
                'birth_place' => 'Lingayen, Pangasinan',
                'marital_status' => ['Single', 'Married', 'Widowed', 'Separated', 'Others'][array_rand(['Single', 'Married', 'Widowed', 'Separated', 'Others'])],
                'sex' => $gender,
                'contact_number' => '09' . rand(100000000, 999999999),
                'email' => rand(0, 1) ? strtolower($firstName . '.' . $lastName . '@email.com') : null,
                'religion' => ['Roman Catholic', 'Iglesia ni Cristo', 'Evangelical', 'Baptist', 'Methodist', 'Seventh Day Adventist', 'Islam', 'Buddhism', 'Jehovah\'s Witness', 'Born Again Christian', 'Aglipayan', 'None', 'Others'][array_rand(['Roman Catholic', 'Iglesia ni Cristo', 'Evangelical', 'Baptist', 'Methodist', 'Seventh Day Adventist', 'Islam', 'Buddhism', 'Jehovah\'s Witness', 'Born Again Christian', 'Aglipayan', 'None', 'Others'])],
                'ethnic_origin' => 'Filipino',
                'language' => 'Filipino, English',
                'gsis_sss' => rand(0, 1) ? rand(1000000000, 9999999999) : null,
                'tin' => rand(0, 1) ? rand(100000000, 999999999) : null,
                'philhealth' => rand(0, 1) ? rand(10000000000, 99999999999) : null,
                'sc_association' => rand(0, 1) ? 'SC-' . rand(1000, 9999) : null,
                'other_govt_id' => rand(0, 1) ? 'Voter ID: ' . rand(10000000, 99999999) : null,
                'can_travel' => rand(0, 1),
                'employment' => rand(0, 1) ? ['Retired', 'Self-employed', 'Part-time', 'Volunteer'][array_rand(['Retired', 'Self-employed', 'Part-time', 'Volunteer'])] : null,
                'has_pension' => rand(0, 1),
                'status' => rand(0, 9) ? 'active' : 'deceased',

                // Section II - Family Composition
                'spouse_last_name' => rand(0, 1) ? $lastNames[array_rand($lastNames)] : null,
                'spouse_first_name' => rand(0, 1) ? $firstNames[array_rand($firstNames)] : null,
                'spouse_middle_name' => rand(0, 1) ? $firstNames[array_rand($firstNames)] : null,
                'spouse_extension' => null,
                'father_last_name' => $lastNames[array_rand($lastNames)],
                'father_first_name' => $firstNames[array_rand($firstNames)],
                'father_middle_name' => $firstNames[array_rand($firstNames)],
                'father_extension' => null,
                'mother_last_name' => $lastNames[array_rand($lastNames)],
                'mother_first_name' => $firstNames[array_rand($firstNames)],
                'mother_middle_name' => $firstNames[array_rand($firstNames)],
                'mother_extension' => null,

                // Section III - Education / HR Profile
                'education_level' => ['Not Attended School', 'Elementary Level', 'Elementary Graduate', 'Highschool Level', 'Highschool Graduate', 'Vocational', 'College Level', 'College Graduate', 'Post Graduate'][array_rand(['Not Attended School', 'Elementary Level', 'Elementary Graduate', 'Highschool Level', 'Highschool Graduate', 'Vocational', 'College Level', 'College Graduate', 'Post Graduate'])],
                'skills' => ['Teaching', 'Cooking', 'Gardening', 'Sewing', 'Carpentry', 'Farming', 'Fishing', 'Driving', 'Computer Skills', 'Music', 'Art', 'Crafting', 'Volunteering', 'Community Service', 'Leadership', 'Mentoring', 'Counseling'],
                'shared_skills' => 'Teaching, Cooking, Gardening, Community Service',
                'community_activities' => ['Volunteer work', 'Community service', 'Church activities', 'Senior citizen activities'],

                // Section IV - Dependency Profile
                'living_condition_primary' => rand(0, 1) ? 'Living Alone' : 'Living with',
                'living_with' => ['Children', 'Spouse', 'Relatives', 'Friends'],
                'household_condition' => ['No privacy', 'Overcrowded in home', 'Informal Settler', 'No permanent house', 'High cost of rent', 'Longing for independent living quiet atmosphere'],

                // Section V - Economic Profile
                'source_of_income' => ['Own earnings, salary / wages', 'Own Pension', 'Stocks / Dividends', 'Dependent on children / relatives', 'Spouse\'s salary', 'Spouse Pension', 'Insurance', 'Rental / Sharecorp', 'Savings', 'Livestock / orchard / farm', 'Fishing'],
                'real_assets' => ['House', 'Lot / Farmland', 'Commercial building'],
                'personal_assets' => ['Vehicle', 'Appliances', 'Jewelry', 'Electronics'],
                'monthly_income' => ['60000 and above', '50000 to 60000', '40000 to 50000', '30000 to 40000', '20000 to 30000', '10000 to 20000', '5000 to 10000', '1000 to 5000', 'below 1000'][array_rand(['60000 and above', '50000 to 60000', '40000 to 50000', '30000 to 40000', '20000 to 30000', '10000 to 20000', '5000 to 10000', '1000 to 5000', 'below 1000'])],
                'problems_needs' => ['Financial assistance', 'Medical care', 'Housing', 'Transportation'],

                // Section VI - Health Profile
                'blood_type' => ['O', 'A', 'B', 'AB', 'DK'][array_rand(['O', 'A', 'B', 'AB', 'DK'])],
                'physical_disability' => rand(0, 1) ? ['None', 'Mobility issues', 'Vision problems', 'Hearing problems'][array_rand(['None', 'Mobility issues', 'Vision problems', 'Hearing problems'])] : null,
                'health_problems' => ['Hypertension', 'Arthritis / Gout', 'Coronary Heart Disease', 'Diabetes', 'Chronic Kidney Disease', 'Alzheimer\'s / Dementia', 'Chronic Obstructive Pulmonary Disease'],
                'dental_concern' => ['Needs Dental Care'],
                'visual_concern' => ['Eye impairment', 'Needs eye care'],
                'hearing_condition' => ['Aural impairment'],
                'social_emotional' => ['Feeling neglect / rejection', 'Feeling helplessness / worthlessness', 'Feeling loneliness / isolate', 'Lack leisure / recreational activities', 'Lack SC friendly environment'],
                'area_difficulty' => ['High Cost of medicines', 'Lack of medicines', 'Lack of medical attention'],
                'maintenance_medicines' => 'Amlodipine 10mg, Metformin 500mg, Simvastatin 20mg',
                'scheduled_checkup' => rand(0, 1) ? 'Yes' : 'No',
                'checkup_frequency' => rand(0, 1) ? ['Monthly', 'Quarterly', 'Semi-annually', 'Annually', 'As needed'][array_rand(['Monthly', 'Quarterly', 'Semi-annually', 'Annually', 'As needed'])] : null,
            ]);
        }

        $this->command->info('Successfully created 100 comprehensive senior records with all sections I-VI!');
    }
}