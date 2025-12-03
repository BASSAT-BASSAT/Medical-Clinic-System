# 🏥 Medical Clinic Backend - Presentation Guide

## Overview
This is a comprehensive guide to the **Laravel 12** backend of the Medical Clinic Appointment System. Use this document to prepare your presentation.

---

# 📑 PART 1: Project Architecture & Structure

## 1.1 Technology Stack
| Component | Technology | Version |
|-----------|------------|---------|
| Framework | Laravel | 12.x |
| Language | PHP | 8.2+ |
| Database | SQLite | (can be MySQL/PostgreSQL) |
| Authentication | Laravel Breeze | Built-in |
| Email | Laravel Mail | Queue-based |

## 1.2 Directory Structure
```
app/
├── Console/Commands/        # Scheduled tasks (reminders)
├── Events/                  # Event classes (AppointmentCreated, etc.)
├── Http/
│   ├── Controllers/         # Business logic handlers
│   ├── Middleware/          # Request filters (RoleMiddleware)
│   └── Requests/            # Form validation
├── Listeners/               # Event handlers (send emails)
├── Mail/                    # Email templates
├── Models/                  # Database entities (Eloquent ORM)
├── Providers/               # Service registration
├── Services/                # Business services (NotificationService)
└── View/Components/         # Blade components

database/
├── migrations/              # Database schema definitions
├── seeders/                 # Test data generators
└── factories/               # Model factories for testing

routes/
├── api.php                  # REST API endpoints
├── web.php                  # Web routes with authentication
└── auth.php                 # Authentication routes
```

## 1.3 Design Patterns Used
1. **MVC (Model-View-Controller)** - Core Laravel pattern
2. **Repository Pattern** - Models abstract database access
3. **Event-Driven Architecture** - Events & Listeners for emails
4. **Service Layer** - NotificationService for complex logic
5. **Middleware Pattern** - Request filtering (auth, roles)

---

# 📑 PART 2: Database Design (ERD)

## 2.1 Entity Relationship Diagram

```
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│   USERS     │       │  SPECIALTIES│       │             │
├─────────────┤       ├─────────────┤       │             │
│ id (PK)     │       │ specialty_id│◄──────┤             │
│ name        │       │ name        │   1:N │             │
│ email       │       └─────────────┘       │             │
│ password    │                             │             │
│ role        │──┐                          │             │
└─────────────┘  │                          │             │
                 │ 1:1                      │             │
┌────────────────┴──────────────┐           │             │
│                               │           │             │
▼                               ▼           │             │
┌─────────────┐           ┌─────────────┐   │             │
│  PATIENTS   │           │   DOCTORS   │───┘             │
├─────────────┤           ├─────────────┤                 │
│ patient_id  │           │ doctor_id   │                 │
│ user_id(FK) │           │ user_id(FK) │                 │
│ first_name  │           │ first_name  │                 │
│ last_name   │           │ last_name   │                 │
│ dob         │           │ specialty_id│                 │
│ phone       │           │ phone       │                 │
│ email       │           │ email       │                 │
└──────┬──────┘           └──────┬──────┘                 │
       │                         │                        │
       │ 1:N                     │ 1:N                    │
       │    ┌─────────────┐      │                        │
       │    │APPOINTMENTS │      │                        │
       └───►├─────────────┤◄─────┘                        │
            │appointment_id                               │
            │ patient_id   │                              │
            │ doctor_id    │                              │
            │ start_time   │                              │
            │ end_time     │                              │
            │ status       │                              │
            │ reason       │                              │
            └──────┬───────┘                              │
                   │                                      │
         ┌─────────┴─────────┐                           │
         │ 1:N           1:N │                           │
         ▼                   ▼                           │
┌─────────────────┐  ┌─────────────────┐                │
│ MEDICAL_RECORDS │  │  NOTIFICATIONS  │                │
├─────────────────┤  ├─────────────────┤                │
│ record_id       │  │ notification_id │                │
│ patient_id      │  │ appointment_id  │                │
│ doctor_id       │  │ patient_id      │                │
│ appointment_id  │  │ doctor_id       │                │
│ record_date     │  │ type            │                │
│ diagnosis       │  │ notification_type                │
│ prescription    │  │ message         │                │
│ notes           │  │ is_sent         │                │
└─────────────────┘  │ sent_at         │                │
                     └─────────────────┘                │
                                                        │
┌───────────────────────┐                               │
│  DOCTOR_AVAILABILITY  │◄──────────────────────────────┘
├───────────────────────┤           1:N
│ availability_id       │
│ doctor_id             │
│ day_of_week           │
│ start_time            │
│ end_time              │
│ is_available          │
│ is_overnight          │
└───────────────────────┘
```

