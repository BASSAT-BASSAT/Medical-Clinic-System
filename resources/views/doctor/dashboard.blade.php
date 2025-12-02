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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-gray-900">Doctor Dashboard</h1>
                        <p class="text-gray-600">Welcome, Dr. {{ Auth::user()->doctor->first_name ?? 'Doctor' }} {{ Auth::user()->doctor->last_name ?? '' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
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
        </div>
    </header>

    <!-- Stats - Exactly like Figma with inline grid style -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-blue-600 mb-1">Total Appointments</p>
                    <p class="text-blue-900 text-2xl font-bold" id="stat-total">0</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-green-600 mb-1">Scheduled</p>
                    <p class="text-green-900 text-2xl font-bold" id="stat-scheduled">0</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <p class="text-purple-600 mb-1">Completed</p>
                    <p class="text-purple-900 text-2xl font-bold" id="stat-completed">0</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <p class="text-red-600 mb-1">Cancelled</p>
                    <p class="text-red-900 text-2xl font-bold" id="stat-cancelled">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs - Exactly like Figma -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex gap-8">
                <button onclick="setActiveTab('today')" id="tab-today" class="tab-btn py-4 border-b-2 border-blue-600 text-blue-600 transition">
                    Today's Schedule
                </button>
                <button onclick="setActiveTab('calendar')" id="tab-calendar" class="tab-btn py-4 border-b-2 border-transparent text-gray-600 hover:text-gray-900 transition">
                    Calendar View
                </button>
                <button onclick="setActiveTab('reports')" id="tab-reports" class="tab-btn py-4 border-b-2 border-transparent text-gray-600 hover:text-gray-900 transition">
                    Reports
                </button>
                <button onclick="setActiveTab('records')" id="tab-records" class="tab-btn py-4 border-b-2 border-transparent text-gray-600 hover:text-gray-900 transition">
                    Patient Records
                </button>
                <button onclick="setActiveTab('availability')" id="tab-availability" class="tab-btn py-4 border-b-2 border-transparent text-gray-600 hover:text-gray-900 transition">
                    Set Availability
                </button>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Today's Schedule Tab -->
        <div id="content-today" class="tab-content">
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-gray-900 text-xl font-semibold">Today's Appointments</h2>
                    <p class="text-gray-600" id="today-date"></p>
                </div>

                <div id="today-appointments" class="space-y-4">
                    <!-- Filled by JS -->
                </div>
            </div>
        </div>

        <!-- Calendar View Tab -->
        <div id="content-calendar" class="tab-content hidden">
            <div class="space-y-6">
                <h2 class="text-gray-900 text-xl font-semibold">Calendar View</h2>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-gray-900 text-lg font-semibold" id="calendar-month"></h3>
                        <div class="flex gap-2">
                            <button onclick="prevMonth()" class="p-2 hover:bg-gray-100 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <button onclick="nextMonth()" class="p-2 hover:bg-gray-100 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Calendar Grid - 7 columns with inline style to guarantee it works -->
                    <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; background-color: #e5e7eb; border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden;">
                        <!-- Day Headers -->
                        <div class="bg-gray-50 p-2 text-center text-gray-700 font-medium">Sun</div>
                        <div class="bg-gray-50 p-2 text-center text-gray-700 font-medium">Mon</div>
                        <div class="bg-gray-50 p-2 text-center text-gray-700 font-medium">Tue</div>
                        <div class="bg-gray-50 p-2 text-center text-gray-700 font-medium">Wed</div>
                        <div class="bg-gray-50 p-2 text-center text-gray-700 font-medium">Thu</div>
                        <div class="bg-gray-50 p-2 text-center text-gray-700 font-medium">Fri</div>
                        <div class="bg-gray-50 p-2 text-center text-gray-700 font-medium">Sat</div>
                        <!-- Calendar Days will be inserted here by JS -->
                    </div>
                    <div id="calendar-days" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; background-color: #e5e7eb; border: 1px solid #e5e7eb; border-top: none;">
                        <!-- Filled by JS -->
                    </div>

                    <div class="mt-4 flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-green-100 border border-green-200 rounded"></div>
                            <span class="text-gray-600 text-sm">Scheduled</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-blue-100 border border-blue-200 rounded"></div>
                            <span class="text-gray-600 text-sm">Completed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Tab -->
        <div id="content-reports" class="tab-content hidden">
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-gray-900 text-xl font-semibold">Appointment Reports</h2>
                    <button onclick="downloadReport()" class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download Report
                    </button>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <h3 class="text-gray-900 font-medium">Filters</h3>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        <div>
                            <label class="block text-gray-700 mb-2">Status</label>
                            <select id="filterStatus" onchange="filterAppointments()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                                <option value="all">All</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Date Range</label>
                            <select id="filterDate" onchange="filterAppointments()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Report Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-gray-700">Date</th>
                                    <th class="px-6 py-3 text-left text-gray-700">Time</th>
                                    <th class="px-6 py-3 text-left text-gray-700">Patient</th>
                                    <th class="px-6 py-3 text-left text-gray-700">Reason</th>
                                    <th class="px-6 py-3 text-left text-gray-700">Status</th>
                                </tr>
                            </thead>
                            <tbody id="report-table" class="divide-y divide-gray-200">
                                <!-- Filled by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Records Tab - Full Featured -->
        <div id="content-records" class="tab-content hidden">
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
                <!-- Patients List -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-gray-900 font-semibold mb-4">Your Patients</h3>
                    
                    <!-- Search -->
                    <div class="relative mb-4">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" id="patientSearch" placeholder="Search patients..." oninput="searchPatients()" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                    </div>

                    <!-- Patient List -->
                    <div id="patient-list" class="space-y-2 max-h-96 overflow-y-auto">
                        <!-- Filled by JS -->
                    </div>
                </div>

                <!-- Patient Records Panel -->
                <div id="patient-records-panel">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-gray-900 font-semibold mb-2">Select a Patient</h3>
                        <p class="text-gray-600">Choose a patient from the list to view and manage their medical records</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Set Availability Tab -->
        <div id="content-availability" class="tab-content hidden">
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-gray-900 text-xl font-semibold">Set Your Availability</h2>
                    <button onclick="saveAvailability()" class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save Availability
                    </button>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <p class="text-gray-600 mb-6">Set your weekly working hours. Patients will be able to book appointments during these times.</p>
                    
                    <div class="space-y-4" id="availability-form">
                        <!-- Monday -->
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                            <label class="flex items-center gap-2 w-32">
                                <input type="checkbox" id="day-1-enabled" class="w-4 h-4 text-blue-600 rounded" checked>
                                <span class="font-medium">Monday</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="time" id="day-1-start" value="09:00" class="px-3 py-2 border border-gray-300 rounded-lg">
                                <span class="text-gray-600">to</span>
                                <input type="time" id="day-1-end" value="17:00" class="px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                        
                        <!-- Tuesday -->
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                            <label class="flex items-center gap-2 w-32">
                                <input type="checkbox" id="day-2-enabled" class="w-4 h-4 text-blue-600 rounded" checked>
                                <span class="font-medium">Tuesday</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="time" id="day-2-start" value="09:00" class="px-3 py-2 border border-gray-300 rounded-lg">
                                <span class="text-gray-600">to</span>
                                <input type="time" id="day-2-end" value="17:00" class="px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                        
                        <!-- Wednesday -->
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                            <label class="flex items-center gap-2 w-32">
                                <input type="checkbox" id="day-3-enabled" class="w-4 h-4 text-blue-600 rounded" checked>
                                <span class="font-medium">Wednesday</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="time" id="day-3-start" value="09:00" class="px-3 py-2 border border-gray-300 rounded-lg">
                                <span class="text-gray-600">to</span>
                                <input type="time" id="day-3-end" value="17:00" class="px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                        
                        <!-- Thursday -->
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                            <label class="flex items-center gap-2 w-32">
                                <input type="checkbox" id="day-4-enabled" class="w-4 h-4 text-blue-600 rounded" checked>
                                <span class="font-medium">Thursday</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="time" id="day-4-start" value="09:00" class="px-3 py-2 border border-gray-300 rounded-lg">
                                <span class="text-gray-600">to</span>
                                <input type="time" id="day-4-end" value="17:00" class="px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                        
                        <!-- Friday -->
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                            <label class="flex items-center gap-2 w-32">
                                <input type="checkbox" id="day-5-enabled" class="w-4 h-4 text-blue-600 rounded" checked>
                                <span class="font-medium">Friday</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="time" id="day-5-start" value="09:00" class="px-3 py-2 border border-gray-300 rounded-lg">
                                <span class="text-gray-600">to</span>
                                <input type="time" id="day-5-end" value="17:00" class="px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                        
                        <!-- Saturday -->
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                            <label class="flex items-center gap-2 w-32">
                                <input type="checkbox" id="day-6-enabled" class="w-4 h-4 text-blue-600 rounded">
                                <span class="font-medium">Saturday</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="time" id="day-6-start" value="10:00" class="px-3 py-2 border border-gray-300 rounded-lg">
                                <span class="text-gray-600">to</span>
                                <input type="time" id="day-6-end" value="14:00" class="px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                        
                        <!-- Sunday -->
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                            <label class="flex items-center gap-2 w-32">
                                <input type="checkbox" id="day-0-enabled" class="w-4 h-4 text-blue-600 rounded">
                                <span class="font-medium">Sunday</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="time" id="day-0-start" value="10:00" class="px-3 py-2 border border-gray-300 rounded-lg">
                                <span class="text-gray-600">to</span>
                                <input type="time" id="day-0-end" value="14:00" class="px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Current Availability Display -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-gray-900 font-semibold mb-4">Current Availability</h3>
                    <div id="current-availability" class="space-y-2">
                        <p class="text-gray-500">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Add Medical Record Modal -->
<div id="addRecordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold">Add Medical Record</h3>
            <button onclick="closeAddRecordModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="addRecordForm" onsubmit="submitMedicalRecord(event)" class="p-6 space-y-4">
            <input type="hidden" id="record-patient-id">
            
            <!-- Link to Appointment (Optional) -->
            <div>
                <label class="block text-gray-700 mb-2">Link to Appointment (Optional)</label>
                <select id="record-appointment" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- No specific appointment --</option>
                </select>
                <p class="text-gray-500 text-xs mt-1">Link this record to a completed appointment</p>
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Diagnosis *</label>
                <input type="text" id="record-diagnosis" placeholder="e.g., Common cold, Migraine, Hypertension" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Prescription *</label>
                <textarea id="record-prescription" rows="3" placeholder="e.g., Paracetamol 500mg - 3 times daily for 5 days" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required></textarea>
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Additional Notes</label>
                <textarea id="record-notes" rows="3" placeholder="Follow-up in 2 weeks, rest recommended, avoid strenuous activity..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">
                Save Medical Record
            </button>
        </form>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="hidden fixed top-4 right-4 z-50 transform transition-all duration-300 translate-x-full">
    <div id="toast-content" class="flex items-center gap-3 px-6 py-4 rounded-lg shadow-lg">
        <div id="toast-icon"></div>
        <div>
            <p id="toast-title" class="font-semibold"></p>
            <p id="toast-message" class="text-sm"></p>
        </div>
        <button onclick="hideToast()" class="ml-4 text-current opacity-70 hover:opacity-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

<!-- Confirm Dialog Modal -->
<div id="confirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900" id="confirm-title">Confirm Action</h3>
                <p class="text-gray-600" id="confirm-message">Are you sure?</p>
            </div>
        </div>
        <div class="flex gap-3 justify-end">
            <button onclick="closeConfirmModal(false)" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                Cancel
            </button>
            <button onclick="closeConfirmModal(true)" id="confirm-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
const doctorId = {{ Auth::user()->doctor->doctor_id ?? 'null' }};
let allAppointments = [];
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let confirmCallback = null;

// ============ TOAST NOTIFICATIONS ============
function showToast(type, title, message, duration = 4000) {
    const toast = document.getElementById('toast');
    const toastContent = document.getElementById('toast-content');
    const toastIcon = document.getElementById('toast-icon');
    const toastTitle = document.getElementById('toast-title');
    const toastMessage = document.getElementById('toast-message');
    
    // Set content
    toastTitle.textContent = title;
    toastMessage.textContent = message;
    
    // Set styles based on type
    toastContent.className = 'flex items-center gap-3 px-6 py-4 rounded-lg shadow-lg ';
    
    if (type === 'success') {
        toastContent.classList.add('bg-green-50', 'text-green-800', 'border', 'border-green-200');
        toastIcon.innerHTML = '<svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
    } else if (type === 'error') {
        toastContent.classList.add('bg-red-50', 'text-red-800', 'border', 'border-red-200');
        toastIcon.innerHTML = '<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
    } else if (type === 'warning') {
        toastContent.classList.add('bg-yellow-50', 'text-yellow-800', 'border', 'border-yellow-200');
        toastIcon.innerHTML = '<svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
    } else {
        toastContent.classList.add('bg-blue-50', 'text-blue-800', 'border', 'border-blue-200');
        toastIcon.innerHTML = '<svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
    }
    
    // Show toast
    toast.classList.remove('hidden');
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 10);
    
    // Auto hide
    if (duration > 0) {
        setTimeout(() => hideToast(), duration);
    }
}

function hideToast() {
    const toast = document.getElementById('toast');
    toast.classList.add('translate-x-full');
    setTimeout(() => toast.classList.add('hidden'), 300);
}

// ============ CONFIRM DIALOG ============
function showConfirm(title, message, callback) {
    document.getElementById('confirm-title').textContent = title;
    document.getElementById('confirm-message').textContent = message;
    confirmCallback = callback;
    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeConfirmModal(confirmed) {
    document.getElementById('confirmModal').classList.add('hidden');
    if (confirmCallback) {
        confirmCallback(confirmed);
        confirmCallback = null;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Set today's date
    const today = new Date();
    document.getElementById('today-date').textContent = today.toLocaleDateString('en-US', { 
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
    });
    
    loadData();
});

function loadData() {
    if (!doctorId) {
        console.log('No doctor ID found');
        return;
    }
    
    fetch(`/api/appointments/by-doctor/${doctorId}`)
        .then(res => res.json())
        .then(data => {
            allAppointments = data;
            updateStats();
            loadTodayAppointments();
            renderCalendar();
            filterAppointments();
            loadPatients();
        })
        .catch(err => console.error('Error loading data:', err));
}

function updateStats() {
    const total = allAppointments.length;
    const scheduled = allAppointments.filter(a => a.status === 'scheduled').length;
    const completed = allAppointments.filter(a => a.status === 'completed').length;
    const cancelled = allAppointments.filter(a => a.status === 'cancelled').length;
    
    document.getElementById('stat-total').textContent = total;
    document.getElementById('stat-scheduled').textContent = scheduled;
    document.getElementById('stat-completed').textContent = completed;
    document.getElementById('stat-cancelled').textContent = cancelled;
}

function setActiveTab(tabName) {
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-600', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-600');
    });
    document.getElementById(`tab-${tabName}`).classList.add('border-blue-600', 'text-blue-600');
    document.getElementById(`tab-${tabName}`).classList.remove('border-transparent', 'text-gray-600');
    
    // Show/hide content
    document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
    document.getElementById(`content-${tabName}`).classList.remove('hidden');
}

