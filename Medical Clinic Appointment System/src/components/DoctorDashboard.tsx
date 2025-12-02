import { useState } from 'react';
import { User, Appointment } from '../App';
import { Calendar, Clock, FileText, LogOut, Filter, Download } from 'lucide-react';
import { AppointmentCalendar } from './AppointmentCalendar';
import { Notifications, Notification } from './Notifications';
import { PatientRecordsManager } from './PatientRecordsManager';
import { mockAppointments } from '../data/mockData';

interface DoctorDashboardProps {
  user: User;
  onLogout: () => void;
}

export function DoctorDashboard({ user, onLogout }: DoctorDashboardProps) {
  const [activeTab, setActiveTab] = useState<'today' | 'calendar' | 'reports' | 'records'>('today');
  const [appointments, setAppointments] = useState<Appointment[]>(
    mockAppointments.filter(apt => apt.doctorId === user.id)
  );
  const [filterStatus, setFilterStatus] = useState<'all' | 'scheduled' | 'completed' | 'cancelled'>('all');
  const [dateFilter, setDateFilter] = useState<'today' | 'week' | 'month'>('today');
  const [notifications, setNotifications] = useState<Notification[]>(() => {
    const initialNotifications: Notification[] = [];

    // Add login notification
    initialNotifications.push({
      id: `login-${Date.now()}`,
      type: 'login',
      title: 'Welcome back, Doctor!',
      message: `You logged in at ${new Date().toLocaleTimeString()}`,
      timestamp: new Date(),
      read: false,
    });

    // Check for upcoming appointments today
    const today = new Date().toISOString().split('T')[0];
    const todayAppointments = mockAppointments.filter(
      apt => apt.doctorId === user.id && apt.date === today && apt.status === 'scheduled'
    );

    if (todayAppointments.length > 0) {
      initialNotifications.push({
        id: `today-schedule-${Date.now()}`,
        type: 'upcoming',
        title: `${todayAppointments.length} Appointment${todayAppointments.length > 1 ? 's' : ''} Today`,
        message: `You have ${todayAppointments.length} scheduled appointment${todayAppointments.length > 1 ? 's' : ''} for today`,
        timestamp: new Date(Date.now() - 3600000),
        read: false,
      });
    }

    // Check for cancelled appointments
    const cancelledAppointments = mockAppointments.filter(
      apt => apt.doctorId === user.id && apt.status === 'cancelled'
    );

    cancelledAppointments.forEach((apt) => {
      initialNotifications.push({
        id: `cancelled-${apt.id}`,
        type: 'cancelled',
        title: 'Appointment Cancelled',
        message: `${apt.patientName} cancelled their appointment on ${new Date(apt.date).toLocaleDateString()} at ${apt.time}`,
        timestamp: new Date(Date.now() - 1800000),
        read: false,
      });
    });

    return initialNotifications;
  });

  const getWeekStart = () => {
    const now = new Date();
    const day = now.getDay();
    const diff = now.getDate() - day;
    return new Date(now.setDate(diff)).toISOString().split('T')[0];
  };

  const getMonthStart = () => {
    const now = new Date();
    return new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
  };

  const today = new Date().toISOString().split('T')[0];
  const todayAppointments = appointments.filter(apt => apt.date === today);

  const filteredAppointments = appointments.filter(apt => {
    const statusMatch = filterStatus === 'all' || apt.status === filterStatus;
    
    if (dateFilter === 'today') {
      return statusMatch && apt.date === today;
    } else if (dateFilter === 'week') {
      return statusMatch && apt.date >= getWeekStart();
    } else if (dateFilter === 'month') {
      return statusMatch && apt.date >= getMonthStart();
    }
    
    return statusMatch;
  });

  const handleCompleteAppointment = (appointmentId: string) => {
    setAppointments(prev =>
      prev.map(apt =>
        apt.id === appointmentId ? { ...apt, status: 'completed' as const } : apt
      )
    );
  };

  const handleMarkAsRead = (id: string) => {
    setNotifications(prev =>
      prev.map(n => n.id === id ? { ...n, read: true } : n)
    );
  };

  const handleMarkAllAsRead = () => {
    setNotifications(prev => prev.map(n => ({ ...n, read: true })));
  };

  const handleClearNotification = (id: string) => {
    setNotifications(prev => prev.filter(n => n.id !== id));
  };

  const generateReport = () => {
    const reportData = filteredAppointments.map(apt => ({
      Date: apt.date,
      Time: apt.time,
      Patient: apt.patientName,
      Reason: apt.reason,
      Status: apt.status
    }));
    
    console.log('Report generated:', reportData);
    alert('Report generated! Check console for details.');
  };

  const stats = {
    total: appointments.length,
    scheduled: appointments.filter(apt => apt.status === 'scheduled').length,
    completed: appointments.filter(apt => apt.status === 'completed').length,
    cancelled: appointments.filter(apt => apt.status === 'cancelled').length,
  };

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <header className="bg-white shadow-sm border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="bg-blue-600 p-2 rounded-lg">
                <FileText className="w-6 h-6 text-white" />
              </div>
              <div>
                <h1 className="text-gray-900">Doctor Dashboard</h1>
                <p className="text-gray-600">Welcome, {user.name}</p>
              </div>
            </div>
            <div className="flex items-center gap-2">
              <Notifications
                notifications={notifications}
                onMarkAsRead={handleMarkAsRead}
                onMarkAllAsRead={handleMarkAllAsRead}
                onClearNotification={handleClearNotification}
              />
              <button
                onClick={onLogout}
                className="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition"
              >
                <LogOut className="w-5 h-5" />
                Logout
              </button>
            </div>
          </div>
        </div>
      </header>

      {/* Stats */}
      <div className="bg-white border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div className="bg-blue-50 p-4 rounded-lg">
              <p className="text-blue-600 mb-1">Total Appointments</p>
              <p className="text-blue-900">{stats.total}</p>
            </div>
            <div className="bg-green-50 p-4 rounded-lg">
              <p className="text-green-600 mb-1">Scheduled</p>
              <p className="text-green-900">{stats.scheduled}</p>
            </div>
            <div className="bg-purple-50 p-4 rounded-lg">
              <p className="text-purple-600 mb-1">Completed</p>
              <p className="text-purple-900">{stats.completed}</p>
            </div>
            <div className="bg-red-50 p-4 rounded-lg">
              <p className="text-red-600 mb-1">Cancelled</p>
              <p className="text-red-900">{stats.cancelled}</p>
            </div>
          </div>
        </div>
      </div>

      {/* Navigation Tabs */}
      <div className="bg-white border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <nav className="flex gap-8">
            <button
              onClick={() => setActiveTab('today')}
              className={`py-4 border-b-2 transition ${
                activeTab === 'today'
                  ? 'border-blue-600 text-blue-600'
                  : 'border-transparent text-gray-600 hover:text-gray-900'
              }`}
            >
              Today's Schedule
            </button>
            <button
              onClick={() => setActiveTab('calendar')}
              className={`py-4 border-b-2 transition ${
                activeTab === 'calendar'
                  ? 'border-blue-600 text-blue-600'
                  : 'border-transparent text-gray-600 hover:text-gray-900'
              }`}
            >
              Calendar View
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
            <button
              onClick={() => setActiveTab('records')}
              className={`py-4 border-b-2 transition ${
                activeTab === 'records'
                  ? 'border-blue-600 text-blue-600'
                  : 'border-transparent text-gray-600 hover:text-gray-900'
              }`}
            >
              Patient Records
            </button>
          </nav>
        </div>
      </div>

      {/* Main Content */}
      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {activeTab === 'today' && (
          <div className="space-y-6">
            <div className="flex items-center justify-between">
              <h2 className="text-gray-900">Today's Appointments</h2>
              <p className="text-gray-600">{new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
            </div>

            <div className="grid gap-4">
              {todayAppointments.length > 0 ? (
                todayAppointments.map((appointment) => (
                  <div
                    key={appointment.id}
                    className="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition"
                  >
                    <div className="flex items-start justify-between">
                      <div className="flex-1">
                        <div className="flex items-center gap-3 mb-2">
                          <h3 className="text-gray-900">{appointment.patientName}</h3>
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
                        </div>
                        <div className="space-y-2 text-gray-600">
                          <div className="flex items-center gap-2">
                            <Clock className="w-4 h-4" />
                            {appointment.time}
                          </div>
                          <p className="text-gray-900">Reason: {appointment.reason}</p>
                          {appointment.notes && (
                            <p className="text-gray-600">Notes: {appointment.notes}</p>
                          )}
                        </div>
                      </div>
                      {appointment.status === 'scheduled' && (
                        <button
                          onClick={() => handleCompleteAppointment(appointment.id)}
                          className="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                        >
                          Mark Complete
                        </button>
                      )}
                    </div>
                  </div>
                ))
              ) : (
                <div className="text-center py-12 bg-white rounded-lg border border-gray-200">
                  <Calendar className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                  <p className="text-gray-600">No appointments scheduled for today</p>
                </div>
              )}
            </div>
          </div>
        )}

        {activeTab === 'calendar' && (
          <div className="space-y-6">
            <h2 className="text-gray-900">Calendar View</h2>
            <AppointmentCalendar appointments={appointments} />
          </div>
        )}

        {activeTab === 'reports' && (
          <div className="space-y-6">
            <div className="flex items-center justify-between">
              <h2 className="text-gray-900">Appointment Reports</h2>
              <button
                onClick={generateReport}
                className="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
              >
                <Download className="w-5 h-5" />
                Download Report
              </button>
            </div>

            {/* Filters */}
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div className="flex items-center gap-4 mb-4">
                <Filter className="w-5 h-5 text-gray-600" />
                <h3 className="text-gray-900">Filters</h3>
              </div>
              <div className="grid md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-gray-700 mb-2">Status</label>
                  <select
                    value={filterStatus}
                    onChange={(e) => setFilterStatus(e.target.value as any)}
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                  >
                    <option value="all">All</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                  </select>
                </div>
                <div>
                  <label className="block text-gray-700 mb-2">Date Range</label>
                  <select
                    value={dateFilter}
                    onChange={(e) => setDateFilter(e.target.value as any)}
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                  >
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                  </select>
                </div>
              </div>
            </div>

            {/* Report Table */}
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-gray-50 border-b border-gray-200">
                    <tr>
                      <th className="px-6 py-3 text-left text-gray-700">Date</th>
                      <th className="px-6 py-3 text-left text-gray-700">Time</th>
                      <th className="px-6 py-3 text-left text-gray-700">Patient</th>
                      <th className="px-6 py-3 text-left text-gray-700">Reason</th>
                      <th className="px-6 py-3 text-left text-gray-700">Status</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-200">
                    {filteredAppointments.map((appointment) => (
                      <tr key={appointment.id} className="hover:bg-gray-50">
                        <td className="px-6 py-4 text-gray-900">
                          {new Date(appointment.date).toLocaleDateString()}
                        </td>
                        <td className="px-6 py-4 text-gray-600">{appointment.time}</td>
                        <td className="px-6 py-4 text-gray-900">{appointment.patientName}</td>
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
              {filteredAppointments.length === 0 && (
                <div className="text-center py-12">
                  <p className="text-gray-600">No appointments found for selected filters</p>
                </div>
              )}
            </div>
          </div>
        )}

        {activeTab === 'records' && (
          <div className="space-y-6">
            <h2 className="text-gray-900">Patient Records Management</h2>
            <PatientRecordsManager doctorId={user.id} doctorName={user.name} />
          </div>
        )}
      </main>
    </div>
  );
}