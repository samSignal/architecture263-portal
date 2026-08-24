{{-- Expects: $status (string) --}}
@php
    $label = match ($status) {
        'draft' => 'Draft',
        'pending' => 'Pending Review',
        'under_review' => 'Under Review',
        'revision_requested' => 'Returned for Correction',
        'rejected' => 'Rejected',
        'approved' => 'Approved',
        default => ucfirst(str_replace('_', ' ', $status)),
    };
    $class = match ($status) {
        'approved' => 'bg-success',
        'rejected' => 'bg-danger',
        'revision_requested' => 'bg-warning text-dark',
        'under_review' => 'bg-info text-dark',
        'pending' => 'bg-secondary',
        default => 'bg-secondary',
    };
@endphp
<span class="badge {{ $class }}">{{ $label }}</span>