function loadTodayAppointments() {
    const today = new Date().toISOString().split('T')[0];
    const todayApts = allAppointments.filter(apt => 
        apt.start_time && apt.start_time.includes(today) && apt.status !== 'cancelled'
    );
    
    const container = document.getElementById('today-appointments');
    
    if (todayApts.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-gray-600">No appointments scheduled for today</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = todayApts.map(apt => {
        const time = new Date(apt.start_time);
        const statusClass = apt.status === 'scheduled' ? 'bg-green-100 text-green-800' : 
                           apt.status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800';
        return `
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-gray-900 font-semibold">${apt.patient?.first_name || ''} ${apt.patient?.last_name || ''}</h3>
                            <span class="px-3 py-1 rounded-full text-sm ${statusClass}">${apt.status}</span>
                        </div>
                        <div class="space-y-2 text-gray-600">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                ${time.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}
                            </div>
                            <p class="text-gray-900">Reason: ${apt.reason || 'General Consultation'}</p>
                        </div>
                    </div>
                    ${apt.status === 'scheduled' ? `
                        <button onclick="markComplete(${apt.appointment_id})" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Mark Complete
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
    }).join('');
}

// Calendar Functions
function renderCalendar() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
    document.getElementById('calendar-month').textContent = `${monthNames[currentMonth]} ${currentYear}`;
    
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const today = new Date();
    
    let html = '';
    
    // Empty cells before first day
    for (let i = 0; i < firstDay; i++) {
        html += `<div style="min-height: 96px;" class="bg-gray-50 p-2"></div>`;
    }
    
    // Days of month
    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const isToday = day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear();
        const dayApts = allAppointments.filter(apt => apt.start_time && apt.start_time.startsWith(dateStr) && apt.status !== 'cancelled');
        
        html += `
            <div style="min-height: 96px;" class="${isToday ? 'bg-blue-50 border-blue-300' : 'bg-white'} border border-gray-200 p-2">
                <div class="${isToday ? 'text-blue-600' : 'text-gray-900'} font-medium mb-1">${day}</div>
                <div class="space-y-1">
                    ${dayApts.slice(0, 2).map(apt => {
                        const statusClass = apt.status === 'scheduled' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800';
                        const time = new Date(apt.start_time);
                        return `<div class="text-xs p-1 rounded ${statusClass}">${time.toLocaleTimeString('en-US', {hour: 'numeric', minute: '2-digit'})} - ${apt.patient?.first_name || 'Patient'}</div>`;
                    }).join('')}
                    ${dayApts.length > 2 ? `<div class="text-xs text-gray-600">+${dayApts.length - 2} more</div>` : ''}
                </div>
            </div>
        `;
    }
    
    document.getElementById('calendar-days').innerHTML = html;
}

