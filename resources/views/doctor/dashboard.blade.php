@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Doctor Dashboard</h1>
            <p class="text-gray-600 mt-2">Welcome, Dr. {{ Auth::user()->doctor->first_name }}!</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm font-medium">Today's Appointments</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2" id="today-count">0</p>
                    </div>
                    <div class="text-4xl text-blue-200">📅</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm font-medium">Total Patients</p>
                        <p class="text-3xl font-bold text-green-600 mt-2" id="patients-count">0</p>
                    </div>
                    <div class="text-4xl text-green-200">👥</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm font-medium">Completed This Month</p>
                        <p class="text-3xl font-bold text-purple-600 mt-2" id="completed-count">0</p>
                    </div>
                    <div class="text-4xl text-purple-200">✓</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm font-medium">Completion Rate</p>
                        <p class="text-3xl font-bold text-orange-600 mt-2" id="completion-rate">0%</p>
                    </div>
                    <div class="text-4xl text-orange-200">📊</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Today's Schedule -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">Today's Schedule</h2>
                        <button onclick="openAvailabilityModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                            ⚙️ Set Availability
                        </button>
                    </div>
                    <div id="schedule-list" class="divide-y divide-gray-200">
                        <p class="p-6 text-center text-gray-500">Loading schedule...</p>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="space-y-6">
                <!-- Calendar -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Calendar</h3>
                    <input type="date" id="scheduleDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500">
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <button onclick="openAvailabilityModal()" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Set Availability
                        </button>
                        <a href="{{ route('doctor.reports') }}" class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            View Reports
                        </a>
                        <a href="{{ route('doctor.patients') }}" class="block w-full text-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                            My Patients
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments Table -->
        <div class="mt-8 bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Upcoming Appointments</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Patient</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Date & Time</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Reason</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="appointments-table" class="divide-y divide-gray-200">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Loading appointments...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Set Availability Modal -->
<div id="availabilityModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600" onclick="closeAvailabilityModal()">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <h3 class="text-lg font-medium text-gray-900 mb-4">Set Your Availability</h3>
        <p class="text-sm text-gray-600 mb-4">Set your working hours for each day of the week. Patients can book appointments during these times.</p>
        <div id="availabilityForm" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Day of Week</label>
                    <select id="day_of_week" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" onchange="updateDayDisplay()">
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1" id="dayDateInfo">Select which day of the week to set availability</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Available?</label>
                    <select id="is_available" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="1">Yes - I'm working</option>
                        <option value="0">No - I'm off</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Start Time</label>
                    <input type="time" id="start_time" value="09:00" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">End Time</label>
                    <input type="time" id="end_time" value="17:00" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 p-3 rounded text-sm text-blue-800">
                <strong>💡 Example:</strong> If you set Monday 9am-5pm, patients can book appointments on any Monday (Jan 5, Jan 12, Jan 19, etc.) during those hours.
            </div>

            <div class="flex gap-3">
                <button onclick="saveAvailability()" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Save Availability
                </button>
                <button type="button" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400" onclick="closeAvailabilityModal()">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadTodaySchedule();
    loadStats();
    loadAppointments();

    document.getElementById('scheduleDate')?.addEventListener('change', function() {
        loadScheduleByDate(this.value);
    });
});

function loadTodaySchedule() {
    const doctorId = {{ Auth::user()->doctor->doctor_id ?? 'null' }};
    const today = new Date().toISOString().split('T')[0];

    if (!doctorId) return;

    fetch(`/api/appointments/by-doctor/${doctorId}`)
        .then(res => res.json())
        .then(data => {
            const today_apts = data.filter(apt => apt.start_time.includes(today) && apt.status !== 'cancelled');
            document.getElementById('today-count').textContent = today_apts.length;

            const list = document.getElementById('schedule-list');
            if (today_apts.length === 0) {
                list.innerHTML = '<p class="p-6 text-center text-gray-500">No appointments today</p>';
                return;
            }

            list.innerHTML = today_apts.map(apt => `
                <div class="p-6 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-semibold text-gray-900">${apt.patient.first_name} ${apt.patient.last_name}</p>
                            <p class="text-sm text-gray-600 mt-1">${new Date(apt.start_time).toLocaleTimeString()}</p>
                            <p class="text-sm text-gray-600">${apt.reason || 'General Appointment'}</p>
                        </div>
                        <button onclick="markAsComplete(${apt.appointment_id})" class="text-green-600 hover:text-green-700 text-sm font-medium">
                            Mark Complete
                        </button>
                    </div>
                </div>
            `).join('');
        })
        .catch(err => console.error('Error:', err));
}

function loadStats() {
    const doctorId = {{ Auth::user()->doctor->doctor_id ?? 'null' }};
    if (!doctorId) return;

    fetch(`/api/appointments/by-doctor/${doctorId}`)
        .then(res => res.json())
        .then(data => {
            const completed = data.filter(apt => apt.status === 'completed').length;
            const total = data.length;
            const completionRate = total > 0 ? Math.round((completed / total) * 100) : 0;

            document.getElementById('completed-count').textContent = completed;
            document.getElementById('completion-rate').textContent = completionRate + '%';

            // Count unique patients
            const uniquePatients = new Set(data.map(apt => apt.patient_id)).size;
            document.getElementById('patients-count').textContent = uniquePatients;
        })
        .catch(err => console.error('Error:', err));
}

