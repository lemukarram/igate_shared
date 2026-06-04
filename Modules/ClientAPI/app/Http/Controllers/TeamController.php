<?php

namespace Modules\ClientAPI\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\Company;
use App\Traits\HandlesApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    use HandlesApiResponses;

    public function index()
    {
        try {
            $user = Auth::user();
            $team = $user->ownedTeams()->first();

            if (!$team) {
                return $this->successResponse([], 'No team found');
            }

            $members = $team->members()->with(['user', 'company'])->get();
            return $this->successResponse($members);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $owner = Auth::user();
            $plan = $owner->plan;

            // Check plan limits
            $team = $owner->ownedTeams()->firstOrCreate([
                'name' => $owner->name . "'s Team"
            ]);

            if ($plan && $team->members()->count() >= $plan->max_users) {
                return $this->errorResponse('Plan limit reached for team members', 403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'role' => 'required|in:manager,staff',
                'company_id' => 'required|exists:companies,id',
            ]);

            // Ensure company belongs to the owner
            $company = Company::where('id', $validated['company_id'])
                ->where('client_id', $owner->id)
                ->firstOrFail();

            // Find or create user
            $user = User::where('email', $validated['email'])->first();
            if (!$user) {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make(Str::random(16)),
                    'role' => 'client', // Team members are also clients in this context
                    'active_portal' => 'client',
                ]);
            }

            // Check if already in team
            $existingMember = TeamMember::where('team_id', $team->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingMember) {
                return $this->errorResponse('User is already a member of this team', 422);
            }

            $member = TeamMember::create([
                'team_id' => $team->id,
                'user_id' => $user->id,
                'company_id' => $company->id,
                'role' => $validated['role'],
                'is_active' => true,
            ]);

            return $this->successResponse($member->load(['user', 'company']), 'Team member added successfully', 201);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $owner = Auth::user();
            $team = $owner->ownedTeams()->firstOrFail();
            
            $member = TeamMember::where('team_id', $team->id)
                ->where('id', $id)
                ->firstOrFail();

            $member->delete();

            return $this->successResponse(null, 'Team member removed successfully');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
