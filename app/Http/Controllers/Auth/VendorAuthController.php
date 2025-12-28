<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cafeteria;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class VendorAuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register_vendor');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:cafeterias',
            'password' => [
                'required',
                'string',
                'min:10',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/'
            ],
        ]);

        $vendor = Cafeteria::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('cafeteria')->login($vendor);

        return redirect()->route('vendor.home');
    }
}
