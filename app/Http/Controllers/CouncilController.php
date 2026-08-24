<?php

namespace App\Http\Controllers;

use App\Support\Architecture263Api;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CouncilController extends Controller
{
    public function index(Request $request, Architecture263Api $api)
    {
        $token = $request->cookie('portal_token');

        $response = $api->listPlanApplications($token);

        return view('council.index', [
            'applications' => $response->successful() ? $response->json('data', []) : [],
        ]);
    }

    public function show(Request $request, Architecture263Api $api, int $id)
    {
        $token = $request->cookie('portal_token');

        $response = $api->getPlanApplication($token, $id);

        if ($response->failed()) {
            return redirect()->route('council.index')->with('error', 'Application not found.');
        }

        return view('council.show', ['application' => $response->json()]);
    }

    public function downloadDrawings(Request $request, Architecture263Api $api, int $id)
    {
        $token = $request->cookie('portal_token');

        $response = $api->downloadDrawings($token, $id);

        if ($response->failed()) {
            return back()->with('error', 'No drawings available for this application.');
        }

        $filename = $response->header('Content-Disposition');
        $filename = $filename && preg_match('/filename="?([^"]+)"?/', $filename, $m) ? $m[1] : "drawings-{$id}";

        return response($response->body())
            ->header('Content-Type', $response->header('Content-Type') ?: 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    public function previewDrawings(Request $request, Architecture263Api $api, int $id)
    {
        $token = $request->cookie('portal_token');

        $response = $api->previewDrawings($token, $id);

        if ($response->failed()) {
            abort(404, 'No drawings available for this application.');
        }

        $filename = $response->header('Content-Disposition');
        $filename = $filename && preg_match('/filename="?([^"]+)"?/', $filename, $m) ? $m[1] : "drawings-{$id}";

        return response($response->body())
            ->header('Content-Type', $response->header('Content-Type') ?: 'application/octet-stream')
            ->header('Content-Disposition', 'inline; filename="'.$filename.'"');
    }

    public function downloadDrawingVersion(Request $request, Architecture263Api $api, int $id, int $version)
    {
        $token = $request->cookie('portal_token');

        $response = $api->downloadDrawingVersion($token, $id, $version);

        if ($response->failed()) {
            return back()->with('error', 'That drawing version was not found.');
        }

        $filename = $response->header('Content-Disposition');
        $filename = $filename && preg_match('/filename="?([^"]+)"?/', $filename, $m) ? $m[1] : "drawings-{$id}-v{$version}";

        return response($response->body())
            ->header('Content-Type', $response->header('Content-Type') ?: 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    public function storeMarkup(Request $request, Architecture263Api $api, int $id)
    {
        $token = $request->cookie('portal_token');

        $validated = $request->validate([
            'type' => ['required', 'in:pin,line'],
            'page' => ['nullable', 'integer', 'min:1'],
            'x' => ['required', 'numeric', 'between:0,1'],
            'y' => ['required', 'numeric', 'between:0,1'],
            'x2' => ['required_if:type,line', 'nullable', 'numeric', 'between:0,1'],
            'y2' => ['required_if:type,line', 'nullable', 'numeric', 'between:0,1'],
            'comment' => ['nullable', 'string'],
        ]);

        $response = $api->storeMarkup($token, $id, $validated);

        // The plan viewer adds markups via fetch() so they appear instantly,
        // with no page reload — respond with JSON for that, and fall back
        // to the redirect flow only if JS/fetch isn't available.
        if ($request->expectsJson()) {
            return $response->successful()
                ? response()->json($response->json(), 201)
                : response()->json(['message' => $response->json('message') ?? 'Unable to add markup.'], $response->status());
        }

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Unable to add markup.');
        }

        return redirect()->route('council.show', $id)->with('success', 'Markup added.');
    }

    public function destroyMarkup(Request $request, Architecture263Api $api, int $id, int $markupId)
    {
        $token = $request->cookie('portal_token');

        $response = $api->deleteMarkup($token, $id, $markupId);

        if ($request->expectsJson()) {
            return $response->successful()
                ? response()->json(['message' => 'Markup removed.'])
                : response()->json(['message' => $response->json('message') ?? 'Unable to remove markup.'], $response->status());
        }

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Unable to remove markup.');
        }

        return redirect()->route('council.show', $id)->with('success', 'Markup removed.');
    }

    public function storeComment(Request $request, Architecture263Api $api, int $id): RedirectResponse
    {
        $token = $request->cookie('portal_token');

        $validated = $request->validate(['body' => ['required', 'string']]);

        $response = $api->addPlanApplicationComment($token, $id, $validated['body']);

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Unable to add comment.');
        }

        return redirect()->route('council.show', $id)->with('success', 'Comment added.');
    }

    public function decide(Request $request, Architecture263Api $api, int $id): RedirectResponse
    {
        $token = $request->cookie('portal_token');

        $validated = $request->validate([
            'decision' => ['required', 'in:approved,rejected,revision_requested'],
            'comment' => ['nullable', 'string'],
        ]);

        $response = $api->decidePlanApplication($token, $id, $validated['decision'], $validated['comment'] ?? null);

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Unable to record decision.');
        }

        return redirect()->route('council.show', $id)->with('success', 'Decision recorded.');
    }
}
