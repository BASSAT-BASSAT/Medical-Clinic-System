<?php

/**
 * Medical Clinic API Test Examples
 * 
 * Base URL: http://localhost:8000/api
 * 
 * This file demonstrates how to use the new and enhanced API endpoints
 */

// ==========================================
// DOCTOR AVAILABILITY ENDPOINTS
// ==========================================

// 1. Get all availability for a doctor
// GET /api/doctors/1/availability

// Response:
// {
//     "doctor_id": 1,
//     "availability": [
//         {
//             "availability_id": 1,
//             "doctor_id": 1,
//             "day_of_week": "Monday",
//             "start_time": "09:00:00",
//             "end_time": "17:00:00",
//             "is_available": true
//         }
//     ]
// }

// 2. Set availability for a specific day
// POST /api/availability
// {
//     "doctor_id": 1,
//     "day_of_week": "Monday",
//     "start_time": "09:00:00",
//     "end_time": "17:00:00",
//     "is_available": true
// }

// 3. Bulk set availability for all days
// POST /api/availability/bulk-set
// {
//     "doctor_id": 1,
//     "availability": [
//         {
//             "day_of_week": "Monday",
//             "start_time": "09:00:00",
//             "end_time": "17:00:00",
//             "is_available": true
//         },
//         {
//             "day_of_week": "Tuesday",
//             "start_time": "09:00:00",
//             "end_time": "17:00:00",
//             "is_available": true
//         },
//         {
//             "day_of_week": "Wednesday",
//             "start_time": "09:00:00",
//             "end_time": "14:00:00",
//             "is_available": true
//         },
//         {
//             "day_of_week": "Thursday",
//             "start_time": "09:00:00",
//             "end_time": "17:00:00",
//             "is_available": true
//         },
//         {
//             "day_of_week": "Friday",
//             "start_time": "09:00:00",
//             "end_time": "17:00:00",
//             "is_available": true
//         },
//         {
//             "day_of_week": "Saturday",
//             "start_time": "10:00:00",
//             "end_time": "14:00:00",
//             "is_available": true
//         },
//         {
//             "day_of_week": "Sunday",
//             "start_time": "00:00:00",
//             "end_time": "00:00:00",
//             "is_available": false
//         }
//     ]
// }

// ==========================================
// APPOINTMENT ENDPOINTS
// ==========================================

// 1. Get available slots for a doctor on a specific date
// GET /api/doctors/1/available-slots/2025-12-15
// Response:
// {
//     "slots": [
//         {
//             "start_time": "2025-12-15 09:00:00",
//             "end_time": "2025-12-15 10:00:00"
//         },
//         {
//             "start_time": "2025-12-15 10:00:00",
//             "end_time": "2025-12-15 11:00:00"
//         },
//         ...
//     ]
// }

// 2. Book an appointment
// POST /api/appointments
// {
//     "patient_id": 1,
//     "doctor_id": 1,
//     "start_time": "2025-12-15 09:00:00",
//     "end_time": "2025-12-15 10:00:00",
//     "reason": "General checkup",
//     "status": "scheduled"
// }
// 
// Success Response (201):
// {
//     "appointment_id": 1,
//     "patient_id": 1,
//     "doctor_id": 1,
//     "start_time": "2025-12-15T09:00:00.000000Z",
//     "end_time": "2025-12-15T10:00:00.000000Z",
//     "status": "scheduled",
//     "reason": "General checkup",
//     "created_at": "2025-11-27T05:30:00.000000Z",
//     "updated_at": "2025-11-27T05:30:00.000000Z"
// }

// 3. Get upcoming appointments for a patient
// GET /api/appointments/upcoming/patient/1
// Response: Array of upcoming appointments

// 4. Get past appointments for a patient
// GET /api/appointments/past/patient/1
// Response: Array of past appointments

// 5. Cancel an appointment
// PUT /api/appointments/1
// {
//     "status": "cancelled"
// }

// ==========================================
// NOTIFICATION ENDPOINTS
// ==========================================

// 1. Get all notifications
// GET /api/notifications

// 2. Get patient notifications
// GET /api/notifications/patient/1
// Response:
// {
//     "current_page": 1,
//     "data": [
//         {
//             "notification_id": 1,
//             "appointment_id": 1,
//             "patient_id": 1,
//             "doctor_id": 1,
//             "type": "email",
//             "notification_type": "booking_confirmation",
//             "message": "Your appointment has been successfully booked.",
//             "is_sent": true,
//             "sent_at": "2025-11-27T05:30:00.000000Z",
//             "recipient": "patient@example.com",
//             "created_at": "2025-11-27T05:30:00.000000Z"
//         }
//     ],
//     "total": 5
// }

