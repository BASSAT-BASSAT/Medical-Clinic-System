@component('mail::message')
# Appointment Reminder

Dear {{ $patient->first_name }},

This is a friendly reminder about your upcoming appointment.

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

**Please remember to:**
- Arrive 10-15 minutes early
- Bring your valid ID and insurance card
- Bring any relevant medical documents
- Inform us of any medication you're currently taking

If you need to reschedule or cancel, please contact us as soon as possible.

@component('mail::button', ['url' => config('app.url') . '/appointments/' . $appointment->appointment_id])
View Appointment
@endcomponent

Best regards,<br>
{{ config('app.name') }} Team
@endcomponent