function prevMonth() {
    currentMonth--;
    if (currentMonth < 0) { currentMonth = 11; currentYear--; }
    renderCalendar();
}

function nextMonth() {
    currentMonth++;
    if (currentMonth > 11) { currentMonth = 0; currentYear++; }
    renderCalendar();
}

// Reports Functions
function filterAppointments() {
    const status = document.getElementById('filterStatus').value;
    const dateRange = document.getElementById('filterDate').value;
    const today = new Date().toISOString().split('T')[0];
    
    let filtered = allAppointments.filter(apt => {
        if (status !== 'all' && apt.status !== status) return false;
        
        if (!apt.start_time) return false;
        const aptDate = apt.start_time.split('T')[0];
        
        if (dateRange === 'today') {
            return aptDate === today;
        } else if (dateRange === 'week') {
            const weekAgo = new Date();
            weekAgo.setDate(weekAgo.getDate() - 7);
            return new Date(aptDate) >= weekAgo;
        }
        return true;
    });
    
    const tbody = document.getElementById('report-table');
    
    if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-gray-600">No appointments found for selected filters</td></tr>';
        return;
    }
    
    tbody.innerHTML = filtered.map(apt => {
        const date = new Date(apt.start_time);
        const statusClass = apt.status === 'scheduled' ? 'bg-green-100 text-green-800' : 
                           apt.status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800';
        return `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-gray-900">${date.toLocaleDateString()}</td>
                <td class="px-6 py-4 text-gray-600">${date.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}</td>
                <td class="px-6 py-4 text-gray-900">${apt.patient?.first_name || ''} ${apt.patient?.last_name || ''}</td>
                <td class="px-6 py-4 text-gray-600">${apt.reason || 'General Consultation'}</td>
                <td class="px-6 py-4"><span class="px-3 py-1 rounded-full text-sm ${statusClass}">${apt.status}</span></td>
            </tr>
        `;
    }).join('');
}

