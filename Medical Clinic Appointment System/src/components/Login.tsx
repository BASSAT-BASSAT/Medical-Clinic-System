import { useState } from 'react';
import { User, UserRole } from '../App';
import { Stethoscope } from 'lucide-react';

interface LoginProps {
  onLogin: (user: User) => void;
}

// Mock users for demo
const mockUsers = [
  { id: '1', name: 'John Doe', email: 'patient@demo.com', role: 'patient' as UserRole, phone: '555-0101' },
  { id: '2', name: 'Dr. Sarah Smith', email: 'doctor@demo.com', role: 'doctor' as UserRole, phone: '555-0102' },
  { id: '3', name: 'Admin User', email: 'admin@demo.com', role: 'admin' as UserRole, phone: '555-0103' },
];

export function Login({ onLogin }: LoginProps) {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [isRegister, setIsRegister] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError('');

    const user = mockUsers.find(u => u.email === email);
    if (user) {
      onLogin(user);
    } else {
      setError('Invalid credentials. Try: patient@demo.com, doctor@demo.com, or admin@demo.com');
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 px-4">
      <div className="max-w-md w-full bg-white rounded-2xl shadow-xl p-8">
        <div className="flex flex-col items-center mb-8">
          <div className="bg-blue-600 p-3 rounded-full mb-4">
            <Stethoscope className="w-8 h-8 text-white" />
          </div>
          <h1 className="text-center text-gray-900 text-[20px] font-bold">MediCare Clinic</h1>
          <p className="text-center text-gray-600 mt-2 font-bold">
            {isRegister ? 'Create your account' : 'Sign in to your account'}
          </p>
        </div>

        <form onSubmit={handleSubmit} className="space-y-6">
          {isRegister && (
            <div>
              <label htmlFor="name" className="block text-gray-700 mb-2">
                Full Name
              </label>
              <input
                id="name"
                type="text"
                className="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                placeholder="Enter your full name"
              />
            </div>
          )}

          <div>
            <label htmlFor="email" className="block text-gray-700 mb-2 font-bold">
              Email Address
            </label>
            <input
              id="email"
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
              placeholder="Enter your email"
              required
            />
          </div>

          <div>
            <label htmlFor="password" className="block text-gray-700 mb-2 font-bold">
              Password
            </label>
            <input
              id="password"
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
              placeholder="Enter your password"
              required
            />
          </div>

          {isRegister && (
            <div>
              <label htmlFor="phone" className="block text-gray-700 mb-2">
                Phone Number
              </label>
              <input
                id="phone"
                type="tel"
                className="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                placeholder="Enter your phone number"
              />
            </div>
          )}

          {error && (
            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
              {error}
            </div>
          )}

          <button
            type="submit"
            className="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition"
          >
            {isRegister ? 'Create Account' : 'Sign In'}
          </button>
        </form>

        <div className="mt-6 text-center">
          <button
            onClick={() => setIsRegister(!isRegister)}
            className="text-blue-600 hover:text-blue-700"
          >
            {isRegister ? 'Already have an account? Sign In' : "Don't have an account? Register"}
          </button>
        </div>

       
      </div>
    </div>
  );
}
