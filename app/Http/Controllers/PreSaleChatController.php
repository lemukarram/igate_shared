<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PreSaleMessage;
use App\Models\Service;
use App\Models\User;
use App\Models\ProviderService;
use Illuminate\Support\Facades\Auth;

class PreSaleChatController extends Controller
{
    public function show($serviceId, $providerId)
    {
        $service = Service::findOrFail($serviceId);
        $provider = User::with('providerProfile')->findOrFail($providerId);
        $ps = ProviderService::where('service_id', $serviceId)->where('provider_id', $providerId)->firstOrFail();

        $messages = PreSaleMessage::where('service_id', $serviceId)
            ->where(function($q) use ($providerId) {
                if (Auth::user()->role === 'client') {
                    $q->where('client_id', Auth::id())->where('provider_id', $providerId);
                } else {
                    $q->where('provider_id', Auth::id())->where('client_id', request('client_id'));
                }
            })
            ->oldest()
            ->get();

        return view('client.explore.pre_chat', compact('service', 'provider', 'ps', 'messages'));
    }

    public function sendMessage(Request $request, $serviceId, $providerId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $clientId = Auth::user()->role === 'client' ? Auth::id() : $request->client_id;

        PreSaleMessage::create([
            'client_id' => $clientId,
            'provider_id' => $providerId,
            'service_id' => $serviceId,
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return redirect()->back()->with('success', 'Message sent.');
    }
}
