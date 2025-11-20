<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClinicSeeder extends Seeder
{
    public function run(): void
    {
        // Specialties
        DB::table('specialties')->insert([
            ['name' => 'Pediatrics', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Orthopedics', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Neurology', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ophthalmology', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Psychiatry', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dermatology', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cardiology', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Doctors
        DB::table('doctors')->insert([
            ['first_name'=>'Ahmed','last_name'=>'Samir','specialty_id'=>1,'phone'=>'0105000006','email'=>'ahmed.samir@clinic.com','created_at' => now(), 'updated_at' => now()],
            ['first_name'=>'Fatma','last_name'=>'Yehia','specialty_id'=>2,'phone'=>'0105000007','email'=>'fatma.yehia@clinic.com','created_at' => now(), 'updated_at' => now()],
            ['first_name'=>'Sarah','last_name'=>'Mostafa','specialty_id'=>3,'phone'=>'0105000001','email'=>'sarah.mostafa@clinic.com','created_at' => now(), 'updated_at' => now()],
            ['first_name'=>'Omar','last_name'=>'Ibrahim','specialty_id'=>4,'phone'=>'0105000002','email'=>'omar.ibrahim@clinic.com','created_at' => now(), 'updated_at' => now()],
            ['first_name'=>'Laila','last_name'=>'Tarek','specialty_id'=>5,'phone'=>'0105000003','email'=>'laila.tarek@clinic.com','created_at' => now(), 'updated_at' => now()],
            ['first_name'=>'Mostafa','last_name'=>'Adel','specialty_id'=>6,'phone'=>'0105000004','email'=>'mostafa.adel@clinic.com','created_at' => now(), 'updated_at' => now()],
            ['first_name'=>'Nour','last_name'=>'Hany','specialty_id'=>7,'phone'=>'0105000005','email'=>'nour.hany@clinic.com','created_at' => now(), 'updated_at' => now()],
        ]);

        // Patients
        DB::table('patients')->insert([
            ['first_name'=>'Hossam','last_name'=>'Zaki','dob'=>'1985-02-15','phone'=>'0152000004','email'=>'hossam@mail.com','created_at' => now(), 'updated_at' => now()],
            ['first_name'=>'Mariam','last_name'=>'Fathy','dob'=>'1995-06-13','phone'=>'0152000001','email'=>'mariam@mail.com','created_at' => now(), 'updated_at' => now()],
            ['first_name'=>'Adel','last_name'=>'Gamal','dob'=>'2000-03-20','phone'=>'0152000002','email'=>'adel@mail.com','created_at' => now(), 'updated_at' => now()],
            ['first_name'=>'Yara','last_name'=>'Kamel','dob'=>'1992-12-05','phone'=>'0152000003','email'=>'yara@mail.com','created_at' => now(), 'updated_at' => now()],
            ['first_name'=>'Kareem','last_name'=>'Nasser','dob'=>'1989-07-30','phone'=>'0152000009','email'=>'kareem.nasser@mail.com','created_at' => now(), 'updated_at' => now()],
            ['first_name'=>'Nadine','last_name'=>'Fouad','dob'=>'1998-08-10','phone'=>'0152000005','email'=>'nadine@mail.com','created_at' => now(), 'updated_at' => now()],
            ['first_name'=>'Omar','last_name'=>'Saeed','dob'=>'1990-09-11','phone'=>'0152000006','email'=>'omar.saeed@mail.com','created_at' => now(), 'updated_at' => now()],
            ['first_name'=>'Salma','last_name'=>'Youssef','dob'=>'1997-01-25','phone'=>'0152000007','email'=>'salma.youssef@mail.com','created_at' => now(), 'updated_at' => now()],
            ['first_name'=>'Farah','last_name'=>'Tamer','dob'=>'2001-04-09','phone'=>'0152000008','email'=>'farah.tamer@mail.com','created_at' => now(), 'updated_at' => now()],
            ['first_name'=>'Dina','last_name'=>'Adham','dob'=>'1996-03-18','phone'=>'0152000010','email'=>'dina.adham@mail.com','created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
