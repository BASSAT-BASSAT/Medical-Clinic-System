@extends('layouts.dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-600 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">Admin Dashboard</h1>
                        <p class="text-sm text-gray-600">Welcome, {{ Auth::user()->name }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Navigation Tabs -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex gap-8">
                <button onclick="switchTab('overview')" data-tab="overview" class="tab-btn py-4 border-b-2 border-blue-600 text-blue-600 transition font-medium">
                    Overview
                </button>
                <button onclick="switchTab('doctors')" data-tab="doctors" class="tab-btn py-4 border-b-2 border-transparent text-gray-600 hover:text-gray-900 transition font-medium">
                    Doctors
                </button>
                <button onclick="switchTab('patients')" data-tab="patients" class="tab-btn py-4 border-b-2 border-transparent text-gray-600 hover:text-gray-900 transition font-medium">
                    Patients
                </button>
                <button onclick="switchTab('appointments')" data-tab="appointments" class="tab-btn py-4 border-b-2 border-transparent text-gray-600 hover:text-gray-900 transition font-medium">
                    All Appointments
                </button>
                <button onclick="switchTab('reports')" data-tab="reports" class="tab-btn py-4 border-b-2 border-transparent text-gray-600 hover:text-gray-900 transition font-medium">
                    Reports
                </button>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Overview Tab -->
        <div id="tab-overview" class="tab-content space-y-6">
            <h2 class="text-xl font-semibold text-gray-900">System Overview</h2>

            <!-- Stats Grid -->
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Patients</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalPatients }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-green-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Doctors</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalDoctors }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-purple-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Appointments</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalAppointments }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointment Status -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Appointment Statistics</h3>
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="p-4 bg-green-50 rounded-lg">
                        <p class="text-sm text-green-600 mb-1">Scheduled</p>
                        <p class="text-2xl font-bold text-green-900" id="scheduled-count">0</p>
                    </div>
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm text-blue-600 mb-1">Completed</p>
                        <p class="text-2xl font-bold text-blue-900" id="completed-count">0</p>
                    </div>
                    <div class="p-4 bg-red-50 rounded-lg">
                        <p class="text-sm text-red-600 mb-1">Cancelled</p>
                        <p class="text-2xl font-bold text-red-900" id="cancelled-count">0</p>
                    </div>
                </div>
            </div>

            <!-- Recent Appointments -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Appointments</h3>
                <div id="recent-appointments" class="space-y-3">
                    @forelse($upcomingAppointments->take(5) as $appointment)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div>
                            <p class="font-medium text-gray-900">{{ $appointment->patient->first_name ?? '-' }} {{ $appointment->patient->last_name ?? '' }}</p>
                            <p class="text-sm text-gray-600">Dr. {{ $appointment->doctor->first_name ?? '-' }} {{ $appointment->doctor->last_name ?? '' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($appointment->start_time)->format('M d, Y') }}</p>
                            <span class="inline-block px-2 py-1 text-xs rounded-full 
                                @if($appointment->status === 'scheduled') bg-green-100 text-green-800
                                @elseif($appointment->status === 'completed') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">No recent appointments</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Doctors Tab -->
        <div id="tab-doctors" class="tab-content hidden space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">Doctors Management</h2>
                <button onclick="openDoctorModal()" class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Add New Doctor
                </button>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Specialization</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Phone</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                            </tr>
                        </thead>
                        <tbody id="doctors-table-body" class="divide-y divide-gray-200">
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Patients Tab -->
        <div id="tab-patients" class="tab-content hidden space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">Patients Management</h2>
                <button onclick="openPatientModal()" class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Add New Patient
                </button>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Phone</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date of Birth</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                            </tr>
                        </thead>
                        <tbody id="patients-table-body" class="divide-y divide-gray-200">
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- All Appointments Tab -->
        <div id="tab-appointments" class="tab-content hidden space-y-6">
            <h2 class="text-xl font-semibold text-gray-900">All Appointments</h2>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Time</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Patient</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Doctor</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Reason</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                            </tr>
                        </thead>
                        <tbody id="all-appointments-table" class="divide-y divide-gray-200">
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Reports Tab -->
        <div id="tab-reports" class="tab-content hidden space-y-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Analytics & Reports</h2>
            
            <!-- Auto-generated Analytics -->
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <!-- Appointments Overview Chart -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Appointments Overview</h3>
                    <div id="admin-appointments-chart" class="h-64">
                        <!-- Chart rendered by JS -->
                    </div>
                </div>

                <!-- Status Distribution Pie Chart -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Distribution</h3>
                    <div id="admin-status-chart" class="h-64 flex items-center justify-center">
                        <!-- Pie chart rendered by JS -->
                    </div>
                </div>
            </div>

            <!-- Monthly Trends -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Appointment Trends</h3>
                <div id="admin-monthly-chart" class="h-64">
                    <!-- Bar chart rendered by JS -->
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="grid md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                    <p class="text-sm opacity-80">Avg Appointments/Day</p>
                    <p class="text-2xl font-bold mt-1" id="admin-avg-daily">0</p>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                    <p class="text-sm opacity-80">Completion Rate</p>
                    <p class="text-2xl font-bold mt-1" id="admin-completion-rate">0%</p>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
                    <p class="text-sm opacity-80">Active Doctors</p>
                    <p class="text-2xl font-bold mt-1" id="admin-active-doctors">0</p>
                </div>
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-4 text-white">
                    <p class="text-sm opacity-80">Total Revenue Est.</p>
                    <p class="text-2xl font-bold mt-1" id="admin-revenue">$0</p>
                </div>
            </div>

            <!-- Doctor Performance -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Doctor Performance</h3>
                <div id="doctor-performance" class="space-y-3">
                    <!-- Filled by JS -->
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- System Health -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">System Health</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Database Status</span>
                            <span class="text-green-600 font-medium flex items-center gap-1">
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                Healthy
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">API Status</span>
                            <span class="text-green-600 font-medium flex items-center gap-1">
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                Running
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Email Service</span>
                            <span class="text-green-600 font-medium flex items-center gap-1">
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                Configured
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">This Week Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">New Patients</span>
                            <span class="font-semibold text-gray-900" id="new-patients-week">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Appointments Booked</span>
                            <span class="font-semibold text-gray-900" id="appointments-week">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Cancellation Rate</span>
                            <span class="font-semibold text-gray-900" id="cancellation-rate">0%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Add Doctor Modal -->
