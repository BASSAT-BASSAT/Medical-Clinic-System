@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Reports</h1>
                <p class="text-gray-600 mt-2">View your performance and appointment statistics</p>
            </div>
            <a href="{{ route('doctor.dashboard') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                ← Back to Dashboard
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Total Appointments</p>
                <p class="text-3xl font-bold text-blue-600 mt-2" id="total-appointments">0</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Completed</p>
                <p class="text-3xl font-bold text-green-600 mt-2" id="completed-count">0</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Cancelled</p>
                <p class="text-3xl font-bold text-red-600 mt-2" id="cancelled-count">0</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Completion Rate</p>
                <p class="text-3xl font-bold text-purple-600 mt-2" id="completion-rate">0%</p>
            </div>
        </div>

        <!-- Report Generator -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Generate Report</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                    <select id="reportType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500">
                        <option value="">-- Select Report Type --</option>
                        <option value="daily">Daily Report</option>
                        <option value="weekly">Weekly Report</option>
                        <option value="monthly">Monthly Report</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" id="reportDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                    <select id="reportFormat" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500">
                        <option value="json">JSON</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button onclick="generateReport()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        Generate
                    </button>
                </div>
            </div>
        </div>

        <!-- Appointments Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">All Appointments</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Patient</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Date & Time</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Reason</th>
                        </tr>
                    </thead>
                    <tbody id="appointments-table" class="divide-y divide-gray-200">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="pagination" class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                <button onclick="previousPage()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">← Previous</button>
                <span id="page-info">Page 1</span>
                <button onclick="nextPage()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Next →</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
const doctorId = {{ auth()->user()->doctor->doctor_id ?? 'null' }};

document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadAppointments();
});

function loadStats() {
    fetch(`/api/appointments/by-doctor/${doctorId}`)
        .then(res => res.json())
        .then(data => {
            const appointments = data.data || data;
            const total = appointments.length;
            const completed = appointments.filter(a => a.status === 'completed').length;
            const cancelled = appointments.filter(a => a.status === 'cancelled').length;
            const completionRate = total > 0 ? Math.round((completed / total) * 100) : 0;
            
            document.getElementById('total-appointments').textContent = total;
            document.getElementById('completed-count').textContent = completed;
            document.getElementById('cancelled-count').textContent = cancelled;
            document.getElementById('completion-rate').textContent = completionRate + '%';
        })
        .catch(err => console.error('Error:', err));
}

function loadAppointments() {
    fetch(`/api/appointments/by-doctor/${doctorId}`)
        .then(res => res.json())
        .then(data => {
            const appointments = data.data || data;
            const table = document.getElementById('appointments-table');
            
            if (appointments.length === 0) {
                table.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">No appointments</td></tr>';
                return;
            }
            
            table.innerHTML = appointments.map(apt => `
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-900">${apt.patient?.first_name || '-'} ${apt.patient?.last_name || ''}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${new Date(apt.start_time).toLocaleString()}</td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-3 py-1 rounded-full text-xs font-medium ${
                            apt.status === 'completed' ? 'bg-green-100 text-green-800' :
                            apt.status === 'cancelled' ? 'bg-red-100 text-red-800' :
                            'bg-blue-100 text-blue-800'
                        }">${apt.status}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">${apt.reason || '-'}</td>
                </tr>
            `).join('');
        })
        .catch(err => console.error('Error:', err));
}

function generateReport() {
    const type = document.getElementById('reportType').value;
    const date = document.getElementById('reportDate').value;
    const format = document.getElementById('reportFormat').value;

    if (!type || !date) {
        alert('Please select report type and date');
        return;
    }

    window.open(`/api/reports/doctor/${date}?format=${format}`, '_blank');
}

function previousPage() {
    if (currentPage > 1) {
        currentPage--;
        loadAppointments();
    }
}

function nextPage() {
    currentPage++;
    loadAppointments();
}
</script>
@endsection
