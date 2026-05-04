<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InternalMessage;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;

class InternalMessageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Find the team
        $team = null;
        if ($user->ownedTeam) {
            $team = $user->ownedTeam;
        } else {
            $membership = $user->teamMemberships()->first();
            if ($membership) {
                $team = $membership->team;
            }
        }

        if (!$team) {
            // Auto-create a team for the owner if they don't have one
            $team = Team::create([
                'owner_id' => $user->id,
                'name' => $user->name . "'s Team",
            ]);
        }

        $messages = InternalMessage::where('team_id', $team->id)
            ->with('user')
            ->oldest()
            ->get();

        return view('internal_messages.index', compact('messages', 'team'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'team_id' => 'required|exists:teams,id',
        ]);

        InternalMessage::create([
            'team_id' => $request->team_id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return redirect()->back()->with('success', 'Message sent.');
    }
}
