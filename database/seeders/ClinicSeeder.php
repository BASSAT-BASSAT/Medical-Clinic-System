<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ClinicSeeder extends Seeder
{
    public function run(): void
    {
        // ---------------------------
        // Specialties
        // ---------------------------
        DB::table('specialties')->insert([
            ['name' => 'Pediatrics', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Orthopedics', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Neurology', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ophthalmology', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Psychiatry', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dermatology', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cardiology', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ---------------------------
        // Doctors
        // ---------------------------
        $doctors = [
            ['first_name'=>'Ahmed','last_name'=>'Samir','specialty_id'=>1,'phone'=>'0105000006','email'=>'ahmed.samir@clinic.com'],
            ['first_name'=>'Fatma','last_name'=>'Yehia','specialty_id'=>2,'phone'=>'0105000007','email'=>'fatma.yehia@clinic.com'],
            ['first_name'=>'Sarah','last_name'=>'Mostafa','specialty_id'=>3,'phone'=>'0105000001','email'=>'sarah.mostafa@clinic.com'],
            ['first_name'=>'Omar','last_name'=>'Ibrahim','specialty_id'=>4,'phone'=>'0105000002','email'=>'omar.ibrahim@clinic.com'],
            ['first_name'=>'Laila','last_name'=>'Tarek','specialty_id'=>5,'phone'=>'0105000003','email'=>'laila.tarek@clinic.com'],
            ['first_name'=>'Mostafa','last_name'=>'Adel','specialty_id'=>6,'phone'=>'0105000004','email'=>'mostafa.adel@clinic.com'],
            ['first_name'=>'Nour','last_name'=>'Hany','specialty_id'=>7,'phone'=>'0105000005','email'=>'nour.hany@clinic.com'],
        ];

        foreach ($doctors as $doctor) {
            // Create User
            $user = User::create([
                'name' => $doctor['first_name'].' '.$doctor['last_name'],
                'email' => $doctor['email'],
                'password' => Hash::make('password123'), // default password
                'role' => 'doctor',
            ]);

            // Insert Doctor linked to User
            DB::table('doctors')->insert(array_merge($doctor, [
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ---------------------------
        // Patients
        // ---------------------------
        $patients = [
            ['first_name'=>'Hossam','last_name'=>'Zaki','dob'=>'1985-02-15','phone'=>'0152000004','email'=>'hossam@mail.com'],
            ['first_name'=>'Mariam','last_name'=>'Fathy','dob'=>'1995-06-13','phone'=>'0152000001','email'=>'mariam@mail.com'],
            ['first_name'=>'Adel','last_name'=>'Gamal','dob'=>'2000-03-20','phone'=>'0152000002','email'=>'adel@mail.com'],
            ['first_name'=>'Yara','last_name'=>'Kamel','dob'=>'1992-12-05','phone'=>'0152000003','email'=>'yara@mail.com'],
            ['first_name'=>'Kareem','last_name'=>'Nasser','dob'=>'1989-07-30','phone'=>'0152000009','email'=>'kareem.nasser@mail.com'],
            ['first_name'=>'Nadine','last_name'=>'Fouad','dob'=>'1998-08-10','phone'=>'0152000005','email'=>'nadine@mail.com'],
            ['first_name'=>'Omar','last_name'=>'Saeed','dob'=>'1990-09-11','phone'=>'0152000006','email'=>'omar.saeed@mail.com'],
            ['first_name'=>'Salma','last_name'=>'Youssef','dob'=>'1997-01-25','phone'=>'0152000007','email'=>'salma.youssef@mail.com'],
            ['first_name'=>'Farah','last_name'=>'Tamer','dob'=>'2001-04-09','phone'=>'0152000008','email'=>'farah.tamer@mail.com'],
            ['first_name'=>'Dina','last_name'=>'Adham','dob'=>'1996-03-18','phone'=>'0152000010','email'=>'dina.adham@mail.com'],
        ];

        foreach ($patients as $patient) {
            // Create User
            $user = User::create([
                'name' => $patient['first_name'].' '.$patient['last_name'],
                'email' => $patient['email'],
                'password' => Hash::make('password123'), // default password
                'role' => 'patient',
            ]);

            // Insert Patient linked to User
            DB::table('patients')->insert(array_merge($patient, [
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
