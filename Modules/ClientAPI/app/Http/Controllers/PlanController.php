<?php

namespace Modules\ClientAPI\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Traits\HandlesApiResponses;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    use HandlesApiResponses;

    public function index()
    {
        try {
            $plans = Plan::where('type', 'client')->get();
            return $this->successResponse($plans);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
