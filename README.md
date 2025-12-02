# 🏥 Medical Clinic Management System

A comprehensive full-stack medical clinic management system built with Laravel, featuring separate dashboards for administrators, doctors, and patients. The system streamlines appointment scheduling, medical records management, and clinic operations.

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

---

## 📋 Table of Contents

- [Features](#-features)
- [Screenshots](#-screenshots)
- [Tech Stack](#-tech-stack)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Database Setup](#-database-setup)
- [Running the Application](#-running-the-application)
- [Default Login Credentials](#-default-login-credentials)
- [Project Structure](#-project-structure)
- [API Endpoints](#-api-endpoints)
- [License](#-license)

---

## ✨ Features

### 👨‍💼 Admin Dashboard
- **Overview Statistics** - Real-time stats for doctors, patients, and appointments
- **Doctor Management** - Add, view, and manage doctors with specialties
- **Patient Management** - Complete patient directory with search functionality
- **Appointment Overview** - View all scheduled, completed, and cancelled appointments
- **Reports & Analytics** - Auto-generated charts showing appointment trends, completion rates, and doctor performance

### 👨‍⚕️ Doctor Dashboard
- **Today's Schedule** - View all appointments for the current day
- **Calendar View** - Monthly calendar with appointment indicators
- **Patient Records** - Access patient medical history and records
- **Add Medical Records** - Create new medical records with diagnosis, prescription, and notes
- **Set Availability** - Configure weekly availability schedule (days and hours)
- **Reports** - Generate and download appointment reports

### 👤 Patient Dashboard
- **My Appointments** - View all past and upcoming appointments
- **Book Appointment** - Easy booking flow: select specialty → doctor → date → time
- **Medical Records** - Access personal medical history
- **Real-time Notifications** - Get notified about appointment confirmations and cancellations
- **Cancel Appointments** - Cancel scheduled appointments with confirmation

### 🔔 Notification System
- Real-time toast notifications (success, error, warning, info)
- Custom confirmation dialogs
- Email notifications for appointment confirmations and cancellations

---

## 🛠 Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend** | Laravel 11.x (PHP 8.2+) |
| **Frontend** | Blade Templates, Tailwind CSS 3.x |
| **Database** | MySQL 8.0 / MariaDB |
| **Build Tool** | Vite |
| **Authentication** | Laravel Breeze |
| **Email** | Laravel Mail (SMTP) |

---

## 📦 Requirements

Before installation, ensure you have the following installed:

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18.x
- **npm** >= 9.x
- **MySQL** >= 8.0 or MariaDB >= 10.4
- **Git**

---

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/BASSAT-BASSAT/Medical-Clinic-Full-system.git
cd Medical-Clinic-Full-system
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

### 4. Environment Configuration

Copy the example environment file and configure it:

```bash
cp .env.example .env
```

Edit the `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=medical_clinic
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

---

## 🗄 Database Setup

### 1. Create the Database

```sql
CREATE DATABASE medical_clinic;
```

### 2. Run Migrations

```bash
php artisan migrate
```

### 3. Seed the Database

This will populate the database with sample data including users, doctors, patients, specialties, and appointments:

```bash
php artisan db:seed
```

---

## ▶️ Running the Application

### Development Mode

You need to run two terminals simultaneously:

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - Vite Development Server:**
```bash
npm run dev
```

The application will be available at: **http://localhost:8000**

### Production Build

For production deployment:

```bash
npm run build
php artisan serve
```

---

## 🔐 Default Login Credentials

After seeding the database, use these credentials to log in:

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@clinic.com | admin123 |
| **Doctor** | ahmed.samir@clinic.com | password123 |
| **Patient** | hossam@mail.com | password123 |

---

## 📁 Project Structure

```
Medical-Clinic-Full-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # API & Web Controllers
│   │   ├── Middleware/         # Authentication & Role Middleware
│   │   └── Requests/           # Form Request Validation
│   ├── Models/                 # Eloquent Models
│   │   ├── User.php
│   │   ├── Doctor.php
│   │   ├── Patient.php
│   │   ├── Appointment.php
│   │   ├── MedicalRecord.php
│   │   ├── Specialty.php
│   │   ├── DoctorAvailability.php
│   │   └── Notification.php
│   ├── Events/                 # Application Events
│   ├── Listeners/              # Event Listeners
│   ├── Mail/                   # Mailable Classes
│   └── Services/               # Business Logic Services
├── database/
│   ├── migrations/             # Database Migrations
│   └── seeders/                # Database Seeders
├── resources/
│   ├── views/
│   │   ├── admin/              # Admin Dashboard Views
│   │   ├── doctor/             # Doctor Dashboard Views
│   │   ├── patient/            # Patient Dashboard Views
│   │   ├── layouts/            # Layout Templates
│   │   └── auth/               # Authentication Views
│   ├── css/                    # Stylesheets
│   └── js/                     # JavaScript Files
├── routes/
│   ├── web.php                 # Web Routes
│   └── api.php                 # API Routes
└── public/                     # Public Assets
```

---

## 🔌 API Endpoints

### Appointments
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/appointments` | Get all appointments |
| GET | `/api/appointments/by-doctor/{id}` | Get appointments by doctor |
| GET | `/api/appointments/by-patient/{id}` | Get appointments by patient |
| POST | `/api/appointments` | Create new appointment |
| PUT | `/api/appointments/{id}` | Update appointment status |

### Doctors
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/doctors` | Get all doctors |
| GET | `/api/doctors/{id}` | Get doctor details |
| GET | `/api/doctors/specialty/{id}` | Get doctors by specialty |
| GET | `/api/doctors/{id}/available-slots/{date}` | Get available time slots |
| POST | `/api/doctors` | Create new doctor |

### Patients
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/patients` | Get all patients |
| GET | `/api/patients/{id}` | Get patient details |
| GET | `/api/patients/{id}/medical-records` | Get patient medical records |
| POST | `/api/patients` | Create new patient |

### Medical Records
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/medical-records` | Get all medical records |
| POST | `/api/medical-records` | Create new medical record |

### Availability
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/doctors/{id}/availability` | Get doctor availability |
| POST | `/api/availability/bulk-set` | Set weekly availability |

### Specialties
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/specialties` | Get all specialties |

### Notifications
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/notifications/user/{id}` | Get user notifications |
| PUT | `/api/notifications/{id}/read` | Mark notification as read |
| PUT | `/api/notifications/user/{id}/read-all` | Mark all as read |

---

## 🎨 UI Features

- **Responsive Design** - Works on desktop, tablet, and mobile
- **Modern UI** - Clean interface with Tailwind CSS
- **Toast Notifications** - Elegant slide-in notifications for all actions
- **Confirmation Dialogs** - Custom modals instead of browser alerts
- **Real-time Updates** - No page refresh needed for most actions
- **Interactive Calendar** - Visual monthly calendar for appointments

---

## 📧 Email Configuration (Optional)

To enable email notifications, configure your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="Medical Clinic"
```

---

## 🧪 Testing

Run the test suite:

```bash
php artisan test
```

Or with Pest:

```bash
./vendor/bin/pest
```

---

## 🔧 Troubleshooting

### Common Issues

**1. Vite manifest not found**
```bash
npm run build
```

**2. Database connection error**
- Ensure MySQL is running
- Verify `.env` database credentials

**3. Storage permission issues**
```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

**4. Clear application cache**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 👥 Contributors

- **BASSAT-BASSAT** - Project Owner

---

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS framework
- [Vite](https://vitejs.dev) - Next Generation Frontend Tooling

---

<p align="center">Made with ❤️ for better healthcare management</p>
