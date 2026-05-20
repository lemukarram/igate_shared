<?php

namespace Modules\ClientAPI\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Settings\GeneralSettings;
use App\Settings\InvoiceSettings;
use App\Settings\LandingPageSettings;
use App\Settings\PaymentSettings;
use App\Traits\HandlesApiResponses;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    use HandlesApiResponses;

    public function general(GeneralSettings $settings)
    {
        try {
            return $this->successResponse($settings->toArray());
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function invoice(InvoiceSettings $settings)
    {
        try {
            return $this->successResponse($settings->toArray());
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function payment(PaymentSettings $settings)
    {
        try {
            return $this->successResponse($settings->toArray());
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function landing(LandingPageSettings $settings)
    {
        try {
            return $this->successResponse($settings->toArray());
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