<div id="doctorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Add New Doctor</h2>
            <button onclick="closeDoctorModal()" class="p-2 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="doctorForm" class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" id="doctor_first_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input type="text" id="doctor_last_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Specialty</label>
                <select id="specialty_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <option value="">-- Select Specialty --</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="doctor_email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Add Doctor
                </button>
                <button type="button" onclick="closeDoctorModal()" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Patient Modal -->
<div id="patientModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Add New Patient</h2>
            <button onclick="closePatientModal()" class="p-2 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="patientForm" class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" id="patient_first_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input type="text" id="patient_last_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                <input type="date" id="patient_dob" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="patient_email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    Add Patient
                </button>
                <button type="button" onclick="closePatientModal()" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast-container" class="fixed top-4 right-4 z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl border border-gray-200 p-4 min-w-[320px] max-w-md flex items-start gap-3">
        <div id="toast-icon" class="flex-shrink-0 w-6 h-6"></div>
        <div class="flex-1">
            <h4 id="toast-title" class="font-semibold text-gray-900"></h4>
            <p id="toast-message" class="text-sm text-gray-600 mt-1"></p>
        </div>
        <button onclick="hideToast()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

<!-- Credentials Modal (shows after creating doctor/patient) -->
<div id="credentialsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-xl max-w-md w-full overflow-hidden shadow-2xl">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="bg-white bg-opacity-20 p-2 rounded-full">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-white">Account Created Successfully!</h2>
                    <p class="text-green-100 text-sm">Login credentials generated</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <p class="text-gray-600 text-sm mb-4">
                    A new <span id="cred-role" class="font-semibold text-gray-900"></span> account has been created. 
                    Share these credentials with the user:
                </p>
            </div>
            
            <div class="space-y-3">
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Name</label>
                    <p id="cred-name" class="text-gray-900 font-medium"></p>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Email</label>
                    <div class="flex items-center justify-between">
                        <p id="cred-email" class="text-gray-900 font-mono"></p>
                        <button onclick="copyToClipboard('cred-email')" class="text-blue-600 hover:text-blue-800 p-1" title="Copy email">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <label class="block text-xs font-medium text-yellow-700 uppercase tracking-wide mb-1">Temporary Password</label>
                    <div class="flex items-center justify-between">
                        <p id="cred-password" class="text-yellow-900 font-mono font-bold text-lg"></p>
                        <button onclick="copyToClipboard('cred-password')" class="text-yellow-700 hover:text-yellow-900 p-1" title="Copy password">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <div id="email-status" class="mt-4 p-3 rounded-lg text-sm">
                <!-- Will be populated by JS -->
            </div>
            
            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-blue-800 text-sm">
                    <strong>⚠️ Security Note:</strong> The user should change this password after their first login.
                </p>
            </div>
            
            <div class="mt-6 flex gap-3">
                <button onclick="copyAllCredentials()" class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Copy All
                </button>
                <button onclick="closeCredentialsModal()" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Done
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Toast Notification System
function showToast(type, title, message, duration = 4000) {
    const container = document.getElementById('toast-container');
    const toastTitle = document.getElementById('toast-title');
    const toastMessage = document.getElementById('toast-message');
    const toastIcon = document.getElementById('toast-icon');
    
    const icons = {
        success: '<svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        error: '<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        warning: '<svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
        info: '<svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
    };
    
    toastIcon.innerHTML = icons[type] || icons.info;
    toastTitle.textContent = title;
    toastMessage.textContent = message;
    
    container.classList.remove('hidden');
    container.style.animation = 'slideIn 0.3s ease-out';
    
    if (window.toastTimeout) clearTimeout(window.toastTimeout);
    window.toastTimeout = setTimeout(hideToast, duration);
}

