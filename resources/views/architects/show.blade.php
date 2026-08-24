@extends('layouts.wizard')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <a href="{{ route('architects.index') }}" class="text-muted small">&larr; Back to search</a>
                    <h1 class="fw-bold mb-3 text-primary mt-2">{{ $architect['name'] }}</h1>

                    <table class="table">
                        <tr>
                            <th style="width: 200px">Registration No</th>
                            <td>{{ $architect['registration_no'] ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Specialty</th>
                            <td>{{ $architect['specialty'] ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Firm</th>
                            <td>{{ $architect['firm_name'] ?? '—' }}</td>
                        </tr>
                    </table>

                    <form method="POST" action="{{ route('engagements.store') }}">
                        @csrf
                        <input type="hidden" name="architect_id" value="{{ $architect['id'] }}">
                        <button type="submit" class="btn btn-primary btn-lg">Select Architect</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
