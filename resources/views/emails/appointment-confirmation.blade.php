@component('mail::message')
# Appointment Confirmation

Dear {{ $patient->first_name }},

Your appointment has been successfully confirmed!

@component('mail::panel')
**Appointment Details:**

- **Doctor:** Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}
- **Specialty:** {{ $doctor->specialty->name ?? 'General' }}
- **Date & Time:** {{ $appointment->start_time->format('M d, Y \a\t g:i A') }}
- **Duration:** {{ $appointment->start_time->diffInMinutes($appointment->end_time) }} minutes
@if($appointment->reason)
- **Reason:** {{ $appointment->reason }}
@endif
@endcomponent

**What to bring:**
- Valid ID
- Insurance card (if applicable)
- Any relevant medical documents

If you need to cancel or reschedule, please contact us at least 24 hours in advance.

@component('mail::button', ['url' => config('app.url') . '/appointments/' . $appointment->appointment_id])
View Appointment Details
@endcomponent

Best regards,<br>
{{ config('app.name') }} Team
@endcomponent
