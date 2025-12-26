<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Cafeteria;
use App\Models\LoginAttempt;

class StudentAuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register_student');
    }

    public function register(Request $request)
    {
        $request->validate([
            'matric_no' => 'required|integer|unique:students,matric_no',
            'name' => 'required|string',
            'email' => 'required|email|unique:students,email',
            'password' => 'required|string|confirmed',
        ]);

        $student = Student::create([
            'matric_no' => $request->matric_no,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('student')->login($student);

        return redirect()->route('student.home')->with('success', 'Welcome to UniMeal!');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = $request->email;

        // Try to find user in both student and cafeteria tables
        $student = Student::where('email', $email)->first();
        $cafeteria = Cafeteria::where('email', $email)->first();

        // Determine which guard to use
        if ($student) {
            $guard = 'student';
            $user = $student;
            $redirectRoute = 'student.home';
        } elseif ($cafeteria) {
            $guard = 'cafeteria';
            $user = $cafeteria;
            $redirectRoute = 'vendor.home';
        } else {
            // No user found with this email
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput($request->only('email'));
        }

        // Check for account lockout
        $failedAttempts = LoginAttempt::getRecentFailedAttempts($email, $guard, 15);

        if ($failedAttempts >= 5) {
            LoginAttempt::logAttempt($email, $guard, false);
            return back()->withErrors([
                'email' => 'Too many failed login attempts. Your account is temporarily locked. Please try again in 15 minutes.'
            ])->withInput($request->only('email'));
        }

        // Verify password
        if (Hash::check($request->password, $user->password)) {
            // Successful login
            LoginAttempt::logAttempt($email, $guard, true);
            LoginAttempt::clearFailedAttempts($email, $guard);

            Auth::guard($guard)->login($user);

            // Regenerate session to prevent session fixation
            $request->session()->regenerate();

            return redirect()->route($redirectRoute);
        }

        // Failed login
        LoginAttempt::logAttempt($email, $guard, false);

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput($request->only('email'));
    }
}
