<?php

namespace Modules\ClientAPI\GraphQL\Mutations;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentMutation
{
    /**
     * Handle file upload from mobile app via GraphQL.
     */
    public function upload($_, array $args)
    {
        $user = Auth::user();
        $input = $args['input'];
        $file = $input['file']; // This is an instance of \Illuminate\Http\UploadedFile

        $path = $file->store('documents', 'public');

        return Document::create([
            'user_id' => $user->id,
            'project_id' => $input['project_id'] ?? null,
            'name' => $input['name'],
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
        ]);
    }
}
