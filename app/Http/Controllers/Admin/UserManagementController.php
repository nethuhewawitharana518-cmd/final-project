<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    /**
     * Display all users list filtered by role.
     */
    public function index(Request $request)
    {
        $role = $request->get('role', 'all');
        $query = User::query();

        if ($role !== 'all') {
            $query->where('role', $role);
        }

        $users = $query->latest()
            ->paginate(15);

        return view('admin.users', compact('users', 'role'));
    }

    /**
     * Suspend user login access.
     */
    public function suspend($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'suspended']);

        if ($user->isBusinessOwner() && $user->business) {
            $user->business->update(['status' => 'suspended']);
        }

        return back()->with('success', 'User account suspended successfully.');
    }

    /**
     * Re-activate suspended user login access.
     */
    public function activate($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);

        if ($user->isBusinessOwner() && $user->business) {
            $user->business->update(['status' => 'approved']);
        }

        return back()->with('success', 'User account activated successfully.');
    }

    /**
     * Hard delete user account.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'User account deleted permanently.');
    }
}
