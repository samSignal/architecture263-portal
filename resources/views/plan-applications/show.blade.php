@extends('layouts.wizard')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <a href="{{ route('plan-applications.index') }}" class="text-muted small">&larr; Back to my plan applications</a>
                    <h1 class="fw-bold mb-3 text-primary mt-2">Plan {{ $application['plan_no'] }}</h1>

                    <div class="mb-4">@include('partials.status-badge', ['status' => $application['status']])</div>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if ($application['status'] === 'revision_requested')
                        <div class="alert alert-warning d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span>Council has returned this application for correction — see the pins and lines marked below, plus the review comments.</span>
                            @if ($me && $me['id'] === $application['submitted_by'])
                                <a href="{{ route('plan-applications.resubmit.form', $application['id']) }}" class="btn btn-warning btn-sm text-nowrap">
                                    <i class="ri-upload-2-line me-1"></i> Fix &amp; Resubmit
                                </a>
                            @endif
                        </div>
                    @endif

                    <h5>Project Details</h5>
                    <table class="table">
                        <tr><th style="width:220px">Stand No</th><td>{{ $application['stand_no'] }}</td></tr>
                        <tr><th>Postal Address</th><td>{{ $application['postal_address'] }}</td></tr>
                        <tr><th>Estimated Cost</th><td>{{ $application['estimated_cost'] }}</td></tr>
                        <tr><th>Purpose</th><td>{{ $application['purpose'] }}</td></tr>
                        <tr><th>Project Type</th><td>{{ $application['project_type'] }}</td></tr>
                        <tr><th>Owner</th><td>{{ $application['owner_name'] }}</td></tr>
                        <tr><th>Ground Floor Area</th><td>{{ $application['area_ground_floor'] }}</td></tr>
                        <tr><th>Total Area</th><td>{{ $application['area_total'] }}</td></tr>
                    </table>

                    <h5 class="mt-4">Council Markings &amp; Corrections</h5>
                    <p class="text-muted small">Pins and lines below show exactly where council flagged a correction, with their comment.</p>
                    @include('partials.plan-viewer', [
                        'previewUrl' => route('plan-applications.drawings.preview', $application['id']),
                        'downloadUrl' => ($application['has_drawings'] ?? false) ? route('plan-applications.drawings', $application['id']) : null,
                        'fileName' => $application['drawings_original_name'] ?? null,
                        'hasDrawings' => $application['has_drawings'] ?? false,
                        'markups' => $application['markups'] ?? [],
                        'interactive' => false,
                        'storeUrl' => null,
                        'viewerId' => 'app-'.$application['id'],
                    ])

                    @include('partials.drawing-history', [
                        'history' => $application['drawings_history'] ?? [],
                        'downloadRoute' => 'plan-applications.drawings.version',
                        'applicationId' => $application['id'],
                    ])

                    <h5 class="mt-4">Technical Review Comments</h5>
                    @forelse ($application['comments'] ?? [] as $comment)
                        <div class="border rounded p-2 mb-2">
                            <div class="small text-muted">{{ $comment['user_name'] }} &middot; {{ $comment['created_at'] }}</div>
                            <div>{{ $comment['body'] }}</div>
                        </div>
                    @empty
                        <p class="text-muted">No comments yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
