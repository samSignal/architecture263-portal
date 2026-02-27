<?php

namespace Tests\Feature;

use Tests\TestCase;

class PlanApprovalTest extends TestCase
{
    public function test_can_view_plan_approval_index()
    {
        $response = $this->get(route('plan-approval.index'));
        $response->assertStatus(200);
        $response->assertSee('Plan Approval Application');
    }

    public function test_can_view_step1()
    {
        $response = $this->get(route('plan-approval.step1'));
        $response->assertStatus(200);
        $response->assertSee('Step 1: Project Details');
    }

    public function test_can_submit_step1()
    {
        $data = [
            'stand_no' => '1234 Test',
            'postal_address' => '123 Test St',
            'estimated_cost' => '50000',
            'purpose' => 'Residential',
            'project_type' => 'New Building',
        ];

        $response = $this->post(route('plan-approval.postStep1'), $data);
        $response->assertRedirect(route('plan-approval.step2'));

        $this->assertEquals('1234 Test', session('plan_approval.step1.stand_no'));
    }

    public function test_can_submit_full_flow()
    {
        // Step 1
        $this->post(route('plan-approval.postStep1'), [
            'stand_no' => '1234',
            'postal_address' => 'Addr',
            'estimated_cost' => '1000',
            'purpose' => 'Res',
            'project_type' => 'New Building',
        ]);

        // Step 2
        $this->post(route('plan-approval.postStep2'), [
            'owner_name' => 'Owner',
            'owner_address' => 'Addr',
            'supervision' => 'Yes',
        ]);

        // Step 3
        $this->post(route('plan-approval.postStep3'), [
            'area_ground_floor' => '100',
            'area_total' => '100',
        ]);

        // Step 4 (View)
        $response = $this->get(route('plan-approval.step4'));
        $response->assertSee('Review');
        $response->assertSee('1234'); // Stand No
        $response->assertSee('Owner'); // Owner Name

        // Submit
        $response = $this->post(route('plan-approval.submit'));
        $response->assertRedirect(route('plan-approval.index'));
        $response->assertSessionHas('success');
    }
}