## 2.2 Table Descriptions

| Table | Purpose | Key Fields |
|-------|---------|------------|
| `users` | Authentication & authorization | role (admin/doctor/patient) |
| `patients` | Patient profiles | dob, contact info |
| `doctors` | Doctor profiles | specialty_id (FK) |
| `specialties` | Medical specializations | Cardiology, Dermatology, etc. |
| `appointments` | Booking records | status (scheduled/completed/cancelled) |
| `medical_records` | Patient health history | diagnosis, prescription |
| `doctor_availability` | Weekly schedule | day_of_week, time slots |
| `notifications` | Email/SMS tracking | is_sent, sent_at |

---

# 📑 PART 3: Models & Relationships (Eloquent ORM)

## 3.1 What is Eloquent ORM?
- **ORM** = Object-Relational Mapping
- Each database table has a corresponding **Model** class
- Models handle CRUD operations without writing SQL
- Relationships are defined as PHP methods

## 3.2 Model Examples

### User Model
```php
class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'role'];
    
    protected $hidden = ['password', 'remember_token'];
    
    // Role checking methods
    public function isAdmin() { return $this->role === 'admin'; }
    public function isDoctor() { return $this->role === 'doctor'; }
    public function isPatient() { return $this->role === 'patient'; }
    
    // Relationships
    public function doctor() { return $this->hasOne(Doctor::class); }
    public function patient() { return $this->hasOne(Patient::class); }
}
```

### Appointment Model
```php
class Appointment extends Model
{
    protected $primaryKey = 'appointment_id';
    
    protected $fillable = [
        'patient_id', 'doctor_id', 
        'start_time', 'end_time', 
        'status', 'reason'
    ];
    
    // Relationships
    public function patient(): BelongsTo {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
    
    public function doctor(): BelongsTo {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
    
    public function medicalRecords(): HasMany {
        return $this->hasMany(MedicalRecord::class, 'appointment_id');
    }
}
```

## 3.3 Relationship Types Used

| Relationship | Example | Explanation |
|--------------|---------|-------------|
| `hasOne` | User → Doctor | A user has one doctor profile |
| `belongsTo` | Doctor → Specialty | A doctor belongs to one specialty |
| `hasMany` | Patient → Appointments | A patient has many appointments |
| `belongsTo` (inverse) | Appointment → Patient | An appointment belongs to one patient |

---

# 📑 PART 4: Controllers & API Endpoints

## 4.1 Controller Overview

| Controller | Purpose | Type |
|------------|---------|------|
| `AppointmentController` | CRUD for appointments + scheduling logic | API + Web |
| `DoctorController` | Doctor management + dashboard | API + Web |
| `PatientController` | Patient management + dashboard | API + Web |
| `SpecialtyController` | Medical specialties CRUD | API |
| `MedicalRecordController` | Health records management | API |
| `NotificationController` | Notification management | API |
| `ReportController` | Statistics & reporting | API |
| `AdminController` | Admin dashboard | Web |
| `ProfileController` | User profile management | Web |

