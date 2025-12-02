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
        // Admin User
        // ---------------------------
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@clinic.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

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

        // ---------------------------
        // Doctor Availability (for all doctors)
        // ---------------------------
        $doctorIds = DB::table('doctors')->pluck('doctor_id');
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        
        foreach ($doctorIds as $doctorId) {
            foreach ($days as $day) {
                DB::table('doctor_availability')->insert([
                    'doctor_id' => $doctorId,
                    'day_of_week' => $day,
                    'start_time' => '09:00:00',
                    'end_time' => '17:00:00',
                    'is_available' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ---------------------------
        // Sample Appointments (Past - Completed)
        // ---------------------------
        $patientIds = DB::table('patients')->pluck('patient_id');
        
        // Past completed appointments for medical records
        $pastAppointments = [
            ['patient_id' => 1, 'doctor_id' => 7, 'days_ago' => 30, 'reason' => 'Annual Heart Checkup', 'status' => 'completed'],
            ['patient_id' => 1, 'doctor_id' => 6, 'days_ago' => 45, 'reason' => 'Skin Rash Examination', 'status' => 'completed'],
            ['patient_id' => 1, 'doctor_id' => 3, 'days_ago' => 60, 'reason' => 'Migraine Consultation', 'status' => 'completed'],
            ['patient_id' => 2, 'doctor_id' => 1, 'days_ago' => 15, 'reason' => 'Child Vaccination', 'status' => 'completed'],
            ['patient_id' => 3, 'doctor_id' => 2, 'days_ago' => 20, 'reason' => 'Knee Pain Assessment', 'status' => 'completed'],
        ];

        foreach ($pastAppointments as $apt) {
            $startTime = now()->subDays($apt['days_ago'])->setHour(10)->setMinute(0)->setSecond(0);
            $endTime = (clone $startTime)->addHour();
            
            $appointmentId = DB::table('appointments')->insertGetId([
                'patient_id' => $apt['patient_id'],
                'doctor_id' => $apt['doctor_id'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => $apt['status'],
                'reason' => $apt['reason'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create medical record for completed appointments
            if ($apt['status'] === 'completed') {
                DB::table('medical_records')->insert([
                    'patient_id' => $apt['patient_id'],
                    'doctor_id' => $apt['doctor_id'],
                    'appointment_id' => $appointmentId,
                    'record_date' => $startTime,
                    'notes' => $this->getMedicalNotes($apt['reason']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ---------------------------
        // Sample Notifications
        // ---------------------------
        DB::table('notifications')->insert([
            [
                'appointment_id' => 1,
                'patient_id' => 1,
                'doctor_id' => 7,
                'type' => 'email',
                'notification_type' => 'booking_confirmation',
                'message' => 'Your appointment with Dr. Nour Hany has been confirmed for your Annual Heart Checkup.',
                'is_sent' => true,
                'sent_at' => now()->subDays(30),
                'created_at' => now()->subDays(30),
                'updated_at' => now(),
            ],
            [
                'appointment_id' => 2,
                'patient_id' => 1,
                'doctor_id' => 6,
                'type' => 'email',
                'notification_type' => 'appointment_completed',
                'message' => 'Your appointment with Dr. Mostafa Adel has been completed. Medical records have been updated.',
                'is_sent' => false,
                'sent_at' => null,
                'created_at' => now()->subDays(45),
                'updated_at' => now(),
            ],
            [
                'appointment_id' => 3,
                'patient_id' => 1,
                'doctor_id' => 3,
                'type' => 'email',
                'notification_type' => 'appointment_reminder',
                'message' => 'Reminder: Please follow up on your migraine treatment as discussed with Dr. Sarah Mostafa.',
                'is_sent' => false,
                'sent_at' => null,
                'created_at' => now()->subDays(7),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Get sample medical notes based on appointment reason
     */
    private function getMedicalNotes(string $reason): string
    {
        $notes = [
            'Annual Heart Checkup' => "Patient examined. Blood pressure: 120/80 mmHg. Heart rate: 72 bpm.\n\nECG Results: Normal sinus rhythm, no abnormalities detected.\n\nRecommendations:\n- Continue regular exercise (30 min daily)\n- Maintain healthy diet, reduce sodium intake\n- Follow up in 6 months\n\nPrescription: None required at this time.",
            
            'Skin Rash Examination' => "Physical examination of affected area completed.\n\nDiagnosis: Contact dermatitis, likely allergic reaction.\n\nTreatment Plan:\n- Hydrocortisone cream 1% - apply twice daily for 7 days\n- Antihistamine (Cetirizine 10mg) - once daily for 5 days\n- Avoid potential allergens\n\nFollow up if symptoms persist after 1 week.",
            
            'Migraine Consultation' => "Patient reports frequent migraines (3-4 times per week) lasting 4-6 hours.\n\nTriggers identified: Stress, irregular sleep, screen time.\n\nTreatment Plan:\n- Sumatriptan 50mg - take at onset of migraine\n- Preventive: Propranolol 40mg daily\n- Maintain sleep schedule\n- Reduce screen time\n\nRefer to neurologist if no improvement in 4 weeks.",
            
            'Child Vaccination' => "Routine vaccination administered as per schedule.\n\nVaccines given:\n- DTaP (Diphtheria, Tetanus, Pertussis)\n- IPV (Polio)\n\nChild tolerated procedure well. No immediate adverse reactions.\n\nNext vaccination due: 6 months from today.\n\nAdvice given to parents regarding post-vaccination care.",
            
            'Knee Pain Assessment' => "Patient complains of persistent knee pain, worse after physical activity.\n\nPhysical Examination:\n- Mild swelling observed\n- Limited range of motion\n- McMurray test: Negative\n\nX-ray findings: Early signs of osteoarthritis.\n\nTreatment Plan:\n- Ibuprofen 400mg - twice daily with food for 10 days\n- Physical therapy recommended - 2 sessions per week\n- Ice application after activity\n- Follow up in 3 weeks",
        ];

        return $notes[$reason] ?? "Patient examination completed. Findings documented. Follow up as needed.";
    }
}
