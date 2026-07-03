<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Show registration type selection screen.
     */
    public function selectType()
    {
        return view('auth.register');
    }

    /**
     * Show customer registration form.
     */
    public function showCustomerForm()
    {
        return view('auth.customer-register');
    }

    /**
     * Handle customer registration.
     */
    public function registerCustomer(Request $request)
    {
        $request->merge([
            'email' => strtolower($request->email),
        ]);

        $request->validate([
            'name'         => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'email'        => 'required|string|email|max:150|unique:users',
            'phone'        => ['required', 'string', 'regex:/^07\d{8}$/'],
            'password'     => 'required|string|min:8|confirmed',
            'home_address' => 'required|string|max:500',
        ], [
            'name.regex'  => 'The name field can only contain letters.',
            'phone.regex' => 'Please enter a valid 10-digit Sri Lankan phone number starting with 07.',
        ]);

        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'home_address'      => $request->home_address,
            'password'          => Hash::make($request->password),
            'role'              => 'customer',
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        return redirect()->route('customer.dashboard')->with('success', 'Registration successful! Welcome to Food Rescue.');
    }

    /**
     * Show business owner registration form.
     */
    public function showBusinessForm()
    {
        return view('auth.business-register');
    }

    /**
     * Handle business owner and business registration.
     */
    public function registerBusiness(Request $request)
    {
        $request->validate([
            'owner_name'    => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'email'         => 'required|string|email|max:150|unique:users',
            'phone'         => ['required', 'string', 'regex:/^07\d{8}$/'],
            'password'      => 'required|string|min:8|confirmed',
            'business_name' => 'required|string|max:150',
            'business_type' => 'required|in:hotel,restaurant,bakery,cafe,supermarket',
            'address'       => 'required|string',
            'reg_number'    => 'required|string|unique:businesses',
            'reg_cert'      => 'required|file|mimes:pdf,jpg,png|max:5120',
            'safety_permit' => 'required|file|mimes:pdf,jpg,png|max:5120',
            'logo'          => 'nullable|image|mimes:jpg,png|max:2048',
        ], [
            'owner_name.regex' => 'The name field can only contain letters.',
            'phone.regex'      => 'Please enter a valid 10-digit Sri Lankan phone number starting with 07.',
        ]);

        // Create User (Business Owner)
        $user = User::create([
            'name'              => $request->owner_name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'password'          => Hash::make($request->password),
            'role'              => 'business_owner',
            'status'            => 'active', // Approved/active by default
            'email_verified_at' => now(),
        ]);

        // Upload documents
        $documents = [];
        if ($request->hasFile('reg_cert')) {
            $path = $request->file('reg_cert')->store('uploads/businesses/documents', 'public');
            $documents['reg_cert'] = $path;
        }
        if ($request->hasFile('safety_permit')) {
            $path = $request->file('safety_permit')->store('uploads/businesses/documents', 'public');
            $documents['safety_permit'] = $path;
        }

        // Logo
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('uploads/businesses/logos', 'public');
        }

        // Geocode the business address
        $latitude = (float) $request->input('latitude', 8.5755);
        $longitude = (float) $request->input('longitude', 81.2285);
        
        $isDefault = ($latitude === 8.5755 && $longitude === 81.2285) || empty($latitude) || empty($longitude);

        if ($isDefault) {
            try {
                $address = $request->address;
                $googleApiKey = env('GOOGLE_MAPS_API_KEY');
                
                $query = trim($address);
                if (!str_contains(strtolower($query), 'trincomalee')) {
                    $query .= ', Trincomalee';
                }
                if (!str_contains(strtolower($query), 'sri lanka')) {
                    $query .= ', Sri Lanka';
                }

                // 1. Try Google Maps Geocoding API
                $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($query) . '&key=' . $googleApiKey;
                $response = @file_get_contents($url);
                $googleSuccess = false;

                if ($response) {
                    $data = json_decode($response, true);
                    if ($data && $data['status'] === 'OK' && !empty($data['results'])) {
                        $latitude = (float) $data['results'][0]['geometry']['location']['lat'];
                        $longitude = (float) $data['results'][0]['geometry']['location']['lng'];
                        $googleSuccess = true;
                    }
                }

                // 2. Fallback to Nominatim if Google fails
                if (!$googleSuccess) {
                    $q = trim($address);
                    if (!str_contains(strtolower($q), 'trincomalee') && !str_contains(strtolower($q), 'sri lanka')) {
                        $q .= ', Trincomalee, Sri Lanka';
                    }

                    $opts = [
                        'http' => [
                            'method' => 'GET',
                            'header' => [
                                'User-Agent: FoodRescueMarketplaceApp/1.0 (info.foodrescue@gmail.com)'
                            ]
                        ]
                    ];
                    $context = stream_context_create($opts);

                    $osmUrl = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($q) . '&limit=1';
                    $osmRes = @file_get_contents($osmUrl, false, $context);
                    if ($osmRes) {
                        $osmData = json_decode($osmRes, true);
                        if (!empty($osmData) && isset($osmData[0]['lat']) && isset($osmData[0]['lon'])) {
                            $latitude = (float) $osmData[0]['lat'];
                            $longitude = (float) $osmData[0]['lon'];
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Geocoding business address failed: ' . $e->getMessage());
            }
        }

        // Create Business Record
        $business = Business::create([
            'user_id'         => $user->id,
            'business_name'   => $request->business_name,
            'business_type'   => $request->business_type,
            'address'         => $request->address,
            'latitude'        => $latitude,
            'longitude'       => $longitude,
            'reg_number'      => $request->reg_number,
            'phone'           => $request->phone,
            'email'           => $request->email,
            'logo'            => $logoPath,
            'documents'       => $documents,
            'status'          => 'approved', // Approved by default
            'reg_fee_paid'    => true, // Simulation of registration fee
            'reg_fee_paid_at' => now(),
        ]);

        // Create Default Active Subscription for the Business Owner
        \App\Models\Subscription::create([
            'business_id'  => $business->id,
            'plan_type'    => 'professional',
            'price'        => 5000.0,
            'upload_limit' => 100,
            'start_date'   => now(),
            'end_date'     => now()->addMonths(12),
            'status'       => 'active',
        ]);

        Auth::login($user);

        return redirect()->route('business.dashboard')->with('success', 'Registration successful! Welcome to your Merchant Panel.');
    }
}