function hideToast() {
    const container = document.getElementById('toast-container');
    container.style.animation = 'slideOut 0.3s ease-out';
    setTimeout(() => container.classList.add('hidden'), 250);
}

// Credentials Modal Functions
function showCredentialsModal({ role, name, email, password, emailSent }) {
    document.getElementById('cred-role').textContent = role;
    document.getElementById('cred-name').textContent = name;
    document.getElementById('cred-email').textContent = email;
    document.getElementById('cred-password').textContent = password;
    
    // Show email status
    const emailStatus = document.getElementById('email-status');
    if (emailSent) {
        emailStatus.className = 'mt-4 p-3 rounded-lg text-sm bg-green-50 border border-green-200';
        emailStatus.innerHTML = `
            <div class="flex items-center gap-2 text-green-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <span><strong>Email sent!</strong> The user has received their login credentials via email.</span>
            </div>
        `;
    } else {
        emailStatus.className = 'mt-4 p-3 rounded-lg text-sm bg-orange-50 border border-orange-200';
        emailStatus.innerHTML = `
            <div class="flex items-center gap-2 text-orange-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span><strong>Email not sent.</strong> Please share these credentials manually with the user.</span>
            </div>
        `;
    }
    
    document.getElementById('credentialsModal').classList.remove('hidden');
}

function closeCredentialsModal() {
    document.getElementById('credentialsModal').classList.add('hidden');
}

function copyToClipboard(elementId) {
    const text = document.getElementById(elementId).textContent;
    navigator.clipboard.writeText(text).then(() => {
        showToast('success', 'Copied!', 'Text copied to clipboard');
    }).catch(() => {
        showToast('error', 'Error', 'Failed to copy to clipboard');
    });
}

function copyAllCredentials() {
    const email = document.getElementById('cred-email').textContent;
    const password = document.getElementById('cred-password').textContent;
    const name = document.getElementById('cred-name').textContent;
    
    const text = `MediCare Login Credentials
Name: ${name}
Email: ${email}
Temporary Password: ${password}

Please change your password after first login.
Login at: ${window.location.origin}/login`;
    
    navigator.clipboard.writeText(text).then(() => {
        showToast('success', 'Copied!', 'All credentials copied to clipboard');
    }).catch(() => {
        showToast('error', 'Error', 'Failed to copy to clipboard');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    loadDoctors();
    loadPatients();
    loadSpecialties();
    loadAppointmentStats();
    loadAllAppointments();
});

// Tab switching
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-600', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-600');
    });
    
    document.getElementById(`tab-${tabName}`).classList.remove('hidden');
    
    const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
    activeBtn.classList.remove('border-transparent', 'text-gray-600');
    activeBtn.classList.add('border-blue-600', 'text-blue-600');
}

