<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Show business profile details form.
     */
    public function index()
    {
        $business = Auth::user()->business;
        return view('business.profile', compact('business'));
    }

    /**
     * Update business owner's business properties.
     */
    public function update(Request $request)
    {
        $business = Auth::user()->business;

        $request->validate([
            'business_name' => 'required|string|max:150',
            'business_type' => 'required|in:hotel,restaurant,bakery,cafe,supermarket',
            'phone'         => ['required', 'string', 'regex:/^07\d{8}$/'],
            'email'         => 'nullable|email|max:150',
            'address'       => 'required|string',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'description'   => 'nullable|string',
            'logo'          => 'nullable|image|mimes:jpg,png|max:2048',
        ], [
            'phone.regex'   => 'Please enter a valid 10-digit Sri Lankan phone number starting with 07.',
        ]);

        $business->business_name = $request->business_name;
        $business->business_type = $request->business_type;
        $business->phone = $request->phone;
        $business->email = $request->email;
        $business->address = $request->address;
        $business->latitude = $request->latitude ? (float) $request->latitude : null;
        $business->longitude = $request->longitude ? (float) $request->longitude : null;
        $business->description = $request->description;

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('uploads/businesses/logos', 'public');
            $business->logo = $path;
        }

        $business->save();

        return back()->with('success', 'Business profile updated successfully.');
    }
}
