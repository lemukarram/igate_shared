<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Plan;
use App\Models\TeamMember;
use App\Models\Team;
use App\Models\Company;
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
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $name = trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? ''));
        
        $data = [
            'name' => $name,
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ];

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profiles', 'public');
            $data['profile_picture'] = $path;
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function updateCompany(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'about' => 'nullable|string',
            'name' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        if ($user->role === 'provider' && $user->providerProfile) {
            $data = ['bio' => $validated['about']];
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('logos', 'public');
                $data['logo'] = $path;
            }
            $user->providerProfile->update($data);
        } elseif ($user->role === 'client') {
            $company = $user->companies()->first();
            if ($company) {
                $data = [
                    'about' => $validated['about'],
                    'name' => $validated['name'] ?? $company->name,
                    'industry' => $validated['industry'] ?? $company->industry,
                ];
                if ($request->hasFile('logo')) {
                    $path = $request->file('logo')->store('logos', 'public');
                    $data['logo'] = $path;
                }
                $company->update($data);
            }
        }

        // Handle documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('company_documents', 'public');
                \App\Models\Document::create([
                    'user_id' => $user->id,
                    'file_path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }
        
        return redirect()->back()->with('success', 'Company details updated.');
    }

    public function updateGeneral(Request $request)
    {
        // For now, this might just be placeholders or session-based settings
        // as language is handled via AlpineJS and localStorage in this template.
        return redirect()->back()->with('success', 'General settings updated.');
    }

    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        $user->update([
            'notification_settings' => $request->notifications ?? []
        ]);

        return redirect()->back()->with('success', 'Notification preferences updated.');
    }

    public function addTeamMember(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:manager,staff',
        ]);

        // In a real app, you'd send an invite. For this prototype, we'll create a user.
        $new_user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make('password'), // default password
            'role' => $user->role, // same role as owner (provider or client)
        ]);

        $team = $user->ownedTeam; // Assuming a relationship exists or we find it
        if (!$team) {
            $team = \App\Models\Team::create([
                'owner_id' => $user->id,
                'name' => $user->name . "'s Team",
            ]);
        }

        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $new_user->id,
            'role' => $validated['role'],
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->back()->with('success', 'Team member added successfully.');
    }

    public function updateTeamMember(Request $request, $id)
    {
        if ($id === 'multiple') {
            foreach ($request->members as $memberId => $data) {
                $member = TeamMember::whereHas('team', function($q) {
                    $q->where('owner_id', Auth::id());
                })->find($memberId);
                
                if ($member) {
                    $member->update([
                        'role' => $data['role'],
                        'permissions' => $data['permissions'] ?? [],
                    ]);
                }
            }
            return redirect()->back()->with('success', 'Team permissions updated.');
        }

        $member = TeamMember::whereHas('team', function($q) {
            $q->where('owner_id', Auth::id());
        })->findOrFail($id);

        $validated = $request->validate([
            'role' => 'required|in:manager,staff',
        ]);

        $member->update([
            'role' => $validated['role'],
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->back()->with('success', 'Team member updated.');
    }

    public function removeTeamMember($id)
    {
        $member = TeamMember::whereHas('team', function($q) {
            $q->where('owner_id', Auth::id());
        })->findOrFail($id);

        $member->delete();

        return redirect()->back()->with('success', 'Team member removed.');
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