## 4.2 REST API Endpoints

### Specialties
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/specialties` | List all specialties |
| POST | `/api/specialties` | Create specialty |
| GET | `/api/specialties/{id}` | Get single specialty |
| PUT | `/api/specialties/{id}` | Update specialty |
| DELETE | `/api/specialties/{id}` | Delete specialty |

### Doctors
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/doctors` | List all doctors |
| GET | `/api/doctors/{id}` | Get doctor details |
| GET | `/api/doctors/{id}/appointments` | Doctor's appointments |
| GET | `/api/specialties/{id}/doctors` | Doctors by specialty |
| GET | `/api/doctors/{id}/availability` | Doctor's schedule |
| GET | `/api/doctors/{id}/available-slots/{date}` | Available time slots |

### Patients
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/patients` | List all patients |
| GET | `/api/patients/{id}` | Get patient details |
| GET | `/api/patients/{id}/appointments` | Patient's appointments |
| GET | `/api/patients/{id}/medical-records` | Patient's records |

### Appointments
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/appointments` | List all appointments |
| POST | `/api/appointments` | **Book new appointment** |
| GET | `/api/appointments/{id}` | Get appointment details |
| PUT | `/api/appointments/{id}` | Update/Cancel appointment |
| DELETE | `/api/appointments/{id}` | Delete appointment |
| GET | `/api/appointments/by-date/{date}` | Appointments on date |
| GET | `/api/appointments/by-doctor/{id}` | Doctor's appointments |

## 4.3 Key Controller Logic

### Appointment Booking (AppointmentController@store)
```php
public function store(Request $request)
{
    // 1. Validate input
    $validated = $request->validate([
        'patient_id' => 'required|exists:patients,patient_id',
        'doctor_id' => 'required|exists:doctors,doctor_id',
        'start_time' => 'required|date_format:"Y-m-d H:i:s"',
        'end_time' => 'required|date_format:"Y-m-d H:i:s"',
        'reason' => 'nullable|string|max:500',
    ]);
    
    // 2. Check duration (max 4 hours)
    if ($end->diffInHours($start) > 4) {
        return response()->json(['error' => 'Too long'], 422);
    }
    
    // 3. Check doctor conflicts
    $conflict = Appointment::where('doctor_id', $doctorId)
        ->where('status', '!=', 'cancelled')
        ->where('start_time', '<', $endTime)
        ->where('end_time', '>', $startTime)
        ->first();
    
    // 4. Check patient conflicts
    // 5. Create appointment
    // 6. Fire event for email notification
}
```

### Available Slots Logic
```php
public function availableSlots($doctorId, $date)
{
    // 1. Get doctor's availability for that day
    $dayOfWeek = Carbon::parse($date)->format('l'); // "Monday"
    $availability = DoctorAvailability::where('doctor_id', $doctorId)
        ->where('day_of_week', $dayOfWeek)
        ->where('is_available', true)
        ->first();
    
    // 2. Get existing appointments on that date
    $appointments = Appointment::where('doctor_id', $doctorId)
        ->whereDate('start_time', $date)
        ->where('status', '!=', 'cancelled')
        ->get();
    
    // 3. Generate 1-hour slots within availability
    // 4. Filter out booked slots
    // 5. Return available slots
}
```

---

# 📑 PART 5: Authentication & Authorization

## 5.1 Authentication (Laravel Breeze)
- Uses **session-based** authentication for web routes
- Password hashing with **bcrypt**
- Email verification support
- "Remember me" functionality

## 5.2 Role-Based Access Control (RBAC)

### Roles
| Role | Access Level |
|------|--------------|
| `admin` | Full system access |
| `doctor` | Own appointments, patients, records |
| `patient` | Own appointments, records, booking |

### RoleMiddleware
```php
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Check if logged in
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        // Check if correct role
        if (auth()->user()->role !== $role) {
            // Redirect to appropriate dashboard
            return redirect()->route(auth()->user()->role . '.dashboard');
        }
        
        return $next($request);
    }
}
```

