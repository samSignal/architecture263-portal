@extends('layouts.wizard')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <h1 class="fw-bold mb-4 text-primary">My Plan Applications</h1>

                    @if (empty($applications))
                        <p class="text-muted">No plan applications submitted yet.</p>
                    @else
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Plan No</th>
                                    <th>Project Type</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($applications as $app)
                                    <tr>
                                        <td>{{ $app['plan_no'] }}</td>
                                        <td>{{ $app['project_type'] }}</td>
                                        <td>@include('partials.status-badge', ['status' => $app['status']])</td>
                                        <td class="text-end">
                                            <a href="{{ route('plan-applications.show', $app['id']) }}" class="btn btn-outline-primary btn-sm">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
