<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Show system administration settings console.
     */
    public function index()
    {
        return view('admin.settings');
    }

    /**
     * Update global config criteria (Mocked).
     */
    public function update(Request $request)
    {
        $request->validate([
            'commission_rate'  => 'required|numeric|min:0|max:100',
            'registration_fee' => 'required|numeric|min:0',
        ]);

        return back()->with('success', 'System parameters updated successfully.');
    }
}