### Route Protection
```php
// Only patients can access
Route::middleware(['auth', 'role:patient'])->group(function(){
    Route::get('/patient/dashboard', [PatientController::class, 'dashboard']);
    Route::get('/patient/appointments/book', [PatientController::class, 'bookAppointment']);
});

// Only doctors can access
Route::middleware(['auth', 'role:doctor'])->group(function(){
    Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard']);
    Route::get('/doctor/patients', [DoctorController::class, 'patients']);
});

// Only admins can access
Route::middleware(['auth', 'role:admin'])->group(function(){
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});
```

---

# 📑 PART 6: Events & Notifications System

## 6.1 Event-Driven Architecture

```
┌──────────────────┐     ┌───────────────┐     ┌─────────────────┐
│ Controller       │────►│ Event         │────►│ Listener        │
│ (creates appt)   │     │ (dispatched)  │     │ (sends email)   │
└──────────────────┘     └───────────────┘     └─────────────────┘

Example:
AppointmentController ──► AppointmentCreated ──► SendAppointmentConfirmation
```

## 6.2 Events Defined

| Event | When Triggered |
|-------|----------------|
| `AppointmentCreated` | New appointment booked |
| `AppointmentCancelled` | Appointment cancelled |
| `AppointmentCompleted` | Appointment marked done |

### Event Class Example
```php
class AppointmentCreated
{
    use Dispatchable, SerializesModels;
    
    public function __construct(
        public Appointment $appointment
    ) {}
}
```

## 6.3 Listeners (Event Handlers)

```php
class SendAppointmentConfirmation
{
    public function handle(AppointmentCreated $event)
    {
        $appointment = $event->appointment;
        
        // Send confirmation email
        Mail::to($appointment->patient->email)
            ->send(new AppointmentConfirmation($appointment));
    }
}
```

## 6.4 Event-Listener Registration
```php
// EventServiceProvider.php
protected $listen = [
    AppointmentCreated::class => [
        SendAppointmentConfirmation::class,
    ],
    AppointmentCancelled::class => [
        SendAppointmentCancellationNotice::class,
    ],
];
```

## 6.5 Email Templates (Mailables)
```php
class AppointmentConfirmation extends Mailable implements ShouldQueue
{
    public function __construct(public Appointment $appointment) {}
    
    public function envelope(): Envelope {
        return new Envelope(subject: 'Appointment Confirmation');
    }
    
    public function content(): Content {
        return new Content(
            view: 'emails.appointment-confirmation',
            with: [
                'appointment' => $this->appointment,
                'patient' => $this->appointment->patient,
                'doctor' => $this->appointment->doctor,
            ],
        );
    }
}
```

---

# 📑 PART 7: Services Layer

## 7.1 NotificationService
Centralizes notification logic for reusability:

```php
class NotificationService
{
    // Send reminders for tomorrow's appointments
    public static function sendAppointmentReminders()
    {
        $tomorrow = Carbon::tomorrow();
        
        $appointments = Appointment::whereBetween('start_time', 
            [$tomorrow->startOfDay(), $tomorrow->endOfDay()])
            ->where('status', 'scheduled')
            ->with('patient', 'doctor')
            ->get();
        
        foreach ($appointments as $appointment) {
            Mail::to($appointment->patient->email)
                ->send(new AppointmentReminder($appointment));
        }
    }
    
    // Process queued notifications
    public static function sendUnsentNotifications() { ... }
    
    // Create notification record
    public static function createNotificationForAppointment(...) { ... }
}
```

---

# 📑 PART 8: Scheduled Tasks (Console Commands)

## 8.1 Custom Artisan Commands

### SendAppointmentReminders
```php
class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Send reminder emails for tomorrow appointments';
    
    public function handle()
    {
        $count = NotificationService::sendAppointmentReminders();
        $this->info("Sent $count reminder emails");
    }
}
```

