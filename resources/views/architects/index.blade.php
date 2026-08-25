@extends('layouts.wizard')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <h1 class="fw-bold mb-3 text-primary">Search Active Architects</h1>
                    <p class="text-muted mb-4">Browse approved, active architects registered with the Institute.</p>

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="GET" action="{{ route('architects.index') }}" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="q" value="{{ $query }}" class="form-control" placeholder="Search by name, firm, or specialty">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>

                    @if (empty($architects))
                        <p class="text-muted">No active architects found.</p>
                    @else
                        <div class="list-group">
                            @foreach ($architects as $architect)
                                <a href="{{ route('architects.show', $architect['id']) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">{{ $architect['name'] }}</span>
                                        <span class="text-muted">{{ $architect['firm_name'] ?? '' }}</span>
                                    </div>
                                    <small class="text-muted">{{ $architect['specialty'] ?? 'Architect' }}</small>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