function loadDoctors() {
    fetch('/api/doctors')
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('doctors-table-body');
            const doctors = data.data || data;
            if (doctors.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No doctors found</td></tr>';
                return;
            }
            tbody.innerHTML = doctors.map(doc => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">Dr. ${doc.first_name} ${doc.last_name}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${doc.specialty?.name || 'N/A'}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${doc.email}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${doc.phone || 'N/A'}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                    </td>
                </tr>
            `).join('');
        })
        .catch(err => console.error('Error:', err));
}

function loadPatients() {
    fetch('/api/patients')
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('patients-table-body');
            const patients = data.data || data;
            if (patients.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No patients found</td></tr>';
                return;
            }
            tbody.innerHTML = patients.map(pat => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${pat.first_name} ${pat.last_name}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${pat.email}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${pat.phone || 'N/A'}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${pat.dob ? new Date(pat.dob).toLocaleDateString() : 'N/A'}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                    </td>
                </tr>
            `).join('');
        })
        .catch(err => console.error('Error:', err));
}

function loadSpecialties() {
    fetch('/api/specialties')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('specialty_id');
            const specialties = data.data || data;
            select.innerHTML = '<option value="">-- Select Specialty --</option>' +
                specialties.map(spec => `<option value="${spec.specialty_id}">${spec.name}</option>`).join('');
        })
        .catch(err => console.error('Error:', err));
}

function loadAppointmentStats() {
    fetch('/api/appointments')
        .then(res => res.json())
        .then(data => {
            const appointments = data.data || data;
            const scheduled = appointments.filter(a => a.status === 'scheduled').length;
            const completed = appointments.filter(a => a.status === 'completed').length;
            const cancelled = appointments.filter(a => a.status === 'cancelled').length;
            
            document.getElementById('scheduled-count').textContent = scheduled;
            document.getElementById('completed-count').textContent = completed;
            document.getElementById('cancelled-count').textContent = cancelled;
        })
        .catch(err => console.error('Error:', err));
}

function loadAllAppointments() {
    fetch('/api/appointments')
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('all-appointments-table');
            const appointments = data.data || data;
            if (appointments.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No appointments found</td></tr>';
                return;
            }
            tbody.innerHTML = appointments.map(apt => {
                const statusColors = {
                    'scheduled': 'bg-green-100 text-green-800',
                    'completed': 'bg-blue-100 text-blue-800',
                    'cancelled': 'bg-red-100 text-red-800'
                };
                return `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">${new Date(apt.start_time).toLocaleDateString()}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">${new Date(apt.start_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">${apt.patient?.first_name || ''} ${apt.patient?.last_name || ''}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">Dr. ${apt.doctor?.first_name || ''} ${apt.doctor?.last_name || ''}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">${apt.reason || 'N/A'}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-medium rounded-full ${statusColors[apt.status] || 'bg-gray-100 text-gray-800'}">${apt.status}</span>
                        </td>
                    </tr>
                `;
            }).join('');
        })
        .catch(err => console.error('Error:', err));
}

function openDoctorModal() {
    document.getElementById('doctorModal').classList.remove('hidden');
}

function closeDoctorModal() {
    document.getElementById('doctorModal').classList.add('hidden');
    document.getElementById('doctorForm').reset();
}

function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
    document.getElementById('patientForm').reset();
}

function generateReport() {
    const type = document.getElementById('reportType').value;
    const date = document.getElementById('reportDate').value;

    if (!type || !date) {
        showToast('warning', 'Missing Fields', 'Please select report type and date');
        return;
    }

    window.open(`/api/reports/${type}/${date}`, '_blank');
}

document.getElementById('doctorForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const data = {
        first_name: document.getElementById('doctor_first_name').value,
        last_name: document.getElementById('doctor_last_name').value,
        specialty_id: document.getElementById('specialty_id').value,
        email: document.getElementById('doctor_email').value
    };

    fetch('/api/doctors', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then((result) => {
        closeDoctorModal();
        loadDoctors();
        
        // Show credentials modal with generated password
        if (result.generated_password) {
            showCredentialsModal({
                role: 'Doctor',
                name: `Dr. ${data.first_name} ${data.last_name}`,
                email: data.email,
                password: result.generated_password,
                emailSent: result.email_sent
            });
        } else {
            showToast('success', 'Success', 'Doctor added successfully!');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showToast('error', 'Error', 'Failed to add doctor');
    });
});

