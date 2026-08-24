@extends('layouts.wizard')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <h1 class="fw-bold mb-4 text-primary">My Engagements</h1>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if ($me && in_array('architect', $me['roles'] ?? []) && ! ($me['blue_book_active'] ?? false))
                        <div class="alert alert-warning d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Blue Book access required.</strong>
                                You need active Blue Book (ACZ Conditions of Engagement &amp; Scale of Fees) access before clients can select you or you can sign contracts.
                                @if ($me['blue_book_requested_at'] ?? null)
                                    <div class="small text-muted mt-1">Requested on {{ $me['blue_book_requested_at'] }} — awaiting admin approval.</div>
                                @endif
                            </div>
                            @unless ($me['blue_book_requested_at'] ?? null)
                                <form method="POST" action="{{ route('blue-book.request') }}" class="ms-3">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm text-nowrap">I've Purchased the Blue Book</button>
                                </form>
                            @endunless
                        </div>
                    @endif

                    @if (empty($engagements))
                        <p class="text-muted">No engagements yet. @if($me && in_array('client', $me['roles'] ?? [])) <a href="{{ route('architects.index') }}">Search for an architect</a> to get started. @endif</p>
                    @else
                        <div class="list-group">
                            @foreach ($engagements as $engagement)
                                <a href="{{ route('engagements.show', $engagement['id']) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between">
                                        <span>Engagement #{{ $engagement['id'] }}</span>
                                        <span class="badge bg-secondary">{{ str_replace('_', ' ', $engagement['status']) }}</span>
                                    </div>
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
