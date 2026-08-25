@extends('layouts.wizard')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0 fw-bold text-primary">Step 4: Review & Submit</h4>
                        <span class="badge bg-primary rounded-pill">4 of 4</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                        <i class="bx bx-check-circle me-2 fs-4"></i>
                        <div>
                            Please review your application details below before submitting.
                        </div>
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted text-uppercase small">Project Details</h6>
                            <p class="mb-1"><strong>Stand No:</strong> {{ $data['step1']['stand_no'] ?? '-' }}</p>
                            <p class="mb-1"><strong>Address:</strong> {{ $data['step1']['postal_address'] ?? '-' }}</p>
                            <p class="mb-1"><strong>Est. Cost:</strong> ${{ number_format($data['step1']['estimated_cost'] ?? 0, 2) }}</p>
                            <p class="mb-1"><strong>Purpose:</strong> {{ $data['step1']['purpose'] ?? '-' }}</p>
                            <p class="mb-0"><strong>Type:</strong> <span class="badge bg-info text-dark">{{ $data['step1']['project_type'] ?? '-' }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted text-uppercase small">People Involved</h6>
                            <p class="mb-1"><strong>Owner:</strong> {{ $data['step2']['owner_name'] ?? '-' }}</p>
                            <p class="mb-1"><strong>Architect:</strong> {{ $data['step2']['architect_name'] ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Contractor:</strong> {{ $data['step2']['contractor_name'] ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>Supervision:</strong> {{ $data['step2']['supervision'] ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted text-uppercase small">Technical Details</h6>
                            <p class="mb-1"><strong>Ground Floor Area:</strong> {{ $data['step3']['area_ground_floor'] ?? 0 }} sq.m</p>
                            <p class="mb-1"><strong>Total Area:</strong> {{ $data['step3']['area_total'] ?? 0 }} sq.m</p>
                            <p class="mb-0"><strong>Outbuildings:</strong> {{ $data['step3']['area_outbuildings'] ?? 0 }} sq.m</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted text-uppercase small">Charges Payable (Est.)</h6>
                            <p class="mb-1"><strong>Building Plans:</strong> $16.15 (Min)</p>
                            <p class="mb-1"><strong>Sewerage Work:</strong> TBD</p>
                            <p class="mb-0"><strong>Sewer Connection:</strong> TBD</p>
                        </div>
                    </div>

                    <form action="{{ route('plan-approval.submit') }}" method="POST" enctype="multipart/form-data" id="submitPlanForm">
                        @csrf

                        <h5 class="fw-bold mb-3 border-bottom pb-2">Drawings to Accompany this Form</h5>
                        <div class="mb-3">
                            <label for="drawings" class="form-label">Upload Plans (PDF, ZIP) <span class="text-danger">*</span></label>
                            <input class="form-control @error('drawings') is-invalid @enderror" type="file" id="drawings" name="drawings" accept=".pdf,.zip" required>
                            @error('drawings')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="form-text"><i class="ri-shield-check-line me-1"></i>PDF or ZIP, up to 20MB. Every upload is automatically scanned for viruses before it's accepted.</div>
                            @enderror
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" value="" id="declaration" required>
                            <label class="form-check-label" for="declaration">
                                I hereby declare that the information provided is true and correct to the best of my knowledge.
                            </label>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('plan-approval.step3') }}" class="btn btn-outline-secondary">Back</a>
                            <button type="submit" class="btn btn-success btn-lg px-5" id="submitPlanBtn">Submit Application <i class="ri-check-line ms-2"></i></button>
                        </div>
                    </form>

                    <script>
                        document.getElementById('submitPlanForm').addEventListener('submit', function () {
                            const btn = document.getElementById('submitPlanBtn');
                            btn.disabled = true;
                            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Scanning &amp; uploading…';
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
