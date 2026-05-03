<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'file' => 'required|file|max:10240', // 10MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('documents', 'public');

            $doc = Document::create([
                'project_id' => $request->project_id,
                'user_id' => Auth::id(),
                'name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);

            $project = \App\Models\Project::find($request->project_id);
            if ($project) {
                \App\Models\ClientActivity::create([
                    'client_id' => $project->client_id,
                    'provider_id' => $project->provider_id,
                    'project_id' => $project->id,
                    'activity_type' => 'document_uploaded',
                    'description' => 'A new document was uploaded: ' . $doc->name,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Document uploaded to vault.');
    }
}
