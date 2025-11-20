# Medical Clinic API Documentation

## Overview
This is a Laravel-based medical clinic management system with complete API endpoints for managing doctors, patients, appointments, medical records, and notifications.

## Setup Complete ✅

- ✅ Database configured (SQLite)
- ✅ 6 Eloquent Models created
- ✅ 6 Controllers with full CRUD operations
- ✅ API routes configured
- ✅ Test data seeded (7 doctors, 10 patients, 7 specialties)

## Controllers & Methods

### DoctorController
```
GET    /api/doctors                      - List all doctors
GET    /api/doctors/{id}                 - Get specific doctor
POST   /api/doctors                      - Create new doctor
PUT    /api/doctors/{id}                 - Update doctor
DELETE /api/doctors/{id}                 - Delete doctor
GET    /api/doctors/{id}/appointments    - Get doctor's appointments
GET    /api/specialties/{id}/doctors     - Get doctors by specialty
```

**Doctor Fields:**
- first_name (required)
- last_name (required)
- specialty_id (required)
- phone (optional)
- email (required, unique)

### PatientController
```
GET    /api/patients                     - List all patients
GET    /api/patients/{id}                - Get specific patient
POST   /api/patients                     - Create new patient
PUT    /api/patients/{id}                - Update patient
DELETE /api/patients/{id}                - Delete patient
GET    /api/patients/{id}/appointments   - Get patient's appointments
GET    /api/patients/{id}/medical-records - Get patient's medical records
```

**Patient Fields:**
- first_name (required)
- last_name (required)
- dob (optional, date format)
- phone (optional)
- email (optional, unique)

### SpecialtyController
```
GET    /api/specialties                  - List all specialties
GET    /api/specialties/{id}             - Get specific specialty
POST   /api/specialties                  - Create new specialty
PUT    /api/specialties/{id}             - Update specialty
DELETE /api/specialties/{id}             - Delete specialty
```

**Specialty Fields:**
- name (required, unique)

### AppointmentController
```
GET    /api/appointments                           - List all appointments
GET    /api/appointments/{id}                      - Get specific appointment
POST   /api/appointments                           - Create new appointment
PUT    /api/appointments/{id}                      - Update appointment
DELETE /api/appointments/{id}                      - Delete appointment
GET    /api/appointments/by-date/{date}            - Get appointments by date
GET    /api/appointments/by-doctor/{doctorId}      - Get doctor's appointments
GET    /api/appointments/by-patient/{patientId}    - Get patient's appointments
GET    /api/doctors/{doctorId}/available-slots/{date} - Get available time slots
```

**Appointment Fields:**
- patient_id (required)
- doctor_id (required)
- start_time (required, format: Y-m-d H:i:s)
- end_time (required, format: Y-m-d H:i:s, must be after start_time)
- status (optional, values: scheduled, completed, cancelled)
- reason (optional)

### MedicalRecordController
```
GET    /api/medical-records                              - List all records
GET    /api/medical-records/{id}                         - Get specific record
POST   /api/medical-records                              - Create new record
PUT    /api/medical-records/{id}                         - Update record
DELETE /api/medical-records/{id}                         - Delete record
GET    /api/medical-records/by-patient/{patientId}       - Get patient's records
GET    /api/medical-records/by-appointment/{appointmentId} - Get appointment's records
```

**Medical Record Fields:**
- patient_id (required)
- doctor_id (required)
- appointment_id (required)
- record_date (optional, format: Y-m-d H:i:s)
- notes (optional, text)

### NotificationController
```
GET    /api/notifications                     - List all notifications
GET    /api/notifications/{id}                - Get specific notification
POST   /api/notifications                     - Create new notification
PUT    /api/notifications/{id}                - Update notification
DELETE /api/notifications/{id}                - Delete notification
GET    /api/notifications/pending             - Get pending notifications
POST   /api/notifications/{id}/mark-as-sent   - Mark notification as sent
```

**Notification Fields:**
- appointment_id (required)
- method (required, values: email, sms, push)
- message (optional)
- sent_at (optional, format: Y-m-d H:i:s)
- status (optional, values: pending, sent, failed)

## Running the Application

### Start the development server
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### Access API endpoints
```
http://localhost:8000/api/doctors
http://localhost:8000/api/patients
http://localhost:8000/api/appointments
```

## Example API Requests

### Get all doctors
```bash
curl http://localhost:8000/api/doctors
```

### Create a new appointment
```bash
curl -X POST http://localhost:8000/api/appointments \
  -H "Content-Type: application/json" \
  -d '{
    "patient_id": 1,
    "doctor_id": 1,
    "start_time": "2025-11-25 10:00:00",
    "end_time": "2025-11-25 11:00:00",
    "reason": "Check-up"
  }'
```

### Get available appointment slots
```bash
curl http://localhost:8000/api/doctors/1/available-slots/2025-11-25
```

### Create medical record
```bash
curl -X POST http://localhost:8000/api/medical-records \
  -H "Content-Type: application/json" \
  -d '{
    "patient_id": 1,
    "doctor_id": 1,
    "appointment_id": 1,
    "notes": "Patient is in good health"
  }'
```

## Database Schema

### Tables Created:
- `specialties` - Medical specialties
- `doctors` - Doctor information with specialty foreign key
- `patients` - Patient information
- `appointments` - Appointment records linking doctors and patients
- `medical_records` - Medical notes from appointments
- `notifications` - Appointment notifications

### Views Created:
- `DoctorDailySchedule` - Daily schedule for doctors
- `PatientHistory` - Complete history for patients
- `AppointmentsPerDay` - Appointment count per day

## Helper Scripts

### Verify Database
```bash
php check_database.php
```

### Verify Controllers
```bash
php verify_controllers.php
```

## Features

✅ **Full CRUD operations** for all entities
✅ **Relationship management** (doctors to specialties, appointments to doctors/patients)
✅ **Conflict detection** for double-booked appointments
✅ **Available slots** calculation for doctors
✅ **Pagination support** for list endpoints
✅ **Validation** on all inputs
✅ **Database relationships** properly configured
✅ **JSON API responses** for easy integration

---

**Ready to manage your clinic! 🏥**
