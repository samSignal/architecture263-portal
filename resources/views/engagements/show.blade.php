@extends('layouts.wizard')

@php
    $isClient = $me && $me['id'] === $engagement['client_id'];
    $isArchitect = $me && $me['id'] === $engagement['architect_id'];
@endphp

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <a href="{{ route('engagements.index') }}" class="text-muted small">&larr; Back to engagements</a>
                    <h1 class="fw-bold mb-3 text-primary mt-2">Engagement #{{ $engagement['id'] }}</h1>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <table class="table">
                        <tr>
                            <th style="width: 220px">Status</th>
                            <td><span class="badge bg-secondary">{{ str_replace('_', ' ', $engagement['status']) }}</span></td>
                        </tr>
                        <tr>
                            <th>Architect approved</th>
                            <td>{{ $engagement['architect_approved_at'] ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Client signed contract</th>
                            <td>{{ $engagement['client_signed_at'] ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Architect signed contract</th>
                            <td>{{ $engagement['architect_signed_at'] ?? '—' }}</td>
                        </tr>
                    </table>

                    <div class="d-flex gap-2 mt-3">
                        @if ($isArchitect && $engagement['status'] === 'pending_architect_approval')
                            <form method="POST" action="{{ route('engagements.approve', $engagement['id']) }}">
                                @csrf
                                <button type="submit" class="btn btn-success">Approve Engagement</button>
                            </form>
                        @endif

                        @if (in_array($engagement['status'], ['architect_approved', 'contract_signed']))
                            @if ($isClient && ! $engagement['client_signed_at'])
                                <form method="POST" action="{{ route('engagements.contract.sign', $engagement['id']) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">Sign Contract</button>
                                </form>
                            @endif
                            @if ($isArchitect && ! $engagement['architect_signed_at'])
                                <form method="POST" action="{{ route('engagements.contract.sign', $engagement['id']) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">Sign Contract</button>
                                </form>
                            @endif
                        @endif
                        @if ($isArchitect && $engagement['status'] === 'contract_signed')
                            <a href="{{ route('plan-approval.index', ['engagement_id' => $engagement['id']]) }}" class="btn btn-outline-primary">
                                Submit Plans
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
