@extends('layouts.wizard')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <a href="{{ route('council.index') }}" class="text-muted small">&larr; Back to dashboard</a>
                    <h1 class="fw-bold mb-3 text-primary mt-2">Plan {{ $application['plan_no'] }}</h1>
                    <div class="mb-4">@include('partials.status-badge', ['status' => $application['status']])</div>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <h5>Submitting Architect</h5>
                            @if ($application['architect'] ?? null)
                                <div class="border rounded p-3">
                                    <div class="fw-semibold">{{ $application['architect']['name'] }}</div>
                                    <div class="small text-muted">{{ $application['architect']['email'] }}</div>
                                    @if ($application['architect']['firm_name'] ?? null)
                                        <div class="small text-muted">{{ $application['architect']['firm_name'] }}</div>
                                    @endif
                                    @if ($application['architect']['registration_no'] ?? null)
                                        <div class="small text-muted">Reg No: {{ $application['architect']['registration_no'] }}</div>
                                    @endif
                                </div>
                            @else
                                <p class="text-muted">Not available.</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h5>Client</h5>
                            @if ($application['client'] ?? null)
                                <div class="border rounded p-3">
                                    <div class="fw-semibold">{{ $application['client']['name'] }}</div>
                                    <div class="small text-muted">{{ $application['client']['email'] }}</div>
                                </div>
                            @else
                                <p class="text-muted">Not available.</p>
                            @endif
                        </div>
                    </div>

                    <h5 class="mt-3">Project Details</h5>
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

                    <h5 class="mt-4">Drawing Review</h5>
                    <p class="text-muted small">Preview the plan below. Use the tools to drop a pin or draw a line at the spot that needs correction, then add a comment — the architect will see it marked directly on their drawing.</p>
                    @include('partials.plan-viewer', [
                        'previewUrl' => route('council.drawings.preview', $application['id']),
                        'downloadUrl' => ($application['has_drawings'] ?? false) ? route('council.drawings', $application['id']) : null,
                        'fileName' => $application['drawings_original_name'] ?? null,
                        'hasDrawings' => $application['has_drawings'] ?? false,
                        'markups' => $application['markups'] ?? [],
                        'interactive' => true,
                        'storeUrl' => route('council.markups.store', $application['id']),
                        'viewerId' => 'council-'.$application['id'],
                    ])

                    @include('partials.drawing-history', [
                        'history' => $application['drawings_history'] ?? [],
                        'downloadRoute' => 'council.drawings.version',
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

                    <form method="POST" action="{{ route('council.comments.store', $application['id']) }}" class="mb-4">
                        @csrf
                        <div class="mb-2">
                            <textarea name="body" class="form-control" rows="3" placeholder="Add a technical review comment" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-outline-primary btn-sm">Add Comment</button>
                    </form>

                    <h5>Decision</h5>
                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('council.decide', $application['id']) }}">
                            @csrf
                            <input type="hidden" name="decision" value="approved">
                            <button type="submit" class="btn btn-success">Final Approval</button>
                        </form>
                        <form method="POST" action="{{ route('council.decide', $application['id']) }}">
                            @csrf
                            <input type="hidden" name="decision" value="revision_requested">
                            <button type="submit" class="btn btn-warning">Return for Correction</button>
                        </form>
                        <form method="POST" action="{{ route('council.decide', $application['id']) }}">
                            @csrf
                            <input type="hidden" name="decision" value="rejected">
                            <button type="submit" class="btn btn-danger">Reject Application</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
