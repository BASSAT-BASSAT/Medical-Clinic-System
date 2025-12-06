# 🏥 Medical Clinic Backend - Presentation Guide

## Overview
This is a comprehensive guide to the **Laravel 12** backend of the Medical Clinic Appointment System. Use this document to prepare your presentation.

---

# 🔥 LARAVEL FOR FASTAPI DEVELOPERS (READ THIS FIRST!)

If you know FastAPI, here's Laravel explained in terms you'll understand:

## Quick Comparison Table

| Concept | FastAPI (Python) | Laravel (PHP) |
|---------|------------------|---------------|
| Entry point | `main.py` | `routes/api.php` + `routes/web.php` |
| Route definition | `@app.get("/users")` | `Route::get('/users', [Controller::class, 'index'])` |
| Request handling | Function with params | Controller method |
| Database ORM | SQLAlchemy | Eloquent |
| Models | Pydantic models | Eloquent Models |
| Migrations | Alembic | Laravel Migrations |
| Dependency Injection | `Depends()` | Service Container (automatic) |
| Validation | Pydantic schemas | `$request->validate([...])` |
| Background tasks | Celery / BackgroundTasks | Events & Listeners / Queues |
| Middleware | `@app.middleware` | Middleware classes |

---

## 🎯 Side-by-Side Code Examples

### 1️⃣ DEFINING A ROUTE

**FastAPI:**
```python
# main.py
from fastapi import FastAPI
app = FastAPI()

@app.get("/api/doctors")
def get_doctors():
    return {"doctors": [...]}

@app.get("/api/doctors/{doctor_id}")
def get_doctor(doctor_id: int):
    return {"doctor": {...}}
```

**Laravel:**
```php
// routes/api.php
use App\Http\Controllers\DoctorController;

Route::get('/doctors', [DoctorController::class, 'index']);
Route::get('/doctors/{id}', [DoctorController::class, 'show']);

// The actual logic is in the Controller (separate file)
```

👉 **Key difference:** Laravel separates route definitions from logic. Routes just point to Controller methods.

---

### 2️⃣ CONTROLLER = YOUR ROUTE FUNCTION

**FastAPI:**
```python
@app.post("/api/appointments")
def create_appointment(
    patient_id: int,
    doctor_id: int,
    start_time: datetime,
    db: Session = Depends(get_db)
):
    # Validation happens via Pydantic
    appointment = Appointment(
        patient_id=patient_id,
        doctor_id=doctor_id,
        start_time=start_time
    )
    db.add(appointment)
    db.commit()
    return appointment
```

**Laravel:**
```php
// app/Http/Controllers/AppointmentController.php
class AppointmentController extends Controller
{
    public function store(Request $request)  // $request = like FastAPI's Request body
    {
        // Validation (like Pydantic but inline)
        $validated = $request->validate([
            'patient_id' => 'required|integer|exists:patients,patient_id',
            'doctor_id' => 'required|integer|exists:doctors,doctor_id',
            'start_time' => 'required|date_format:Y-m-d H:i:s',
        ]);
        
        // Create record (like db.add + db.commit combined)
        $appointment = Appointment::create($validated);
        
        return response()->json($appointment, 201);
    }
}
```

👉 **Key insight:** 
- `$request` = FastAPI's request body
- `$request->validate()` = Pydantic validation
- `Model::create()` = `db.add()` + `db.commit()`

---

### 3️⃣ DATABASE MODELS (ORM)

**FastAPI + SQLAlchemy:**
```python
# models.py
from sqlalchemy import Column, Integer, String, ForeignKey
from sqlalchemy.orm import relationship

class Doctor(Base):
    __tablename__ = "doctors"
    
    doctor_id = Column(Integer, primary_key=True)
    first_name = Column(String(100))
    last_name = Column(String(100))
    specialty_id = Column(Integer, ForeignKey("specialties.specialty_id"))
    
    # Relationships
    specialty = relationship("Specialty", back_populates="doctors")
    appointments = relationship("Appointment", back_populates="doctor")
```

