@extends('layouts.wizard')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <a href="{{ route('plan-applications.show', $application['id']) }}" class="text-muted small">&larr; Back to plan {{ $application['plan_no'] }}</a>
                    <h1 class="fw-bold mb-1 text-primary mt-2">Fix &amp; Resubmit</h1>
                    <p class="text-muted">Correct whatever council flagged, upload the updated drawings if they changed, then resubmit. This sends the application back into the review queue.</p>

                    <form action="{{ route('plan-applications.resubmit', $application['id']) }}" method="POST" enctype="multipart/form-data" id="resubmitForm">
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
                                <label for="plan_no" class="form-label fw-bold">Plan No <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('plan_no') is-invalid @enderror" id="plan_no" name="plan_no" value="{{ old('plan_no', $application['plan_no']) }}" required>
                                @error('plan_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label for="stand_no" class="form-label fw-bold">Stand No <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('stand_no') is-invalid @enderror" id="stand_no" name="stand_no" value="{{ old('stand_no', $application['stand_no']) }}" required>
                                @error('stand_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label for="postal_address" class="form-label fw-bold">Postal Address <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('postal_address') is-invalid @enderror" id="postal_address" name="postal_address" rows="2" required>{{ old('postal_address', $application['postal_address']) }}</textarea>
                                @error('postal_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="estimated_cost" class="form-label fw-bold">Estimated Cost ($) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('estimated_cost') is-invalid @enderror" id="estimated_cost" name="estimated_cost" value="{{ old('estimated_cost', $application['estimated_cost']) }}" required>
                                @error('estimated_cost') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Project Type <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3 pt-2">
                                    @foreach (['New Building', 'Alteration', 'Addition'] as $type)
                                        @php $typeId = 'type_'.strtolower(str_replace(' ', '_', $type)); @endphp
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="project_type" id="{{ $typeId }}" value="{{ $type }}" {{ old('project_type', $application['project_type']) === $type ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="{{ $typeId }}">{{ $type }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('project_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label for="purpose" class="form-label fw-bold">Purpose <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('purpose') is-invalid @enderror" id="purpose" name="purpose" value="{{ old('purpose', $application['purpose']) }}" required>
                                @error('purpose') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label for="industry_type" class="form-label fw-bold">Industry Type</label>
                                <input type="text" class="form-control @error('industry_type') is-invalid @enderror" id="industry_type" name="industry_type" value="{{ old('industry_type', $application['industry_type']) }}">
                                @error('industry_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12"><hr></div>

                            <div class="col-12">
                                <label for="owner_name" class="form-label fw-bold">Owner Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('owner_name') is-invalid @enderror" id="owner_name" name="owner_name" value="{{ old('owner_name', $application['owner_name']) }}" required>
                                @error('owner_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label for="owner_address" class="form-label fw-bold">Owner Address <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('owner_address') is-invalid @enderror" id="owner_address" name="owner_address" rows="2" required>{{ old('owner_address', $application['owner_address']) }}</textarea>
                                @error('owner_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="owner_phone" class="form-label fw-bold">Owner Phone</label>
                                <input type="text" class="form-control @error('owner_phone') is-invalid @enderror" id="owner_phone" name="owner_phone" value="{{ old('owner_phone', $application['owner_phone']) }}">
                                @error('owner_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Supervision: Will Architect or Engineer supervise the work? <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3 pt-2">
                                    @foreach (['Yes', 'No'] as $option)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="supervision" id="supervision_{{ strtolower($option) }}" value="{{ $option }}" {{ old('supervision', $application['supervision']) === $option ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="supervision_{{ strtolower($option) }}">{{ $option }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('supervision') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12"><hr></div>

                            <div class="col-md-4">
                                <label for="area_ground_floor" class="form-label fw-bold">Ground Floor Area <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('area_ground_floor') is-invalid @enderror" id="area_ground_floor" name="area_ground_floor" value="{{ old('area_ground_floor', $application['area_ground_floor']) }}" required>
                                @error('area_ground_floor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="area_first_floor" class="form-label fw-bold">First Floor Area</label>
                                <input type="number" step="0.01" class="form-control @error('area_first_floor') is-invalid @enderror" id="area_first_floor" name="area_first_floor" value="{{ old('area_first_floor', $application['area_first_floor']) }}">
                                @error('area_first_floor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="area_total" class="form-label fw-bold">Total Area <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('area_total') is-invalid @enderror" id="area_total" name="area_total" value="{{ old('area_total', $application['area_total']) }}" required>
                                @error('area_total') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="area_outbuildings" class="form-label fw-bold">Outbuildings Area</label>
                                <input type="number" step="0.01" class="form-control @error('area_outbuildings') is-invalid @enderror" id="area_outbuildings" name="area_outbuildings" value="{{ old('area_outbuildings', $application['area_outbuildings']) }}">
                                @error('area_outbuildings') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="fire_fighting_equipment" class="form-label fw-bold">Fire Fighting Equipment</label>
                                <input type="text" class="form-control @error('fire_fighting_equipment') is-invalid @enderror" id="fire_fighting_equipment" name="fire_fighting_equipment" value="{{ old('fire_fighting_equipment', $application['fire_fighting_equipment']) }}">
                                @error('fire_fighting_equipment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12"><hr></div>

                            <div class="col-12">
                                <label for="drawings" class="form-label fw-bold">Updated Drawings</label>
                                <input type="file" class="form-control @error('drawings') is-invalid @enderror" id="drawings" name="drawings" accept=".pdf,.zip">
                                <div class="form-text">
                                    Current file: {{ $application['drawings_original_name'] ?? 'none' }} (v{{ $application['drawings_version'] ?? 1 }}).
                                    Leave blank to keep it as-is, or upload a corrected PDF/ZIP (max 20MB) — it's scanned for viruses automatically, and kept as a new version rather than replacing the old one, so council's earlier submission stays on record. Council's markings on the previous version won't carry over onto the new one.
                                </div>
                                @error('drawings') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('plan-applications.show', $application['id']) }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="resubmitBtn">
                                <i class="ri-upload-2-line me-1"></i> Resubmit for Review
                            </button>
                        </div>
                    </form>

                    <script>
                        document.getElementById('resubmitForm').addEventListener('submit', function () {
                            const btn = document.getElementById('resubmitBtn');
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
