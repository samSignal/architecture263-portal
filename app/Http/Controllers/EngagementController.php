<?php

namespace App\Http\Controllers;

use App\Support\Architecture263Api;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EngagementController extends Controller
{
    public function index(Request $request, Architecture263Api $api)
    {
        $token = $request->cookie('portal_token');

        $response = $api->listEngagements($token);

        return view('engagements.index', [
            'engagements' => $response->successful() ? $response->json('data', []) : [],
            'me' => $this->currentUser($token, $api),
        ]);
    }

    public function store(Request $request, Architecture263Api $api): RedirectResponse
    {
        $token = $request->cookie('portal_token');

        $validated = $request->validate([
            'architect_id' => ['required', 'integer'],
        ]);

        $response = $api->createEngagement($token, $validated['architect_id']);

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Unable to select this architect.');
        }

        return redirect()->route('engagements.show', $response->json('id'))
            ->with('success', 'Request sent to the architect for approval.');
    }

    public function show(Request $request, Architecture263Api $api, int $id)
    {
        $token = $request->cookie('portal_token');

        $response = $api->getEngagement($token, $id);

        if ($response->failed()) {
            return redirect()->route('engagements.index')->with('error', 'Engagement not found.');
        }

        return view('engagements.show', [
            'engagement' => $response->json(),
            'me' => $this->currentUser($token, $api),
        ]);
    }

    public function approve(Request $request, Architecture263Api $api, int $id): RedirectResponse
    {
        return $this->act($request, $api, $id, fn (string $token) => $api->approveEngagement($token, $id));
    }

    public function requestBlueBook(Request $request, Architecture263Api $api): RedirectResponse
    {
        $token = $request->cookie('portal_token');

        $response = $api->requestBlueBook($token);

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Unable to record Blue Book purchase.');
        }

        return back()->with('success', $response->json('message') ?? 'Blue Book purchase recorded.');
    }

    public function signContract(Request $request, Architecture263Api $api, int $id): RedirectResponse
    {
        return $this->act($request, $api, $id, fn (string $token) => $api->signContract($token, $id));
    }

    private function act(Request $request, Architecture263Api $api, int $id, \Closure $call): RedirectResponse
    {
        $token = $request->cookie('portal_token');

        $response = $call($token);

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Action failed.');
        }

        return redirect()->route('engagements.show', $id)->with('success', 'Updated successfully.');
    }

    private function currentUser(string $token, Architecture263Api $api): ?array
    {
        $response = $api->getUser($token);

        return $response->successful() ? $response->json() : null;
    }
}
