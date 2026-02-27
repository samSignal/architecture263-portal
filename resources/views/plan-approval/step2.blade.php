@extends('layouts.wizard')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0 fw-bold text-primary">Step 2: People Involved</h4>
                        <span class="badge bg-primary rounded-pill">2 of 4</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('plan-approval.postStep2') }}" method="POST">
                        @csrf
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <!-- Building Owner -->
                        <h5 class="fw-bold mb-3 border-bottom pb-2">8. Building Owner</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label for="owner_name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="owner_name" name="owner_name" value="{{ session('plan_approval.step2.owner_name', old('owner_name')) }}" required>
                            </div>
                            <div class="col-12">
                                <label for="owner_address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="owner_address" name="owner_address" rows="2" required>{{ session('plan_approval.step2.owner_address', old('owner_address')) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label for="owner_phone" class="form-label">Telephone No.</label>
                                <input type="tel" class="form-control" id="owner_phone" name="owner_phone" value="{{ session('plan_approval.step2.owner_phone', old('owner_phone')) }}">
                            </div>
                        </div>

                        <!-- Architect -->
                        <h5 class="fw-bold mb-3 border-bottom pb-2">7. Architect</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label for="architect_name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="architect_name" name="architect_name" value="{{ session('plan_approval.step2.architect_name', old('architect_name')) }}">
                            </div>
                            <div class="col-12">
                                <label for="architect_address" class="form-label">Address</label>
                                <textarea class="form-control" id="architect_address" name="architect_address" rows="2">{{ session('plan_approval.step2.architect_address', old('architect_address')) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label for="architect_phone" class="form-label">Telephone No.</label>
                                <input type="tel" class="form-control" id="architect_phone" name="architect_phone" value="{{ session('plan_approval.step2.architect_phone', old('architect_phone')) }}">
                            </div>
                        </div>

                        <!-- Contractor -->
                        <h5 class="fw-bold mb-3 border-bottom pb-2">6. Building/Sewerage Contractor</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label for="contractor_name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="contractor_name" name="contractor_name" value="{{ session('plan_approval.step2.contractor_name', old('contractor_name')) }}">
                            </div>
                            <div class="col-12">
                                <label for="contractor_address" class="form-label">Address</label>
                                <textarea class="form-control" id="contractor_address" name="contractor_address" rows="2">{{ session('plan_approval.step2.contractor_address', old('contractor_address')) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label for="contractor_phone" class="form-label">Telephone No.</label>
                                <input type="tel" class="form-control" id="contractor_phone" name="contractor_phone" value="{{ session('plan_approval.step2.contractor_phone', old('contractor_phone')) }}">
                            </div>
                        </div>

                        <!-- Supervision -->
                        <div class="col-12 mb-4">
                            <label class="form-label fw-bold">12. Supervision: Will Architect or Engineer supervise the work? <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="supervision" id="supervision_yes" value="Yes" {{ (session('plan_approval.step2.supervision') == 'Yes' || old('supervision') == 'Yes') ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="supervision_yes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="supervision" id="supervision_no" value="No" {{ (session('plan_approval.step2.supervision') == 'No' || old('supervision') == 'No') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="supervision_no">No</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('plan-approval.step1') }}" class="btn btn-outline-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">Next Step <i class="ri-arrow-right-line ms-1"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
