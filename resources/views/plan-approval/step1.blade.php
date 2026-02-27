@extends('layouts.wizard')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0 fw-bold text-primary">Step 1: Project Details</h4>
                        <span class="badge bg-primary rounded-pill">1 of 4</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('plan-approval.postStep1') }}" method="POST">
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
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="stand_no" class="form-label fw-bold">1. Stand No <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('stand_no') is-invalid @enderror" id="stand_no" name="stand_no" value="{{ session('plan_approval.step1.stand_no', old('stand_no')) }}" required placeholder="e.g. 1234 Highlands">
                                @error('stand_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="postal_address" class="form-label fw-bold">2. Full Postal Address of Stand <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('postal_address') is-invalid @enderror" id="postal_address" name="postal_address" rows="3" required>{{ session('plan_approval.step1.postal_address', old('postal_address')) }}</textarea>
                                @error('postal_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="estimated_cost" class="form-label fw-bold">3. Estimated Cost of Building and/or Sewerage Work ($) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control @error('estimated_cost') is-invalid @enderror" id="estimated_cost" name="estimated_cost" value="{{ session('plan_approval.step1.estimated_cost', old('estimated_cost')) }}" required>
                                </div>
                                @error('estimated_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="purpose" class="form-label fw-bold">10. Purpose for which building is to be used <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('purpose') is-invalid @enderror" id="purpose" name="purpose" value="{{ session('plan_approval.step1.purpose', old('purpose')) }}" required placeholder="e.g. Residential, Commercial">
                                @error('purpose')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="industry_type" class="form-label fw-bold">10(a). If industrial, state type of industry</label>
                                <input type="text" class="form-control @error('industry_type') is-invalid @enderror" id="industry_type" name="industry_type" value="{{ session('plan_approval.step1.industry_type', old('industry_type')) }}">
                                @error('industry_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">11. Is it a New Building, Alteration, or Addition? <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="project_type" id="type_new" value="New Building" {{ (session('plan_approval.step1.project_type') == 'New Building' || old('project_type') == 'New Building') ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="type_new">New Building</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="project_type" id="type_alteration" value="Alteration" {{ (session('plan_approval.step1.project_type') == 'Alteration' || old('project_type') == 'Alteration') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="type_alteration">Alteration</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="project_type" id="type_addition" value="Addition" {{ (session('plan_approval.step1.project_type') == 'Addition' || old('project_type') == 'Addition') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="type_addition">Addition</label>
                                    </div>
                                </div>
                                @error('project_type')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('plan-approval.index') }}" class="btn btn-outline-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">Next Step <i class="ri-arrow-right-line ms-1"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
