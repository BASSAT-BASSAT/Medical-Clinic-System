import { useState } from "react";
import { Login } from "./components/Login";
import { PatientDashboard } from "./components/PatientDashboard";
import { DoctorDashboard } from "./components/DoctorDashboard";
import { AdminDashboard } from "./components/AdminDashboard";

export type UserRole = "patient" | "doctor" | "admin" | null;

export interface User {
  id: string;
  name: string;
  email: string;
  role: UserRole;
  phone?: string;
}

export interface Appointment {
  id: string;
  patientId: string;
  patientName: string;
  doctorId: string;
  doctorName: string;
  date: string;
  time: string;
  reason: string;
  status: "scheduled" | "completed" | "cancelled";
  notes?: string;
}

export interface MedicalRecord {
  id: string;
  patientId: string;
  date: string;
  diagnosis: string;
  prescription: string;
  doctorName: string;
  notes: string;
}

function App() {
  const [currentUser, setCurrentUser] = useState<User | null>(
    null,
  );

  const handleLogin = (user: User) => {
    setCurrentUser(user);
  };

  const handleLogout = () => {
    setCurrentUser(null);
  };

  if (!currentUser) {
    return <Login onLogin={handleLogin} />;
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {currentUser.role === "patient" && (
        <PatientDashboard
          user={currentUser}
          onLogout={handleLogout}
        />
      )}
      {currentUser.role === "doctor" && (
        <DoctorDashboard
          user={currentUser}
          onLogout={handleLogout}
        />
      )}
      {currentUser.role === "admin" && (
        <AdminDashboard
          user={currentUser}
          onLogout={handleLogout}
        />
      )}
    </div>
  );
}

export default App;