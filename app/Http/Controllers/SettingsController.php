<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Plan;
use App\Models\TeamMember;
use App\Models\User;

class SettingsController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $name = trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? ''));
        
        $user->update([
            'name' => $name,
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function updateCompany(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role === 'provider' && $user->providerProfile) {
            $validated = $request->validate([
                'about' => 'nullable|string',
            ]);
            $user->providerProfile->update(['bio' => $validated['about']]);
        }
        
        return redirect()->back()->with('success', 'Company details updated.');
    }

    public function updateSecurity(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'The provided password does not match our records.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    public function updatePlan(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);
        
        if ($plan->type !== Auth::user()->role) {
            return redirect()->back()->withErrors(['error' => 'Invalid plan type.']);
        }

        Auth::user()->update(['plan_id' => $plan->id]);

        return redirect()->back()->with('success', 'Subscription plan updated successfully.');
    }

    public function updateTeamMember(Request $request, $id)
    {
        $member = TeamMember::whereHas('team', function($q) {
            $q->where('owner_id', Auth::id());
        })->findOrFail($id);

        $validated = $request->validate([
            'role' => 'required|in:owner,manager,staff',
        ]);

        $member->update(['role' => $validated['role']]);

        return redirect()->back()->with('success', 'Team member role updated.');
    }

    public function updateStatus(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'provider' || !$user->providerProfile) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:active,inactive',
        ]);

        $user->providerProfile->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'status' => $validated['status'],
            'label' => $validated['status'] === 'active' ? __('common.active') : __('common.inactive')
        ]);
    }
}