**Laravel Eloquent:**
```php
// app/Models/Doctor.php
class Doctor extends Model
{
    protected $table = 'doctors';
    protected $primaryKey = 'doctor_id';
    
    protected $fillable = ['first_name', 'last_name', 'specialty_id', 'phone', 'email'];
    
    // Relationships (same concept, different syntax)
    public function specialty() {
        return $this->belongsTo(Specialty::class, 'specialty_id');
    }
    
    public function appointments() {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }
}
```

👉 **Relationship translation:**
| SQLAlchemy | Laravel Eloquent |
|------------|------------------|
| `relationship("Parent")` with FK | `belongsTo(Parent::class)` |
| `relationship("Child", back_populates)` | `hasMany(Child::class)` |
| `relationship(..., uselist=False)` | `hasOne(...)` |

---

### 4️⃣ DATABASE QUERIES

**FastAPI + SQLAlchemy:**
```python
# Get all doctors
doctors = db.query(Doctor).all()

# Get one doctor
doctor = db.query(Doctor).filter(Doctor.doctor_id == id).first()

# Get doctors by specialty
doctors = db.query(Doctor).filter(Doctor.specialty_id == 1).all()

# Get doctor with relationships loaded
doctor = db.query(Doctor).options(joinedload(Doctor.specialty)).first()
```

**Laravel Eloquent:**
```php
// Get all doctors
$doctors = Doctor::all();

// Get one doctor
$doctor = Doctor::find($id);
// or
$doctor = Doctor::where('doctor_id', $id)->first();

// Get doctors by specialty
$doctors = Doctor::where('specialty_id', 1)->get();

// Get doctor with relationships loaded (eager loading)
$doctor = Doctor::with('specialty')->first();
```

👉 **Query translation:**
| SQLAlchemy | Laravel |
|------------|---------|
| `db.query(Model).all()` | `Model::all()` |
| `db.query(Model).filter(...).first()` | `Model::where(...)->first()` |
| `db.query(Model).filter(...).all()` | `Model::where(...)->get()` |
| `joinedload()` | `::with('relationship')` |

---

### 5️⃣ MIGRATIONS (DATABASE SCHEMA)

**FastAPI + Alembic:**
```python
# alembic/versions/001_create_doctors.py
def upgrade():
    op.create_table(
        'doctors',
        sa.Column('doctor_id', sa.Integer(), primary_key=True),
        sa.Column('first_name', sa.String(100)),
        sa.Column('specialty_id', sa.Integer(), sa.ForeignKey('specialties.specialty_id')),
    )

def downgrade():
    op.drop_table('doctors')
```

**Laravel:**
```php
// database/migrations/2025_11_20_000003_create_doctors_table.php
public function up(): void
{
    Schema::create('doctors', function (Blueprint $table) {
        $table->id('doctor_id');
        $table->string('first_name', 100);
        $table->foreignId('specialty_id')->constrained('specialties', 'specialty_id');
        $table->timestamps();  // created_at, updated_at
    });
}

public function down(): void
{
    Schema::dropIfExists('doctors');
}
```

👉 **Commands:**
| Alembic | Laravel |
|---------|---------|
| `alembic upgrade head` | `php artisan migrate` |
| `alembic downgrade -1` | `php artisan migrate:rollback` |
| `alembic revision -m "msg"` | `php artisan make:migration msg` |

---

### 6️⃣ MIDDLEWARE (REQUEST INTERCEPTORS)

**FastAPI:**
```python
@app.middleware("http")
async def auth_middleware(request: Request, call_next):
    token = request.headers.get("Authorization")
    if not token:
        return JSONResponse(status_code=401, content={"error": "Not authenticated"})
    response = await call_next(request)
    return response
```