document.getElementById('patientForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const data = {
        first_name: document.getElementById('patient_first_name').value,
        last_name: document.getElementById('patient_last_name').value,
        dob: document.getElementById('patient_dob').value,
        email: document.getElementById('patient_email').value
    };

    fetch('/api/patients', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then((result) => {
        closePatientModal();
        loadPatients();
        
        // Show credentials modal with generated password
        if (result.generated_password) {
            showCredentialsModal({
                role: 'Patient',
                name: `${data.first_name} ${data.last_name}`,
                email: data.email,
                password: result.generated_password,
                emailSent: result.email_sent
            });
        } else {
            showToast('success', 'Success', 'Patient added successfully!');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showToast('error', 'Error', 'Failed to add patient');
    });
});

// =============== AUTO-GENERATED REPORTS & CHARTS ===============

function loadReportAnalytics() {
    Promise.all([
        fetch('/api/appointments').then(r => r.json()),
        fetch('/api/doctors').then(r => r.json()),
        fetch('/api/patients').then(r => r.json())
    ]).then(([appointmentsData, doctorsData, patientsData]) => {
        const appointments = appointmentsData.data || appointmentsData;
        const doctors = doctorsData.data || doctorsData;
        const patients = patientsData.data || patientsData;

        // Calculate stats
        const scheduled = appointments.filter(a => a.status === 'scheduled').length;
        const completed = appointments.filter(a => a.status === 'completed').length;
        const cancelled = appointments.filter(a => a.status === 'cancelled').length;
        const total = appointments.length;

        // Calculate this week's stats
        const now = new Date();
        const weekStart = new Date(now);
        weekStart.setDate(now.getDate() - 7);
        
        const thisWeekAppointments = appointments.filter(a => new Date(a.start_time) >= weekStart);
        const newPatientsWeek = patients.filter(p => new Date(p.created_at) >= weekStart).length;

        // Update metrics
        document.getElementById('admin-avg-daily').textContent = (total / 30).toFixed(1);
        document.getElementById('admin-completion-rate').textContent = total > 0 ? Math.round((completed / total) * 100) + '%' : '0%';
        document.getElementById('admin-active-doctors').textContent = doctors.length;
        document.getElementById('admin-revenue').textContent = '$' + (completed * 75).toLocaleString(); // $75 per visit estimate

        document.getElementById('new-patients-week').textContent = newPatientsWeek;
        document.getElementById('appointments-week').textContent = thisWeekAppointments.length;
        document.getElementById('cancellation-rate').textContent = total > 0 ? Math.round((cancelled / total) * 100) + '%' : '0%';

        // Draw charts
        drawStatusPieChart(scheduled, completed, cancelled);
        drawMonthlyBarChart(appointments);
        drawWeeklyLineChart(appointments);
        loadDoctorPerformance(appointments, doctors);
    }).catch(err => console.error('Error loading analytics:', err));
}

function drawStatusPieChart(scheduled, completed, cancelled) {
    const container = document.getElementById('admin-status-chart');
    const total = scheduled + completed + cancelled;
    
    if (total === 0) {
        container.innerHTML = '<p class="text-gray-500">No appointment data</p>';
        return;
    }

    const canvas = document.createElement('canvas');
    canvas.width = 200;
    canvas.height = 200;
    container.innerHTML = '';
    
    const legendHTML = `
        <div class="ml-6 space-y-2">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                <span class="text-sm text-gray-700">Scheduled: ${scheduled} (${Math.round(scheduled/total*100)}%)</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                <span class="text-sm text-gray-700">Completed: ${completed} (${Math.round(completed/total*100)}%)</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                <span class="text-sm text-gray-700">Cancelled: ${cancelled} (${Math.round(cancelled/total*100)}%)</span>
            </div>
        </div>
    `;
    
    container.appendChild(canvas);
    container.insertAdjacentHTML('beforeend', legendHTML);
    
    const ctx = canvas.getContext('2d');
    const centerX = 100;
    const centerY = 100;
    const radius = 80;
    
    const data = [
        { value: scheduled, color: '#22c55e' },
        { value: completed, color: '#3b82f6' },
        { value: cancelled, color: '#ef4444' }
    ];
    
    let startAngle = -0.5 * Math.PI;
    
    data.forEach(segment => {
        if (segment.value > 0) {
            const sliceAngle = (segment.value / total) * 2 * Math.PI;
            ctx.beginPath();
            ctx.moveTo(centerX, centerY);
            ctx.arc(centerX, centerY, radius, startAngle, startAngle + sliceAngle);
            ctx.closePath();
            ctx.fillStyle = segment.color;
            ctx.fill();
            startAngle += sliceAngle;
        }
    });
}

