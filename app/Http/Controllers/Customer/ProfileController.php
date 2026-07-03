<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show customer profile.
     */
    public function index()
    {
        return view('customer.profile');
    }

    /**
     * Update customer profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'         => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'phone'        => ['required', 'string', 'regex:/^07\d{8}$/'],
            'avatar'       => 'nullable|image|mimes:jpg,png|max:2048',
            'home_address' => 'required|string|max:500',
        ], [
            'name.regex'  => 'The name field can only contain letters.',
            'phone.regex' => 'Please enter a valid 10-digit Sri Lankan phone number starting with 07.',
        ]);

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->home_address = $request->home_address;

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('uploads/avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update customer password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }
}
