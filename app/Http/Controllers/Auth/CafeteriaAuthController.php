<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Cafeteria;

class CafeteriaAuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register_vendor');
    }

    public function home()
    {
        $cafeteria = Auth::guard('cafeteria')->user();
        $mahallah = $cafeteria->mahallah ?? 'Unknown Mahallah';

        return view('vendor.home', compact('mahallah', 'cafeteria'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'cafeteria_id' => 'required|integer|unique:cafeterias,cafeteria_id',
            'name' => 'required|string',
            'email' => 'required|email|unique:cafeterias,email',
            'password' => [
                'required',
                'string',
                'min:10',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/'
            ],
        ]);

        $cafeteria = Cafeteria::create([
            'cafeteria_id' => $request->cafeteria_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('cafeteria')->login($cafeteria);

        return redirect()->route('vendor.home')->with('success', 'Vendor account created!');
    }
}
