<?php

namespace App\Http\Controllers;

use App\Support\Architecture263Api;
use Illuminate\Http\Request;

class PlanApplicationViewController extends Controller
{
    public function index(Request $request, Architecture263Api $api)
    {
        $token = $request->cookie('portal_token');

        $response = $api->listPlanApplications($token);

        return view('plan-applications.index', [
            'applications' => $response->successful() ? $response->json('data', []) : [],
        ]);
    }

    public function show(Request $request, Architecture263Api $api, int $id)
    {
        $token = $request->cookie('portal_token');

        $response = $api->getPlanApplication($token, $id);

        if ($response->failed()) {
            return redirect()->route('plan-applications.index')->with('error', 'Application not found.');
        }

        $meResponse = $api->getUser($token);

        return view('plan-applications.show', [
            'application' => $response->json(),
            'me' => $meResponse->successful() ? $meResponse->json() : null,
        ]);
    }

    public function resubmitForm(Request $request, Architecture263Api $api, int $id)
    {
        $token = $request->cookie('portal_token');

        $response = $api->getPlanApplication($token, $id);

        if ($response->failed()) {
            return redirect()->route('plan-applications.index')->with('error', 'Application not found.');
        }

        $application = $response->json();

        if ($application['status'] !== 'revision_requested') {
            return redirect()->route('plan-applications.show', $id)->with('error', 'This application is not awaiting revision.');
        }

        return view('plan-applications.resubmit', ['application' => $application]);
    }

    public function resubmit(Request $request, Architecture263Api $api, int $id)
    {
        $token = $request->cookie('portal_token');

        $validated = $request->validate([
            'plan_no' => ['required', 'string'],
            'stand_no' => ['required', 'string'],
            'postal_address' => ['required', 'string'],
            'estimated_cost' => ['required', 'numeric'],
            'purpose' => ['required', 'string'],
            'industry_type' => ['nullable', 'string'],
            'project_type' => ['required', 'string'],
            'owner_name' => ['required', 'string'],
            'owner_address' => ['required', 'string'],
            'owner_phone' => ['nullable', 'string'],
            'architect_name' => ['nullable', 'string'],
            'architect_address' => ['nullable', 'string'],
            'architect_phone' => ['nullable', 'string'],
            'contractor_name' => ['nullable', 'string'],
            'contractor_address' => ['nullable', 'string'],
            'contractor_phone' => ['nullable', 'string'],
            'supervision' => ['required', 'string'],
            'area_ground_floor' => ['required', 'numeric'],
            'area_first_floor' => ['nullable', 'numeric'],
            'area_total' => ['required', 'numeric'],
            'area_outbuildings' => ['nullable', 'numeric'],
            'fire_fighting_equipment' => ['nullable', 'string'],
            'drawings' => ['nullable', 'file', 'mimes:pdf,zip', 'max:20480'],
        ]);

        $drawings = $request->file('drawings');
        unset($validated['drawings']);

        $response = $api->resubmitPlanApplication($token, $id, $validated, $drawings);

        if ($response->failed()) {
            return back()->withInput()->with('error', $response->json('message') ?? 'Unable to resubmit this application.');
        }

        return redirect()->route('plan-applications.show', $id)->with('success', 'Corrected application resubmitted for review.');
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
}
