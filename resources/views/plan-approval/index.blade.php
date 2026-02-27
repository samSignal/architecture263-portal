@extends('layouts.wizard')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5 text-center">
                    <h1 class="fw-bold mb-3 text-primary">Plan Approval Application</h1>
                    <p class="lead text-muted mb-4">City of Harare - Department of Urban Planning Services</p>
                    
                    <div class="alert alert-info text-start mb-4">
                        <h5 class="alert-heading"><i class="ri-information-line me-2"></i>Before you begin</h5>
                        <p class="mb-0">Please ensure you have the following information ready:</p>
                        <ul class="mb-0 mt-2">
                            <li>Stand Number and Full Postal Address</li>
                            <li>Estimated Cost of Building/Sewerage Work</li>
                            <li>Architect and Contractor Details</li>
                            <li>Drawings and Calculations (to be uploaded later)</li>
                        </ul>
                    </div>

                    <a href="{{ route('plan-approval.step1') }}" class="btn btn-primary btn-lg px-5">
                        Start Application <i class="ri-arrow-right-line ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
