import { useState } from 'react';
import { User, Appointment } from '../App';
import { Users, Calendar, FileText, LogOut, TrendingUp, UserPlus, Bell } from 'lucide-react';
import { mockAppointments, mockDoctors, mockPatients } from '../data/mockData';
import { NotificationManager } from './NotificationManager';
import { AdminReports } from './AdminReports';

interface AdminDashboardProps {
  user: User;
  onLogout: () => void;
}

export function AdminDashboard({ user, onLogout }: AdminDashboardProps) {
  const [activeTab, setActiveTab] = useState<'overview' | 'doctors' | 'patients' | 'appointments' | 'notifications' | 'reports'>('overview');
  const [appointments] = useState<Appointment[]>(mockAppointments);

  const stats = {
    totalPatients: mockPatients.length,
    totalDoctors: mockDoctors.length,
    totalAppointments: appointments.length,
    scheduledAppointments: appointments.filter(apt => apt.status === 'scheduled').length,
    completedAppointments: appointments.filter(apt => apt.status === 'completed').length,
    cancelledAppointments: appointments.filter(apt => apt.status === 'cancelled').length,
  };

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <header className="bg-white shadow-sm border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="bg-blue-600 p-2 rounded-lg">
                <TrendingUp className="w-6 h-6 text-white" />
              </div>
              <div>
                <h1 className="text-gray-900">Admin Dashboard</h1>
                <p className="text-gray-600">Welcome, {user.name}</p>
              </div>
            </div>
            <button
              onClick={onLogout}
              className="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition"
            >
              <LogOut className="w-5 h-5" />
              Logout
            </button>
          </div>
        </div>
      </header>

      {/* Navigation Tabs */}
      <div className="bg-white border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <nav className="flex gap-8">
            <button
              onClick={() => setActiveTab('overview')}
              className={`py-4 border-b-2 transition ${
                activeTab === 'overview'
                  ? 'border-blue-600 text-blue-600'
                  : 'border-transparent text-gray-600 hover:text-gray-900'
              }`}
            >
              Overview
            </button>
            <button
              onClick={() => setActiveTab('doctors')}
              className={`py-4 border-b-2 transition ${
                activeTab === 'doctors'
                  ? 'border-blue-600 text-blue-600'
                  : 'border-transparent text-gray-600 hover:text-gray-900'
              }`}
            >
              Doctors
            </button>
            <button
              onClick={() => setActiveTab('patients')}
              className={`py-4 border-b-2 transition ${
                activeTab === 'patients'
                  ? 'border-blue-600 text-blue-600'
                  : 'border-transparent text-gray-600 hover:text-gray-900'
              }`}
            >
              Patients
            </button>
            <button
              onClick={() => setActiveTab('appointments')}
              className={`py-4 border-b-2 transition ${
                activeTab === 'appointments'
                  ? 'border-blue-600 text-blue-600'
                  : 'border-transparent text-gray-600 hover:text-gray-900'
              }`}
            >
              All Appointments
            </button>
            <button
              onClick={() => setActiveTab('notifications')}
              className={`py-4 border-b-2 transition ${
                activeTab === 'notifications'
                  ? 'border-blue-600 text-blue-600'
                  : 'border-transparent text-gray-600 hover:text-gray-900'
              }`}
            >
              Notifications
            </button>
            <button
              onClick={() => setActiveTab('reports')}
              className={`py-4 border-b-2 transition ${
                activeTab === 'reports'
                  ? 'border-blue-600 text-blue-600'
                  : 'border-transparent text-gray-600 hover:text-gray-900'
              }`}
            >
              Reports
            </button>
          </nav>
        </div>
      </div>

      {/* Main Content */}
      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {activeTab === 'overview' && (
          <div className="space-y-6">
            <h2 className="text-gray-900">System Overview</h2>

            {/* Stats Grid */}
            <div className="grid md:grid-cols-3 gap-6">
              <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div className="flex items-center gap-4">
                  <div className="bg-blue-100 p-3 rounded-lg">
                    <Users className="w-6 h-6 text-blue-600" />
                  </div>
                  <div>
                    <p className="text-gray-600">Total Patients</p>
                    <p className="text-gray-900">{stats.totalPatients}</p>
                  </div>
                </div>
              </div>

              <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div className="flex items-center gap-4">
                  <div className="bg-green-100 p-3 rounded-lg">
                    <FileText className="w-6 h-6 text-green-600" />
                  </div>
                  <div>
                    <p className="text-gray-600">Total Doctors</p>
                    <p className="text-gray-900">{stats.totalDoctors}</p>
                  </div>
                </div>
              </div>

              <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div className="flex items-center gap-4">
                  <div className="bg-purple-100 p-3 rounded-lg">
                    <Calendar className="w-6 h-6 text-purple-600" />
                  </div>
                  <div>
                    <p className="text-gray-600">Total Appointments</p>
                    <p className="text-gray-900">{stats.totalAppointments}</p>
                  </div>
                </div>
              </div>
            </div>

            {/* Appointment Status */}
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 className="text-gray-900 mb-4">Appointment Statistics</h3>
              <div className="grid md:grid-cols-3 gap-4">
                <div className="p-4 bg-green-50 rounded-lg">
                  <p className="text-green-600 mb-1">Scheduled</p>
                  <p className="text-green-900">{stats.scheduledAppointments}</p>
                </div>
                <div className="p-4 bg-blue-50 rounded-lg">
                  <p className="text-blue-600 mb-1">Completed</p>
                  <p className="text-blue-900">{stats.completedAppointments}</p>
                </div>
                <div className="p-4 bg-red-50 rounded-lg">
                  <p className="text-red-600 mb-1">Cancelled</p>
                  <p className="text-red-900">{stats.cancelledAppointments}</p>
                </div>
              </div>
            </div>

            {/* Recent Appointments */}
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 className="text-gray-900 mb-4">Recent Appointments</h3>
              <div className="space-y-3">
                {appointments.slice(0, 5).map((appointment) => (
                  <div
                    key={appointment.id}
                    className="flex items-center justify-between py-3 border-b border-gray-100 last:border-0"
                  >
                    <div>
                      <p className="text-gray-900">{appointment.patientName}</p>
                      <p className="text-gray-600">Dr. {appointment.doctorName}</p>
                    </div>
                    <div className="text-right">
                      <p className="text-gray-600">{new Date(appointment.date).toLocaleDateString()}</p>
                      <span
                        className={`inline-block px-2 py-1 rounded-full ${
                          appointment.status === 'scheduled'
                            ? 'bg-green-100 text-green-800'
                            : appointment.status === 'completed'
                            ? 'bg-blue-100 text-blue-800'
                            : 'bg-red-100 text-red-800'
                        }`}
                      >
                        {appointment.status}
                      </span>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        )}

        {activeTab === 'doctors' && (
          <div className="space-y-6">
            <div className="flex items-center justify-between">
              <h2 className="text-gray-900">Doctors Management</h2>
              <button className="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <UserPlus className="w-5 h-5" />
                Add New Doctor
              </button>
            </div>

            <div className="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-gray-50 border-b border-gray-200">
                    <tr>
                      <th className="px-6 py-3 text-left text-gray-700">Name</th>
                      <th className="px-6 py-3 text-left text-gray-700">Specialization</th>
                      <th className="px-6 py-3 text-left text-gray-700">Email</th>
                      <th className="px-6 py-3 text-left text-gray-700">Phone</th>
                      <th className="px-6 py-3 text-left text-gray-700">Status</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-200">
                    {mockDoctors.map((doctor) => (
                      <tr key={doctor.id} className="hover:bg-gray-50">
                        <td className="px-6 py-4 text-gray-900">{doctor.name}</td>
                        <td className="px-6 py-4 text-gray-600">{doctor.specialization}</td>
                        <td className="px-6 py-4 text-gray-600">{doctor.email}</td>
                        <td className="px-6 py-4 text-gray-600">{doctor.phone}</td>
                        <td className="px-6 py-4">
                          <span className="px-3 py-1 bg-green-100 text-green-800 rounded-full">
                            Active
                          </span>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}

        {activeTab === 'patients' && (
          <div className="space-y-6">
            <div className="flex items-center justify-between">
              <h2 className="text-gray-900">Patients Management</h2>
              <button className="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <UserPlus className="w-5 h-5" />
                Add New Patient
              </button>
            </div>

            <div className="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-gray-50 border-b border-gray-200">
                    <tr>
                      <th className="px-6 py-3 text-left text-gray-700">Name</th>
                      <th className="px-6 py-3 text-left text-gray-700">Email</th>
                      <th className="px-6 py-3 text-left text-gray-700">Phone</th>
                      <th className="px-6 py-3 text-left text-gray-700">Date of Birth</th>
                      <th className="px-6 py-3 text-left text-gray-700">Status</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-200">
                    {mockPatients.map((patient) => (
                      <tr key={patient.id} className="hover:bg-gray-50">
                        <td className="px-6 py-4 text-gray-900">{patient.name}</td>
                        <td className="px-6 py-4 text-gray-600">{patient.email}</td>
                        <td className="px-6 py-4 text-gray-600">{patient.phone}</td>
                        <td className="px-6 py-4 text-gray-600">
                          {new Date(patient.dateOfBirth).toLocaleDateString()}
                        </td>
                        <td className="px-6 py-4">
                          <span className="px-3 py-1 bg-green-100 text-green-800 rounded-full">
                            Active
                          </span>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}

        {activeTab === 'appointments' && (
          <div className="space-y-6">
            <h2 className="text-gray-900">All Appointments</h2>

            <div className="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-gray-50 border-b border-gray-200">
                    <tr>
                      <th className="px-6 py-3 text-left text-gray-700">Date</th>
                      <th className="px-6 py-3 text-left text-gray-700">Time</th>
                      <th className="px-6 py-3 text-left text-gray-700">Patient</th>
                      <th className="px-6 py-3 text-left text-gray-700">Doctor</th>
                      <th className="px-6 py-3 text-left text-gray-700">Reason</th>
                      <th className="px-6 py-3 text-left text-gray-700">Status</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-200">
                    {appointments.map((appointment) => (
                      <tr key={appointment.id} className="hover:bg-gray-50">
                        <td className="px-6 py-4 text-gray-900">
                          {new Date(appointment.date).toLocaleDateString()}
                        </td>
                        <td className="px-6 py-4 text-gray-600">{appointment.time}</td>
                        <td className="px-6 py-4 text-gray-900">{appointment.patientName}</td>
                        <td className="px-6 py-4 text-gray-600">{appointment.doctorName}</td>
                        <td className="px-6 py-4 text-gray-600">{appointment.reason}</td>
                        <td className="px-6 py-4">
                          <span
                            className={`px-3 py-1 rounded-full ${
                              appointment.status === 'scheduled'
                                ? 'bg-green-100 text-green-800'
                                : appointment.status === 'completed'
                                ? 'bg-blue-100 text-blue-800'
                                : 'bg-red-100 text-red-800'
                            }`}
                          >
                            {appointment.status}
                          </span>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}

        {activeTab === 'notifications' && (
          <div className="space-y-6">
            <h2 className="text-gray-900">Notification Management Center</h2>
            <NotificationManager />
          </div>
        )}

        {activeTab === 'reports' && (
          <div className="space-y-6">
            <h2 className="text-gray-900">Admin Reports</h2>
            <AdminReports />
          </div>
        )}
      </main>
    </div>
  );
}