import { useState } from 'react';
import { Appointment } from '../App';
import { ChevronLeft, ChevronRight } from 'lucide-react';

interface AppointmentCalendarProps {
  appointments: Appointment[];
}

export function AppointmentCalendar({ appointments }: AppointmentCalendarProps) {
  const [currentDate, setCurrentDate] = useState(new Date());

  const getDaysInMonth = (date: Date) => {
    return new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
  };

  const getFirstDayOfMonth = (date: Date) => {
    return new Date(date.getFullYear(), date.getMonth(), 1).getDay();
  };

  const previousMonth = () => {
    setCurrentDate(new Date(currentDate.getFullYear(), currentDate.getMonth() - 1));
  };

  const nextMonth = () => {
    setCurrentDate(new Date(currentDate.getFullYear(), currentDate.getMonth() + 1));
  };

  const daysInMonth = getDaysInMonth(currentDate);
  const firstDay = getFirstDayOfMonth(currentDate);
  const monthName = currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

  const getAppointmentsForDate = (day: number) => {
    const dateStr = new Date(currentDate.getFullYear(), currentDate.getMonth(), day)
      .toISOString()
      .split('T')[0];
    return appointments.filter(apt => apt.date === dateStr && apt.status !== 'cancelled');
  };

  const days = [];
  for (let i = 0; i < firstDay; i++) {
    days.push(<div key={`empty-${i}`} className="min-h-24 bg-gray-50" />);
  }

  for (let day = 1; day <= daysInMonth; day++) {
    const dayAppointments = getAppointmentsForDate(day);
    const isToday = 
      day === new Date().getDate() &&
      currentDate.getMonth() === new Date().getMonth() &&
      currentDate.getFullYear() === new Date().getFullYear();

    days.push(
      <div
        key={day}
        className={`min-h-24 border border-gray-200 p-2 ${
          isToday ? 'bg-blue-50 border-blue-300' : 'bg-white'
        }`}
      >
        <div className={`mb-1 ${isToday ? 'text-blue-600' : 'text-gray-900'}`}>
          {day}
        </div>
        <div className="space-y-1">
          {dayAppointments.slice(0, 2).map((apt) => (
            <div
              key={apt.id}
              className={`text-xs p-1 rounded ${
                apt.status === 'scheduled'
                  ? 'bg-green-100 text-green-800'
                  : 'bg-blue-100 text-blue-800'
              }`}
            >
              {apt.time} - {apt.patientName || apt.doctorName}
            </div>
          ))}
          {dayAppointments.length > 2 && (
            <div className="text-xs text-gray-600">
              +{dayAppointments.length - 2} more
            </div>
          )}
        </div>
      </div>
    );
  }

  return (
    <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <div className="flex items-center justify-between mb-6">
        <h3 className="text-gray-900">{monthName}</h3>
        <div className="flex gap-2">
          <button
            onClick={previousMonth}
            className="p-2 hover:bg-gray-100 rounded-lg transition"
          >
            <ChevronLeft className="w-5 h-5" />
          </button>
          <button
            onClick={nextMonth}
            className="p-2 hover:bg-gray-100 rounded-lg transition"
          >
            <ChevronRight className="w-5 h-5" />
          </button>
        </div>
      </div>

      <div className="grid grid-cols-7 gap-px bg-gray-200 border border-gray-200">
        {['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map((day) => (
          <div key={day} className="bg-gray-50 p-2 text-center text-gray-700">
            {day}
          </div>
        ))}
        {days}
      </div>

      <div className="mt-4 flex items-center gap-4">
        <div className="flex items-center gap-2">
          <div className="w-4 h-4 bg-green-100 border border-green-200 rounded" />
          <span className="text-gray-600">Scheduled</span>
        </div>
        <div className="flex items-center gap-2">
          <div className="w-4 h-4 bg-blue-100 border border-blue-200 rounded" />
          <span className="text-gray-600">Completed</span>
        </div>
      </div>
    </div>
  );
}