function drawMonthlyBarChart(appointments) {
    const container = document.getElementById('admin-monthly-chart');
    container.innerHTML = '';
    
    // Group by month
    const monthlyData = {};
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    appointments.forEach(apt => {
        const date = new Date(apt.start_time);
        const monthKey = months[date.getMonth()];
        monthlyData[monthKey] = (monthlyData[monthKey] || 0) + 1;
    });
    
    // Get last 6 months
    const currentMonth = new Date().getMonth();
    const last6Months = [];
    for (let i = 5; i >= 0; i--) {
        const monthIdx = (currentMonth - i + 12) % 12;
        last6Months.push(months[monthIdx]);
    }
    
    const maxValue = Math.max(...last6Months.map(m => monthlyData[m] || 0), 1);
    
    const chartHTML = `
        <div class="flex items-end justify-around h-48 px-4">
            ${last6Months.map(month => {
                const value = monthlyData[month] || 0;
                const height = (value / maxValue) * 100;
                return `
                    <div class="flex flex-col items-center">
                        <span class="text-xs text-gray-600 mb-1">${value}</span>
                        <div class="w-12 bg-blue-500 rounded-t transition-all" style="height: ${Math.max(height, 5)}%"></div>
                        <span class="text-xs text-gray-600 mt-2">${month}</span>
                    </div>
                `;
            }).join('')}
        </div>
    `;
    
    container.innerHTML = chartHTML;
}

function drawWeeklyLineChart(appointments) {
    const container = document.getElementById('admin-appointments-chart');
    container.innerHTML = '';
    
    // Get last 7 days
    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const dailyData = {};
    const last7Days = [];
    
    for (let i = 6; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        const key = date.toISOString().split('T')[0];
        last7Days.push({ key, day: days[date.getDay()] });
        dailyData[key] = 0;
    }
    
    appointments.forEach(apt => {
        const key = new Date(apt.start_time).toISOString().split('T')[0];
        if (dailyData.hasOwnProperty(key)) {
            dailyData[key]++;
        }
    });
    
    const values = last7Days.map(d => dailyData[d.key]);
    const maxValue = Math.max(...values, 1);
    
    const chartHTML = `
        <div class="flex items-end justify-around h-48 px-4">
            ${last7Days.map((d, i) => {
                const value = values[i];
                const height = (value / maxValue) * 100;
                return `
                    <div class="flex flex-col items-center">
                        <span class="text-xs text-gray-600 mb-1">${value}</span>
                        <div class="w-8 bg-purple-500 rounded-t transition-all" style="height: ${Math.max(height, 5)}%"></div>
                        <span class="text-xs text-gray-600 mt-2">${d.day}</span>
                    </div>
                `;
            }).join('')}
        </div>
    `;
    
    container.innerHTML = chartHTML;
}

function loadDoctorPerformance(appointments, doctors) {
    const container = document.getElementById('doctor-performance');
    
    if (doctors.length === 0) {
        container.innerHTML = '<p class="text-gray-500">No doctors found</p>';
        return;
    }
    
    const doctorStats = doctors.map(doc => {
        const docAppointments = appointments.filter(a => a.doctor_id === doc.id);
        const completed = docAppointments.filter(a => a.status === 'completed').length;
        const total = docAppointments.length;
        return {
            name: `Dr. ${doc.first_name} ${doc.last_name}`,
            specialty: doc.specialty?.name || 'General',
            total,
            completed,
            rate: total > 0 ? Math.round((completed / total) * 100) : 0
        };
    }).sort((a, b) => b.total - a.total);
    
    const maxTotal = Math.max(...doctorStats.map(d => d.total), 1);
    
    container.innerHTML = doctorStats.slice(0, 5).map(doc => `
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-gray-900">${doc.name}</span>
                    <span class="text-sm text-gray-600">${doc.total} appts (${doc.rate}% completed)</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: ${(doc.total / maxTotal) * 100}%"></div>
                </div>
            </div>
        </div>
    `).join('');
}

// Load reports on tab switch
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if (this.dataset.tab === 'reports') {
            loadReportAnalytics();
        }
    });
});

// Also load if reports tab is opened directly
if (document.getElementById('tab-reports')?.classList.contains('hidden') === false) {
    loadReportAnalytics();
}
</script>
@endsection
