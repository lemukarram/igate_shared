<?php

namespace Modules\ClientAPI\GraphQL\Mutations;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class CompanyMutation
{
    /**
     * Create a new company for the authenticated client.
     */
    public function create($_, array $args)
    {
        $user = Auth::user();
        $input = $args['input'];

        return Company::create(array_merge($input, [
            'client_id' => $user->id,
            'is_active' => true,
        ]));
    }
}
