<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Milestone;
use Illuminate\Support\Facades\Auth;

class MilestoneController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        Milestone::create(array_merge($validated, ['status' => 'pending']));

        return redirect()->back()->with('success', 'Milestone added.');
    }

    public function updateStatus(Request $request, $id)
    {
        $milestone = Milestone::findOrFail($id);
        
        // Logic: Provider can set to 'completed', Client can set to 'released'
        $milestone->update(['status' => $request->status]);
        
        if ($request->status === 'completed') {
            $milestone->update(['completed_at' => now()]);
        }

        return redirect()->back()->with('success', 'Milestone status updated.');
    }
}
