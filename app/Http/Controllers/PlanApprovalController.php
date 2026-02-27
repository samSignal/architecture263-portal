<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlanApprovalController extends Controller
{
    public function index()
    {
        return view('plan-approval.index');
    }

    public function step1(Request $request)
    {
        return view('plan-approval.step1');
    }

    public function postStep1(Request $request)
    {
        // Store data in session
        $validated = $request->validate([
            'stand_no' => 'required|string',
            'postal_address' => 'required|string',
            'estimated_cost' => 'required|numeric',
            'purpose' => 'required|string',
            'industry_type' => 'nullable|string',
            'project_type' => 'required|string', // New, Alteration, Addition
        ]);

        $request->session()->put('plan_approval.step1', $validated);

        return redirect()->route('plan-approval.step2');
    }

    public function step2(Request $request)
    {
        return view('plan-approval.step2');
    }

    public function postStep2(Request $request)
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

        $request->session()->put('plan_approval.step2', $validated);

        return redirect()->route('plan-approval.step3');
    }

    public function step3(Request $request)
    {
        return view('plan-approval.step3');
    }

    public function postStep3(Request $request)
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

        $request->session()->put('plan_approval.step3', $validated);

        return redirect()->route('plan-approval.step4');
    }

    public function step4(Request $request)
    {
        $data = $request->session()->get('plan_approval', []);

        return view('plan-approval.step4', compact('data'));
    }

    public function submit(Request $request)
    {
        // Here we would save to the database
        // For now, just clear session and redirect to success
        $request->session()->forget('plan_approval');

        return redirect()->route('plan-approval.index')->with('success', 'Application submitted successfully!');
    }
}