**Laravel:**
```php
// app/Http/Middleware/RoleMiddleware.php
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        if (auth()->user()->role !== $role) {
            abort(403, 'Unauthorized');
        }
        
        return $next($request);  // Continue to the route
    }
}

// Usage in routes:
Route::middleware(['auth', 'role:admin'])->group(function() {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});
```

---

### 7️⃣ BACKGROUND TASKS = EVENTS & LISTENERS

**FastAPI:**
```python
from fastapi import BackgroundTasks

@app.post("/api/appointments")
def create_appointment(data: AppointmentCreate, background_tasks: BackgroundTasks):
    appointment = create_in_db(data)
    
    # Run email sending in background
    background_tasks.add_task(send_confirmation_email, appointment)
    
    return appointment

def send_confirmation_email(appointment):
    # Send email logic
    pass
```

**Laravel (Events & Listeners):**
```php
// 1. Controller fires an event
class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        $appointment = Appointment::create($validated);
        
        // Fire event (like background_tasks.add_task)
        event(new AppointmentCreated($appointment));
        
        return response()->json($appointment, 201);
    }
}

// 2. Event class (the "message")
// app/Events/AppointmentCreated.php
class AppointmentCreated
{
    public function __construct(public Appointment $appointment) {}
}

// 3. Listener (the "task" that runs)
// app/Listeners/SendAppointmentConfirmation.php
class SendAppointmentConfirmation
{
    public function handle(AppointmentCreated $event)
    {
        Mail::to($event->appointment->patient->email)
            ->send(new AppointmentConfirmation($event->appointment));
    }
}

// 4. Register in EventServiceProvider (connect event to listener)
protected $listen = [
    AppointmentCreated::class => [
        SendAppointmentConfirmation::class,
    ],
];
```

👉 **Flow:** Controller → Event → Listener (sends email)

---

## 📁 FILE STRUCTURE EXPLAINED

```
Medical-Clinic-Full-system/
│
├── routes/
│   ├── api.php          ← Like your main.py @app.get() routes (API endpoints)
│   └── web.php          ← Routes that return HTML pages
│
├── app/
│   ├── Http/
│   │   ├── Controllers/ ← Your route handler functions live here
│   │   │   ├── AppointmentController.php
│   │   │   ├── DoctorController.php
│   │   │   └── PatientController.php
│   │   │
│   │   └── Middleware/  ← Request interceptors (auth checks)
│   │       └── RoleMiddleware.php
│   │
│   ├── Models/          ← Like SQLAlchemy models
│   │   ├── Appointment.php
│   │   ├── Doctor.php
│   │   └── Patient.php
│   │
│   ├── Events/          ← Background task triggers
│   └── Listeners/       ← Background task handlers
│
├── database/
│   ├── migrations/      ← Like Alembic migrations
│   └── seeders/         ← Test data generators
│
└── .env                 ← Environment variables (same concept!)
```

---

## 🔄 REQUEST FLOW (How a request travels)

```
HTTP Request
     │
     ▼
┌─────────────────┐
│  routes/api.php │  ← "Which controller handles this URL?"
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Middleware    │  ← "Is user logged in? Correct role?"
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Controller    │  ← "Validate input, run business logic"
│                 │
│  Uses Model ────┼──► Eloquent Model ──► Database
│                 │
│  Fires Event ───┼──► Listener ──► Send Email
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  JSON Response  │
└─────────────────┘
```

---

## 🎯 QUICK REFERENCE CHEAT SHEET

| Task | Laravel Command/Code |
|------|---------------------|
| Start server | `php artisan serve` |
| Run migrations | `php artisan migrate` |
| Rollback migration | `php artisan migrate:rollback` |
| Seed database | `php artisan db:seed` |
| Create controller | `php artisan make:controller NameController` |
| Create model | `php artisan make:model Name` |
| Create migration | `php artisan make:migration create_table_name` |
| Clear cache | `php artisan cache:clear` |
| List routes | `php artisan route:list` |

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
