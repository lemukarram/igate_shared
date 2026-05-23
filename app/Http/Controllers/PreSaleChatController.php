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
    public function index()
    {
        $providerId = Auth::id();
        $chats = PreSaleMessage::where('provider_id', $providerId)
            ->with(['service', 'client'])
            ->latest()
            ->get()
            ->unique(function ($item) {
                return $item->client_id . '-' . $item->service_id;
            });

        return view('provider.pre_sale_chats.index', compact('chats'));
    }

    public function show($serviceId, $providerId)
    {
        $service = Service::findOrFail($serviceId);
        $provider = User::with('providerProfile')->findOrFail($providerId);
        $ps = ProviderService::where('service_id', $serviceId)->where('provider_id', $providerId)->firstOrFail();

        $messages = PreSaleMessage::where('service_id', $serviceId)
            ->where(function($q) use ($providerId) {
                if (Auth::user()->isClientMode()) {
                    $q->where('client_id', Auth::id())->where('provider_id', $providerId);
                } else {
                    $q->where('provider_id', Auth::id())->where('client_id', request('client_id'));
                }
            })
            ->oldest()
            ->get();

        $companies = Auth::user()->isClientMode() ? Auth::user()->companies : collect();

        return view('client.explore.pre_chat', compact('service', 'provider', 'ps', 'messages', 'companies'));
    }

    public function sendMessage(Request $request, $serviceId, $providerId)
    {
        $request->validate([
            'message' => 'required|string',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $clientId = Auth::user()->isClientMode() ? Auth::id() : $request->client_id;

        PreSaleMessage::create([
            'client_id' => $clientId,
            'provider_id' => $providerId,
            'service_id' => $serviceId,
            'company_id' => $request->company_id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return redirect()->back()->with('success', 'Message sent.');
    }
}