function loadAppointments() {
    const doctorId = {{ Auth::user()->doctor->doctor_id ?? 'null' }};
    if (!doctorId) return;

    fetch(`/api/appointments/by-doctor/${doctorId}`)
        .then(res => res.json())
        .then(data => {
            const upcoming = data.filter(apt => new Date(apt.start_time) > new Date() && apt.status !== 'cancelled');
            const table = document.getElementById('appointments-table');

            if (upcoming.length === 0) {
                table.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No upcoming appointments</td></tr>';
                return;
            }

            table.innerHTML = upcoming.map(apt => `
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-900">${apt.patient.first_name} ${apt.patient.last_name}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${new Date(apt.start_time).toLocaleString()}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${apt.reason || '-'}</td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${apt.status}</span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <button onclick="markAsComplete(${apt.appointment_id})" class="text-blue-600 hover:text-blue-700">Complete</button>
                    </td>
                </tr>
            `).join('');
        })
        .catch(err => console.error('Error:', err));
}

function markAsComplete(appointmentId) {
    if (!confirm('Mark this appointment as completed?')) return;

    fetch(`/api/appointments/${appointmentId}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ status: 'completed' })
    })
    .then(res => res.json())
    .then(() => {
        alert('Appointment marked as completed');
        loadTodaySchedule();
        loadStats();
        loadAppointments();
    })
    .catch(err => console.error('Error:', err));
}

function openAvailabilityModal() {
    document.getElementById('availabilityModal').classList.remove('hidden');
}

function closeAvailabilityModal() {
    document.getElementById('availabilityModal').classList.add('hidden');
}

function saveAvailability() {
    const doctorId = {{ Auth::user()->doctor->doctor_id ?? 'null' }};
    const day = document.getElementById('day_of_week').value;
    let startTime = document.getElementById('start_time').value;
    let endTime = document.getElementById('end_time').value;
    const isAvailable = document.getElementById('is_available').value === '1';

    if (!doctorId) {
        alert('Doctor ID not found. Please refresh the page.');
        return;
    }

    // Validate time format
    if (!startTime || !endTime) {
        alert('Please select both start and end times');
        return;
    }

    // Check if times are in reverse (end time is earlier than start time)
    // This indicates a shift that spans midnight (e.g., 5pm to 1am next day)
    const [startHours, startMins] = startTime.split(':').map(Number);
    const [endHours, endMins] = endTime.split(':').map(Number);
    const startTotalMins = startHours * 60 + startMins;
    const endTotalMins = endHours * 60 + endMins;

    if (endTotalMins < startTotalMins) {
        const confirmMsg = `You've entered times in reverse order (${startTime} to ${endTime}).\n\n` +
            `This will be interpreted as a shift that spans midnight.\n` +
            `For example: Start at ${startTime} (today) → End at ${endTime} (next day)\n\n` +
            `Is this correct?`;
        if (!confirm(confirmMsg)) {
            return;
        }
    }

    const availabilityData = {
        doctor_id: doctorId,
        day_of_week: day,
        start_time: startTime,
        end_time: endTime,
        is_available: isAvailable
    };

    console.log('Saving availability:', availabilityData);

    fetch('/api/availability', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(availabilityData)
    })
    .then(res => {
        if (!res.ok) {
            return res.json().then(err => { throw err; });
        }
        return res.json();
    })
    .then(data => {
        alert('Availability updated successfully!');
        closeAvailabilityModal();
        loadTodaySchedule();
        loadStats();
    })
    .catch(err => {
        console.error('Availability error:', err);
        alert('Error saving availability: ' + (err.message || JSON.stringify(err)));
    });
}

function loadScheduleByDate(date) {
    const doctorId = {{ Auth::user()->doctor->doctor_id ?? 'null' }};
    if (!doctorId) return;

    fetch(`/api/appointments/by-date/${date}`)
        .then(res => res.json())
        .then(data => {
            const doctorApts = data.filter(apt => apt.doctor_id === doctorId && apt.status !== 'cancelled');
            // Handle display here
        })
        .catch(err => console.error('Error:', err));
}

function updateDayDisplay() {
    const daySelect = document.getElementById('day_of_week').value;
    const dayMap = {
        'Monday': 1,
        'Tuesday': 2,
        'Wednesday': 3,
        'Thursday': 4,
        'Friday': 5,
        'Saturday': 6,
        'Sunday': 0
    };

    const targetDay = dayMap[daySelect];
    const upcomingDates = [];
    const today = new Date();
    
    // Find next 4 occurrences of this day
    for (let i = 0; i < 90; i++) {
        const date = new Date(today);
        date.setDate(date.getDate() + i);
        
        if (date.getDay() === targetDay && upcomingDates.length < 4) {
            upcomingDates.push(date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric' 
            }));
        }
    }

    const dateInfo = document.getElementById('dayDateInfo');
    dateInfo.innerHTML = `<strong>Upcoming ${daySelect}s:</strong> ${upcomingDates.join(' • ')}`;
    dateInfo.style.color = '#666';
    dateInfo.style.fontWeight = '500';
}

// Initialize day display on page load
document.addEventListener('DOMContentLoaded', function() {
    updateDayDisplay();
});
</script>
@endsection