function downloadReport() {
    showToast('info', 'Report Generated', 'Report data has been logged to console.');
    console.log('Report data:', allAppointments);
}

// ============ PATIENT RECORDS MANAGEMENT ============
let allPatientsList = [];
let selectedPatientId = null;

function loadPatients() {
    const patientMap = {};
    allAppointments.forEach(apt => {
        if (!apt.patient) return;
        const pid = apt.patient_id;
        if (!patientMap[pid]) {
            patientMap[pid] = { patient: apt.patient, visits: [], lastVisit: null };
        }
        patientMap[pid].visits.push(apt);
        const aptDate = new Date(apt.start_time);
        if (!patientMap[pid].lastVisit || aptDate > new Date(patientMap[pid].lastVisit)) {
            patientMap[pid].lastVisit = apt.start_time;
        }
    });
    
    allPatientsList = Object.values(patientMap);
    renderPatientList(allPatientsList);
}

function renderPatientList(patients) {
    const container = document.getElementById('patient-list');
    
    if (patients.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-600">No patients found</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = patients.map(p => `
        <button onclick="selectPatient(${p.patient.patient_id})" class="w-full text-left p-4 rounded-lg border-2 transition ${selectedPatientId === p.patient.patient_id ? 'border-blue-600 bg-blue-50' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'}">
            <p class="${selectedPatientId === p.patient.patient_id ? 'text-blue-900' : 'text-gray-900'} font-medium">${p.patient.first_name} ${p.patient.last_name}</p>
            <p class="text-gray-600 text-sm">${p.patient.email || 'No email'}</p>
            <p class="text-gray-500 text-xs mt-1">${p.visits.length} visit${p.visits.length !== 1 ? 's' : ''}</p>
        </button>
    `).join('');
}

function searchPatients() {
    const query = document.getElementById('patientSearch').value.toLowerCase();
    const filtered = allPatientsList.filter(p => 
        p.patient.first_name.toLowerCase().includes(query) ||
        p.patient.last_name.toLowerCase().includes(query) ||
        (p.patient.email && p.patient.email.toLowerCase().includes(query))
    );
    renderPatientList(filtered);
}

function selectPatient(patientId) {
    selectedPatientId = patientId;
    renderPatientList(allPatientsList);
    loadPatientRecords(patientId);
}

function loadPatientRecords(patientId) {
    const patientData = allPatientsList.find(p => p.patient.patient_id === patientId);
    if (!patientData) return;
    
    const panel = document.getElementById('patient-records-panel');
    
    // Show loading
    panel.innerHTML = `<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center"><p class="text-gray-500">Loading records...</p></div>`;
    
    // Fetch medical records for this patient
    fetch(`/api/medical-records/by-patient/${patientId}`)
        .then(res => res.json())
        .then(records => {
            renderPatientPanel(patientData, records);
        })
        .catch(err => {
            console.error('Error loading records:', err);
            renderPatientPanel(patientData, []);
        });
}

function renderPatientPanel(patientData, records) {
    const panel = document.getElementById('patient-records-panel');
    const patient = patientData.patient;
    
    panel.innerHTML = `
        <div class="space-y-6">
            <!-- Patient Info Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-gray-900 text-lg font-semibold mb-2">${patient.first_name} ${patient.last_name}</h3>
                        <div class="space-y-1 text-gray-600 text-sm">
                            <p>Email: ${patient.email || 'N/A'}</p>
                            <p>Phone: ${patient.phone || 'N/A'}</p>
                            <p>Date of Birth: ${patient.date_of_birth ? new Date(patient.date_of_birth).toLocaleDateString() : 'N/A'}</p>
                            <p>Total Visits: ${patientData.visits.length}</p>
                        </div>
                    </div>
                    <button onclick="openAddRecordModal(${patient.patient_id})" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Record
                    </button>
                </div>
            </div>

            <!-- Medical Records History -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-gray-900 font-semibold mb-4">Medical History</h3>
                
                ${records.length > 0 ? `
                    <div class="space-y-4">
                        ${records.map(record => `
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-gray-900 font-medium">${new Date(record.created_at || record.date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                                    </div>
                                    <span class="text-gray-600 text-sm">${record.doctor?.first_name ? 'Dr. ' + record.doctor.first_name + ' ' + record.doctor.last_name : 'Doctor'}</span>
                                </div>
                                
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-gray-700 text-sm font-medium mb-1">Diagnosis</p>
                                        <p class="text-gray-900">${record.diagnosis || 'N/A'}</p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-gray-700 text-sm font-medium mb-1">Prescription</p>
                                        <p class="text-gray-900">${record.prescription || 'N/A'}</p>
                                    </div>
                                    
                                    ${record.notes ? `
                                        <div>
                                            <p class="text-gray-700 text-sm font-medium mb-1">Notes</p>
                                            <p class="text-gray-600">${record.notes}</p>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        `).join('')}
                    </div>
                ` : `
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-600">No medical records found</p>
                        <p class="text-gray-500 text-sm">Click "Add Record" to create the first entry</p>
                    </div>
                `}
            </div>

            <!-- Visit History -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-gray-900 font-semibold mb-4">Appointment History</h3>
                <div class="space-y-2">
                    ${patientData.visits.map(v => `
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">${new Date(v.start_time).toLocaleDateString()}</p>
                                <p class="text-xs text-gray-500">${v.reason || 'General Consultation'}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full ${v.status === 'completed' ? 'bg-green-100 text-green-700' : v.status === 'scheduled' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700'}">${v.status}</span>
                        </div>
                    `).join('')}
                </div>
            </div>
        </div>
    `;
}

function openAddRecordModal(patientId) {
    document.getElementById('record-patient-id').value = patientId;
    
    // Populate appointments dropdown with this patient's completed appointments
    const patientData = allPatientsList.find(p => p.patient.patient_id === patientId);
    const appointmentSelect = document.getElementById('record-appointment');
    
    // Clear existing options except the first one
    appointmentSelect.innerHTML = '<option value="">-- No specific appointment --</option>';
    
    if (patientData && patientData.visits) {
        // Filter for completed appointments (most likely to need a record)
        const completedVisits = patientData.visits.filter(v => v.status === 'completed');
        completedVisits.forEach(apt => {
            const date = new Date(apt.start_time);
            const option = document.createElement('option');
            option.value = apt.appointment_id;
            option.textContent = `${date.toLocaleDateString()} - ${apt.reason || 'General Consultation'}`;
            appointmentSelect.appendChild(option);
        });
    }
    
    document.getElementById('addRecordModal').classList.remove('hidden');
}

function closeAddRecordModal() {
    document.getElementById('addRecordModal').classList.add('hidden');
    document.getElementById('addRecordForm').reset();
}

function submitMedicalRecord(event) {
    event.preventDefault();
    
    const patientId = document.getElementById('record-patient-id').value;
    const appointmentId = document.getElementById('record-appointment').value;
    
    const data = {
        patient_id: patientId,
        doctor_id: doctorId,
        diagnosis: document.getElementById('record-diagnosis').value,
        prescription: document.getElementById('record-prescription').value,
        notes: document.getElementById('record-notes').value
    };
    
    // Only include appointment_id if one was selected
    if (appointmentId) {
        data.appointment_id = appointmentId;
    }
    
    fetch('/api/medical-records', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => {
        if (!res.ok) throw new Error('Failed to save record');
        return res.json();
    })
    .then(() => {
        showToast('success', 'Success', 'Medical record added successfully!');
        closeAddRecordModal();
        loadPatientRecords(patientId);
    })
    .catch(err => {
        console.error('Error:', err);
        showToast('error', 'Error', 'Failed to add medical record');
    });
}

// ============ AVAILABILITY MANAGEMENT ============
function loadAvailability() {
    if (!doctorId) return;
    
    fetch(`/api/doctors/${doctorId}/availability`)
        .then(res => res.json())
        .then(data => {
            const availability = data.availability || data.data || data;
            displayCurrentAvailability(availability);
            populateAvailabilityForm(availability);
        })
        .catch(err => console.error('Error loading availability:', err));
}

function displayCurrentAvailability(availability) {
    const container = document.getElementById('current-availability');
    const dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    
    if (!availability || availability.length === 0) {
        container.innerHTML = '<p class="text-gray-500">No availability set yet. Configure your schedule above.</p>';
        return;
    }
    
    const sortedAvailability = availability.sort((a, b) => dayOrder.indexOf(a.day_of_week) - dayOrder.indexOf(b.day_of_week));
    
    container.innerHTML = sortedAvailability.map(slot => `
        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
            <span class="font-medium text-gray-900">${slot.day_of_week}</span>
            <span class="text-gray-600">${formatTime(slot.start_time)} - ${formatTime(slot.end_time)}</span>
        </div>
    `).join('');
}

function formatTime(time) {
    if (!time) return '';
    const [hours, minutes] = time.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const hour12 = hour % 12 || 12;
    return `${hour12}:${minutes} ${ampm}`;
}

function populateAvailabilityForm(availability) {
    const dayToNum = {'Sunday': 0, 'Monday': 1, 'Tuesday': 2, 'Wednesday': 3, 'Thursday': 4, 'Friday': 5, 'Saturday': 6};
    
    // Reset all checkboxes
    for (let i = 0; i <= 6; i++) {
        const checkbox = document.getElementById(`day-${i}-enabled`);
        if (checkbox) checkbox.checked = false;
    }
    
    // Set values from existing availability
    availability.forEach(slot => {
        const day = dayToNum[slot.day_of_week];
        if (day === undefined) return;
        
        const checkbox = document.getElementById(`day-${day}-enabled`);
        const startInput = document.getElementById(`day-${day}-start`);
        const endInput = document.getElementById(`day-${day}-end`);
        
        if (checkbox) checkbox.checked = true;
        if (startInput) startInput.value = slot.start_time.substring(0, 5);
        if (endInput) endInput.value = slot.end_time.substring(0, 5);
    });
}

function saveAvailability() {
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const availability = [];
    
    for (let day = 0; day <= 6; day++) {
        const checkbox = document.getElementById(`day-${day}-enabled`);
        const startInput = document.getElementById(`day-${day}-start`);
        const endInput = document.getElementById(`day-${day}-end`);
        
        if (checkbox && checkbox.checked) {
            availability.push({
                day_of_week: days[day],
                start_time: startInput.value + ':00',
                end_time: endInput.value + ':00',
                is_available: true
            });
        }
    }
    
    fetch('/api/availability/bulk-set', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ doctor_id: doctorId, availability: availability })
    })
    .then(res => res.json())
    .then(() => {
        showToast('success', 'Success', 'Availability saved successfully!');
        loadAvailability();
    })
    .catch(err => {
        console.error('Error:', err);
        showToast('error', 'Error', 'Failed to save availability');
    });
}

// Update setActiveTab to load availability when that tab is selected
const originalSetActiveTab = setActiveTab;
setActiveTab = function(tabName) {
    originalSetActiveTab(tabName);
    if (tabName === 'availability') {
        loadAvailability();
    }
};

function markComplete(appointmentId) {
    showConfirm('Complete Appointment', 'Mark this appointment as completed?', (confirmed) => {
        if (!confirmed) return;
        
        fetch(`/api/appointments/${appointmentId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: 'completed' })
        })
        .then(res => res.json())
        .then(() => {
            showToast('success', 'Success', 'Appointment marked as completed!');
            loadData();
        })
        .catch(err => {
            console.error('Error:', err);
            showToast('error', 'Error', 'Failed to update appointment');
        });
    });
}
</script>
@endsection
