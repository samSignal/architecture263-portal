{{--
    Expects:
      $history       (array)  application['drawings_history'] — [['version','original_name','uploaded_by_name','created_at','is_current'], ...]
      $downloadRoute (string) route name that accepts ($applicationId, $version), e.g. 'council.drawings.version'
      $applicationId (int)
--}}
@if (! empty($history))
    <div class="mt-3">
        <button class="btn btn-link btn-sm text-decoration-none ps-0" type="button" data-bs-toggle="collapse" data-bs-target="#drawingHistory-{{ $applicationId }}">
            <i class="ri-history-line me-1"></i> Drawing history ({{ count($history) }} {{ count($history) === 1 ? 'version' : 'versions' }})
        </button>
        <div class="collapse" id="drawingHistory-{{ $applicationId }}">
            <ul class="list-group list-group-flush small">
                @foreach (array_reverse($history) as $version)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>
                            <strong>v{{ $version['version'] }}</strong>
                            {{ $version['original_name'] }}
                            <span class="text-muted">— {{ $version['uploaded_by_name'] ?? 'unknown' }}, {{ $version['created_at'] }}</span>
                            @if ($version['is_current'])
                                <span class="badge bg-primary ms-1">current</span>
                            @endif
                        </span>
                        <a href="{{ route($downloadRoute, [$applicationId, $version['version']]) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="ri-download-line"></i>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