### Usage
```bash
# Manual execution
php artisan appointments:send-reminders

# Scheduled (in app/Console/Kernel.php)
$schedule->command('appointments:send-reminders')->dailyAt('18:00');
```

---

# 📑 PART 9: Validation & Error Handling

## 9.1 Request Validation
```php
$validated = $request->validate([
    'patient_id' => 'required|exists:patients,patient_id',
    'doctor_id' => 'required|exists:doctors,doctor_id',
    'start_time' => 'required|date_format:"Y-m-d H:i:s"',
    'end_time' => 'required|date_format:"Y-m-d H:i:s"|after:start_time',
    'status' => 'sometimes|in:scheduled,completed,cancelled',
    'reason' => 'nullable|string|max:500',
]);
```

## 9.2 Validation Rules Used

| Rule | Purpose |
|------|---------|
| `required` | Field must be present |
| `exists:table,column` | Foreign key must exist |
| `date_format` | Must match datetime format |
| `after:field` | Date must be after another field |
| `in:a,b,c` | Must be one of specified values |
| `email` | Must be valid email format |
| `unique:table,column` | Must not exist in database |
| `max:N` | Maximum string length |

## 9.3 Error Responses
```php
// Validation error
return response()->json(['error' => 'Validation error'], 422);

// Conflict (double booking)
return response()->json(['error' => 'Doctor has a conflicting appointment'], 409);

// Not found
return response()->json(['error' => 'Resource not found'], 404);

// Server error
return response()->json(['error' => 'Internal server error'], 500);
```

---

# 📑 PART 10: API Response Format

## 10.1 Success Responses

### List (with pagination)
```json
{
    "data": [...],
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 73
}
```

### Single Resource
```json
{
    "appointment_id": 1,
    "patient_id": 1,
    "doctor_id": 2,
    "start_time": "2025-12-04 10:00:00",
    "end_time": "2025-12-04 11:00:00",
    "status": "scheduled",
    "patient": { ... },
    "doctor": { ... }
}
```

### Available Slots
```json
{
    "slots": ["09:00", "10:00", "11:00", "14:00", "15:00"]
}
```

## 10.2 Error Response
```json
{
    "error": "Doctor has a conflicting appointment at this time"
}
```

---

# 📑 Summary: Key Concepts for Presentation

## 🎯 Main Topics to Cover

1. **Laravel MVC Architecture**
   - Models = Database tables
   - Views = HTML templates (Blade)
   - Controllers = Business logic

2. **Eloquent ORM**
   - No raw SQL needed
   - Relationships (hasMany, belongsTo)
   - Query builder

3. **RESTful API Design**
   - HTTP methods (GET, POST, PUT, DELETE)
   - Resource-based URLs
   - JSON responses

4. **Authentication & Authorization**
   - Session-based login
   - Role middleware
   - Protected routes

5. **Event-Driven Architecture**
   - Decoupled components
   - Events → Listeners
   - Email notifications

6. **Database Design**
   - Migrations (version control for DB)
   - Foreign keys & relationships
   - Indexes for performance

7. **Business Logic Highlights**
   - Conflict checking for bookings
   - Available slot calculation
   - Notification queuing

## 📊 Statistics
- **8 Models** (database tables)
- **11 Controllers** 
- **3 Events** + **2 Listeners**
- **3 Mailable classes**
- **90+ API endpoints**
- **3 User roles**

---

# 🎤 Presentation Tips

1. **Start with the ERD** - Visual helps understanding
2. **Show code snippets** - Keep them short (5-10 lines)
3. **Demo the API** - Use Postman or curl
4. **Explain the flow** - User books → Controller validates → Event fires → Email sent
5. **Highlight Laravel features** - ORM, Migrations, Events, Mail

Good luck with your presentation! 🚀