// 3. Get unsent notifications (for admin/backend jobs)
// GET /api/notifications/unsent
// Response: Array of all unsent notifications

// 4. Mark a notification as sent
// PUT /api/notifications/1/mark-sent
// Response: Updated notification

// 5. Mark all unsent as sent
// PUT /api/notifications/mark-all-sent
// Response:
// {
//     "message": "Marked 5 notifications as sent",
//     "count": 5
// }

// ==========================================
// REPORTING ENDPOINTS
// ==========================================

// 1. Daily report
// GET /api/reports/daily/2025-12-15
// Response:
// {
//     "stats": {
//         "date": "2025-12-15",
//         "total_appointments": 10,
//         "completed": 8,
//         "scheduled": 1,
//         "cancelled": 1
//     },
//     "appointments": [...]
// }

// 2. Weekly report
// GET /api/reports/weekly/2025-12-08
// Response:
// {
//     "stats": {
//         "week_start": "2025-12-08",
//         "week_end": "2025-12-14",
//         "total_appointments": 45,
//         "completed": 40,
//         "scheduled": 3,
//         "cancelled": 2,
//         "daily_breakdown": {
//             "2025-12-08": {...},
//             "2025-12-09": {...},
//             ...
//         }
//     }
// }

// 3. Monthly report
// GET /api/reports/monthly/2025/12
// Response: Monthly statistics

// 4. Doctor-specific report (date range)
// GET /api/reports/doctor/1/2025-12-01/2025-12-31
// Response:
// {
//     "stats": {
//         "doctor_id": 1,
//         "doctor_name": "Dr. John Doe",
//         "specialty": "Cardiology",
//         "date_range": {...},
//         "total_appointments": 50,
//         "completed": 48,
//         "scheduled": 2,
//         "cancelled": 0,
//         "completion_rate": 96.0
//     }
// }

// 5. Patient report
// GET /api/reports/patient/1
// GET /api/reports/patient/1/2025-12-01/2025-12-31
// Response: Patient appointment history and statistics

// 6. System statistics
// GET /api/reports/system-stats
// Response:
// {
//     "overall_stats": {
//         "total_appointments": 1000,
//         "completed": 950,
//         "scheduled": 45,
//         "cancelled": 5,
//         "completion_rate": 95.0,
//         "cancellation_rate": 0.5
//     },
//     "doctor_stats": {
//         "total_doctors": 20,
//         "avg_appointments_per_doctor": 50.0
//     },
//     "today_stats": {
//         "appointments_today": 15
//     },
//     "upcoming_stats": {
//         "appointments_next_7_days": 75
//     }
// }

// ==========================================
// CONSOLE COMMANDS FOR AUTOMATION
// ==========================================

// Send appointment reminders for tomorrow's appointments:
// php artisan appointments:send-reminders

// Send all unsent notifications:
// php artisan notifications:send-unsent

// ==========================================
// SCHEDULING (Add to app/Console/Kernel.php)
// ==========================================

// $schedule->command('appointments:send-reminders')
//     ->dailyAt('08:00');  // Send reminders daily at 8 AM

// $schedule->command('notifications:send-unsent')
//     ->everyFiveMinutes();  // Send unsent notifications every 5 minutes

// ==========================================
// ERROR RESPONSES
// ==========================================

// Conflict Error (409):
// {
//     "error": "Doctor has a conflicting appointment at this time"
// }

// Validation Error (422):
// {
//     "message": "The given data was invalid.",
//     "errors": {
//         "start_time": ["Start time must be in the future"],
//         "end_time": ["Appointment duration cannot exceed 4 hours"]
//     }
// }

// Not Found (404):
// {
//     "message": "No query results found for model [App\\Models\\Appointment] 1"
// }

// ==========================================
// CURL EXAMPLES
// ==========================================

// Get available slots:
// curl -X GET "http://localhost:8000/api/doctors/1/available-slots/2025-12-15"

// Book appointment:
// curl -X POST "http://localhost:8000/api/appointments" \
//   -H "Content-Type: application/json" \
//   -d '{
//     "patient_id": 1,
//     "doctor_id": 1,
//     "start_time": "2025-12-15 09:00:00",
//     "end_time": "2025-12-15 10:00:00",
//     "reason": "Checkup"
//   }'

// Get daily report:
// curl -X GET "http://localhost:8000/api/reports/daily/2025-12-15"

// Get system stats:
// curl -X GET "http://localhost:8000/api/reports/system-stats"
