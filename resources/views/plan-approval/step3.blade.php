@extends('layouts.wizard')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0 fw-bold text-primary">Step 3: Technical Details</h4>
                        <span class="badge bg-primary rounded-pill">3 of 4</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('plan-approval.postStep3') }}" method="POST">
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
                        
                        <!-- Areas -->
                        <h5 class="fw-bold mb-3 border-bottom pb-2">5. Areas (in sq.m)</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label for="area_ground_floor" class="form-label">Area of Main Building on ground floor (including outside walls) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="area_ground_floor" name="area_ground_floor" value="{{ session('plan_approval.step3.area_ground_floor', old('area_ground_floor')) }}" required>
                                    <span class="input-group-text">sq.m</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="area_total" class="form-label">Total area of all floors (including outside walls) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="area_total" name="area_total" value="{{ session('plan_approval.step3.area_total', old('area_total')) }}" required>
                                    <span class="input-group-text">sq.m</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="area_outbuildings" class="form-label">Area of Outbuildings (including outside walls)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="area_outbuildings" name="area_outbuildings" value="{{ session('plan_approval.step3.area_outbuildings', old('area_outbuildings')) }}">
                                    <span class="input-group-text">sq.m</span>
                                </div>
                                <div class="form-text text-muted">*When the building project is an addition, insert here only the area of addition or additions.</div>
                            </div>
                        </div>

                        <!-- Fire Fighting -->
                        <h5 class="fw-bold mb-3 border-bottom pb-2">10(b). Fire Safety</h5>
                        <div class="mb-4">
                            <label for="fire_fighting_equipment" class="form-label fw-bold">What Fire-fighting equipment is to be provided?</label>
                            <textarea class="form-control" id="fire_fighting_equipment" name="fire_fighting_equipment" rows="3">{{ session('plan_approval.step3.fire_fighting_equipment', old('fire_fighting_equipment')) }}</textarea>
                        </div>

                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="bx bx-error me-2 fs-4"></i>
                            <div>
                                <strong>Note:</strong> Charges payable will be calculated based on the estimated cost and areas provided.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('plan-approval.step2') }}" class="btn btn-outline-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">Next Step <i class="ri-arrow-right-line ms-1"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
