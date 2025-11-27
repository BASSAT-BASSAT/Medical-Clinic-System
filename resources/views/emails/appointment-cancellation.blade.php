@component('mail::message')
# Appointment Cancelled

Dear {{ $patient->first_name }},

We are writing to confirm that your appointment has been cancelled.

@component('mail::panel')
**Cancelled Appointment Details:**

- **Doctor:** Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}
- **Specialty:** {{ $doctor->specialty->name ?? 'General' }}
- **Date & Time:** {{ $appointment->start_time->format('M d, Y \a\t g:i A') }}
- **Status:** Cancelled
@if($appointment->reason)
- **Reason:** {{ $appointment->reason }}
@endif
@endcomponent

**If you would like to:**
- **Schedule a new appointment:** Please visit our scheduling system
- **Contact the clinic:** Reach out to our reception team
- **Reschedule:** Let us know your preferred date and time

We apologize for any inconvenience this may cause and look forward to serving you in the future.

@component('mail::button', ['url' => config('app.url') . '/appointments/new'])
Schedule New Appointment
@endcomponent

Best regards,<br>
{{ config('app.name') }} Team
@endcomponent
