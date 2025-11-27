@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Book an Appointment</h1>
                <p class="text-gray-600 mt-2">Schedule your visit with a doctor</p>
            </div>
            <a href="{{ route('patient.dashboard') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                ← Back to Dashboard
            </a>
        </div>

        <!-- Booking Form -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Appointment Details</h2>
            </div>
            <div class="p-6">
                <form id="bookingForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Specialty Selection -->
                        <div>
                            <label for="specialty_id" class="block text-sm font-medium text-gray-700 mb-2">Specialty</label>
                            <select id="specialty_id" name="specialty_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500" required aria-label="Select specialty">
                                <option value="">-- Select Specialty --</option>
                            </select>
                        </div>

                        <!-- Doctor Selection -->
                        <div>
                            <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-2">Doctor</label>
                            <select id="doctor_id" name="doctor_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500" required aria-label="Select doctor">
                                <option value="">-- Select Doctor --</option>
                            </select>
                        </div>
                    </div>

                    <!-- Date and Time Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Appointment Date</label>
                            <input type="date" id="start_date" name="start_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500" required aria-label="Select appointment date">
                            <p class="text-xs text-gray-500 mt-1">Select a date when the doctor is available</p>
                        </div>

                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Appointment Time</label>
                            <select id="start_time" name="start_time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500" required aria-label="Select appointment time">
                                <option value="">-- Select Time --</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Available 1-hour slots for the selected date</p>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason for Visit</label>
                        <textarea id="reason" name="reason" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500" placeholder="Please describe your symptoms or reason for visit" aria-label="Enter reason for visit"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            Book Appointment
                        </button>
                        <button type="button" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400" onclick="history.back()">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Available Slots Info -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="font-semibold text-blue-900 mb-2">ℹ️ About Appointment Booking</h3>
            <ul class="text-sm text-blue-800 space-y-1">
                <li>• Select your preferred specialty and doctor</li>
                <li>• Choose a date in the future (at least 1 day from now)</li>
                <li>• Available time slots will be shown based on doctor availability</li>
                <li>• Provide a brief reason for your visit</li>
                <li>• You'll receive a confirmation email with appointment details</li>
            </ul>
        </div>
    </div>
</div>

<script>
const patientId = {{ auth()->user()->patient->patient_id ?? 'null' }};

document.addEventListener('DOMContentLoaded', function() {
    loadSpecialties();
    setMinDate();
    
    document.getElementById('specialty_id').addEventListener('change', loadDoctorsBySpecialty);
    document.getElementById('start_date').addEventListener('change', loadAvailableSlots);
    document.getElementById('doctor_id').addEventListener('change', loadAvailableSlots);
    document.getElementById('bookingForm').addEventListener('submit', bookAppointment);
});

function loadSpecialties() {
    fetch('/api/specialties')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('specialty_id');
            const specialties = Array.isArray(data) ? data : (data.data || data);
            
            if (!specialties || specialties.length === 0) {
                select.innerHTML = '<option value="">No specialties available</option>';
                return;
            }
            
            select.innerHTML = '<option value="">-- Select Specialty --</option>' +
                specialties.map(s => `<option value="${s.specialty_id}">${s.name}</option>`).join('');
        })
        .catch(err => {
            console.error('Error loading specialties:', err);
            document.getElementById('specialty_id').innerHTML = '<option value="">Error loading specialties</option>';
        });
}

function loadDoctorsBySpecialty() {
    const specialtyId = document.getElementById('specialty_id').value;
    if (!specialtyId) {
        document.getElementById('doctor_id').innerHTML = '<option value="">-- Select Doctor --</option>';
        return;
    }

    fetch(`/api/doctors/specialty/${specialtyId}`)
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('doctor_id');
            const doctors = Array.isArray(data) ? data : (data.data || data);
            
            if (!doctors || doctors.length === 0) {
                select.innerHTML = '<option value="">No doctors available</option>';
                return;
            }
            
            select.innerHTML = '<option value="">-- Select Doctor --</option>' +
                doctors.map(d => `<option value="${d.doctor_id}">${d.first_name} ${d.last_name}</option>`).join('');
        })
        .catch(err => {
            console.error('Error loading doctors:', err);
            document.getElementById('doctor_id').innerHTML = '<option value="">Error loading doctors</option>';
        });
}

function loadAvailableSlots() {
    const doctorId = document.getElementById('doctor_id').value;
    const date = document.getElementById('start_date').value;

    if (!doctorId || !date) {
        document.getElementById('start_time').innerHTML = '<option value="">-- Select Time --</option>';
        return;
    }

    fetch(`/api/doctors/${doctorId}/available-slots/${date}`)
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            const select = document.getElementById('start_time');
            const slots = data.slots || data.data || data;
            
            if (!slots || slots.length === 0) {
                const message = data.message || 'No available slots for this date';
                select.innerHTML = `<option value="">${message}</option>`;
                return;
            }
            
            select.innerHTML = '<option value="">-- Select Time --</option>' +
                slots.map(time => `<option value="${time}">${time}</option>`).join('');
        })
        .catch(err => {
            console.error('Error loading slots:', err);
            document.getElementById('start_time').innerHTML = '<option value="">Error loading times - check console</option>';
        });
}

function setMinDate() {
    const input = document.getElementById('start_date');
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    input.min = tomorrow.toISOString().split('T')[0];
}

function bookAppointment(e) {
    e.preventDefault();
    
    const date = document.getElementById('start_date').value;
    const time = document.getElementById('start_time').value;
    
    if (!date || !time) {
        alert('Please select both date and time');
        return;
    }

    const startTime = date + ' ' + time + ':00';
    const endTime = calculateEndTime(date, time);

    const appointmentData = {
        patient_id: patientId,
        doctor_id: document.getElementById('doctor_id').value,
        start_time: startTime,
        end_time: endTime,
        reason: document.getElementById('reason').value || '',
        status: 'scheduled'
    };

    fetch('/api/appointments', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(appointmentData)
    })
    .then(res => {
        if (!res.ok) {
            return res.json().then(err => { throw err; });
        }
        return res.json();
    })
    .then(data => {
        alert('Appointment booked successfully! Check your email for confirmation.');
        window.location.href = '{{ route("patient.dashboard") }}';
    })
    .catch(err => {
        console.error('Booking error:', err);
        alert('Error booking appointment: ' + (err.message || JSON.stringify(err)));
    });
}

function calculateEndTime(date, time) {
    const [hours, minutes] = time.split(':').map(Number);
    const endHours = String(hours + 1).padStart(2, '0');
    const endTime = endHours + ':' + String(minutes).padStart(2, '0') + ':00';
    return date + ' ' + endTime;
}
</script>
@endsection
