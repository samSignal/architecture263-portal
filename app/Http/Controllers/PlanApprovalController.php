<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\Architecture263Api;
use Illuminate\Support\Facades\Log;

class PlanApprovalController extends Controller
{
    /**
     * Which flat draft fields belong to which wizard step — used to
     * rehydrate the session (for prefilling) from the backend draft.
     */
    private const STEP_FIELDS = [
        'step1' => ['plan_no', 'stand_no', 'postal_address', 'estimated_cost', 'purpose', 'industry_type', 'project_type'],
        'step2' => ['owner_name', 'owner_address', 'owner_phone', 'architect_name', 'architect_address', 'architect_phone', 'contractor_name', 'contractor_address', 'contractor_phone', 'supervision'],
        'step3' => ['area_ground_floor', 'area_total', 'area_outbuildings', 'fire_fighting_equipment'],
    ];

    public function index(Request $request, Architecture263Api $api)
    {
        if ($request->filled('engagement_id')) {
            $request->session()->put('plan_approval.engagement_id', (int) $request->query('engagement_id'));
        }

        $engagementId = $request->session()->get('plan_approval.engagement_id');

        if (! $engagementId) {
            return redirect()->route('engagements.index')
                ->with('error', 'Select a contract-signed engagement before submitting plans.');
        }

        $this->hydrateSessionFromDraft($request, $api, $engagementId);

        return view('plan-approval.index');
    }

    public function step1(Request $request)
    {
        return view('plan-approval.step1');
    }

    public function postStep1(Request $request, Architecture263Api $api)
    {
        $validated = $request->validate([
            'plan_no' => 'required|string',
            'stand_no' => 'required|string',
            'postal_address' => 'required|string',
            'estimated_cost' => 'required|numeric',
            'purpose' => 'required|string',
            'industry_type' => 'nullable|string',
            'project_type' => 'required|string', // New, Alteration, Addition
        ]);

        if (! $this->saveDraftStep($request, $api, $validated)) {
            return back()->withInput()->with('error', 'Could not save your progress. Please try again.');
        }

        $request->session()->put('plan_approval.step1', $validated);

        return redirect()->route('plan-approval.step2');
    }

    public function step2(Request $request)
    {
        return view('plan-approval.step2');
    }

    public function postStep2(Request $request, Architecture263Api $api)
    {
        $validated = $request->validate([
            'owner_name' => 'required|string',
            'owner_address' => 'required|string',
            'owner_phone' => 'nullable|string',
            'architect_name' => 'nullable|string',
            'architect_address' => 'nullable|string',
            'architect_phone' => 'nullable|string',
            'contractor_name' => 'nullable|string',
            'contractor_address' => 'nullable|string',
            'contractor_phone' => 'nullable|string',
            'supervision' => 'required|string', // Architect or Engineer
        ]);

        if (! $this->saveDraftStep($request, $api, $validated)) {
            return back()->withInput()->with('error', 'Could not save your progress. Please try again.');
        }

        $request->session()->put('plan_approval.step2', $validated);

        return redirect()->route('plan-approval.step3');
    }

    public function step3(Request $request)
    {
        return view('plan-approval.step3');
    }

    public function postStep3(Request $request, Architecture263Api $api)
    {
        $validated = $request->validate([
            'area_ground_floor' => 'required|numeric',
            'area_total' => 'required|numeric',
            'area_outbuildings' => 'nullable|numeric',
            'fire_fighting_equipment' => 'nullable|string',
        ]);

        // Calculate charges (mock logic based on PDF structure)
        // (a) Buildings Plans (minimum fee $16-15) - $1.75 for every $100
        // (b) Sewerage Work - $1.75 for every $100
        // (c) Sewer Connection

        if (! $this->saveDraftStep($request, $api, $validated)) {
            return back()->withInput()->with('error', 'Could not save your progress. Please try again.');
        }

        $request->session()->put('plan_approval.step3', $validated);

        return redirect()->route('plan-approval.step4');
    }

    public function step4(Request $request)
    {
        $data = $request->session()->get('plan_approval', []);

        return view('plan-approval.step4', compact('data'));
    }

    public function submit(Request $request, Architecture263Api $api)
    {
        $engagementId = $request->session()->get('plan_approval.engagement_id');
        $draftId = $request->session()->get('plan_approval.draft_id');

        if (! $engagementId || ! $draftId) {
            return redirect()->route('plan-approval.index')->with('error', 'No application data found.');
        }

        $request->validate([
            'drawings' => ['required', 'file', 'mimes:pdf,zip', 'max:20480'],
        ]);

        try {
            $token = $request->cookie('portal_token');
            if (!$token) {
                 return redirect()->route('portal.login')->with('error', 'Session expired. Please login again.');
            }

            $response = $api->finalizePlanApplication($token, $draftId, [], $request->file('drawings'));

            if ($response->failed()) {
                Log::error('Plan application finalize failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                $errors = $response->json('errors');
                $message = is_array($errors)
                    ? collect($errors)->flatten()->first()
                    : ($response->json('message') ?? 'Unknown error');

                return back()->with('error', 'Submission failed: ' . $message);
            }

            // Success - clear session and redirect
            $request->session()->forget('plan_approval');
            return redirect()->route('plan-approval.index')->with('success', 'Application submitted successfully!');

        } catch (\Exception $e) {
            Log::error('Plan application submission exception', ['message' => $e->getMessage()]);
             return back()->with('error', 'An error occurred while submitting your application. Please try again later.');
        }
    }

    /**
     * Persist the just-completed step to the backend draft immediately —
     * this is what makes progress durable (save-and-continue) instead of
     * living only in the PHP session.
     */
    private function saveDraftStep(Request $request, Architecture263Api $api, array $stepData): bool
    {
        $token = $request->cookie('portal_token');
        $engagementId = $request->session()->get('plan_approval.engagement_id');

        if (! $token || ! $engagementId) {
            return false;
        }

        $response = $api->saveDraftPlanApplication($token, array_merge($stepData, [
            'engagement_id' => $engagementId,
        ]));

        if ($response->failed()) {
            Log::error('Plan application draft save failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        $request->session()->put('plan_approval.draft_id', $response->json('id'));

        return true;
    }

    /**
     * On entering the wizard, pull back whatever draft already exists for
     * this engagement (from a previous session, device, or login) and
     * repopulate the session so the step forms prefill correctly.
     */
    private function hydrateSessionFromDraft(Request $request, Architecture263Api $api, int $engagementId): void
    {
        if ($request->session()->get('plan_approval.draft_id')) {
            return; // already hydrated this session
        }

        $token = $request->cookie('portal_token');
        if (! $token) {
            return;
        }

        $response = $api->getDraftPlanApplication($token, $engagementId);

        if ($response->failed() || ! $response->json('draft')) {
            return;
        }

        $draft = $response->json('draft');
        $request->session()->put('plan_approval.draft_id', $draft['id']);

        foreach (self::STEP_FIELDS as $step => $fields) {
            $stepData = array_filter(
                array_intersect_key($draft, array_flip($fields)),
                fn ($value) => $value !== null
            );

            if (! empty($stepData)) {
                $request->session()->put("plan_approval.{$step}", $stepData);
            }
        }
    }
}
