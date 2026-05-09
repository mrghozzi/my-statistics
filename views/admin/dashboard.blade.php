@extends('admin::layouts.admin')

@section('title', __('my_statistics::messages.dashboard_title'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li>{{ __('messages.plugins') }}</li>
                <li>{{ __('my_statistics::messages.dashboard_title') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('my_statistics::messages.analytics') }}</div>
            <h1 class="admin-hero__title">{{ __('my_statistics::messages.dashboard_title') }}</h1>
            <p class="admin-hero__copy">{{ __('my_statistics::messages.dashboard_desc') }}</p>
        </div>
    </section>

    <div class="row g-3 mt-1">
        <div class="col-md-6 col-xl-3">
            <div class="admin-panel h-100">
                <div class="admin-panel__body">
                    <span class="admin-panel__eyebrow">{{ __('my_statistics::messages.today') }}</span>
                    <h2 class="admin-panel__title">{{ $todayVisitors }} <small class="fs-6 text-muted">/ {{ $todayHits }} {{ __('my_statistics::messages.hits') }}</small></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="admin-panel h-100">
                <div class="admin-panel__body">
                    <span class="admin-panel__eyebrow">{{ __('my_statistics::messages.yesterday') }}</span>
                    <h2 class="admin-panel__title">{{ $yesterdayVisitors }} <small class="fs-6 text-muted">/ {{ $yesterdayHits }} {{ __('my_statistics::messages.hits') }}</small></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="admin-panel h-100">
                <div class="admin-panel__body">
                    <span class="admin-panel__eyebrow">{{ __('my_statistics::messages.last_7_days') }}</span>
                    <h2 class="admin-panel__title">{{ $last7DaysVisitors }} <small class="fs-6 text-muted">/ {{ $last7DaysHits }} {{ __('my_statistics::messages.hits') }}</small></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="admin-panel h-100">
                <div class="admin-panel__body">
                    <span class="admin-panel__eyebrow">{{ __('my_statistics::messages.total') }}</span>
                    <h2 class="admin-panel__title">{{ $totalVisitors }} <small class="fs-6 text-muted">/ {{ $totalHits }} {{ __('my_statistics::messages.hits') }}</small></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Traffic Trend Chart -->
    <section class="admin-panel mt-4">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('my_statistics::messages.chart') }}</span>
                <h2 class="admin-panel__title">{{ __('my_statistics::messages.traffic_trend') }}</h2>
            </div>
        </div>
        <div class="admin-panel__body">
            <canvas id="trafficTrendChart" height="250"></canvas>
        </div>
    </section>

    <!-- Detailed Stats row -->
    <div class="row g-3 mt-4">
        <div class="col-xl-8">
            <section class="admin-panel h-100">
                <div class="admin-panel__header">
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('my_statistics::messages.content') }}</span>
                        <h2 class="admin-panel__title">{{ __('my_statistics::messages.top_pages') }}</h2>
                    </div>
                </div>
                <div class="admin-panel__body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('my_statistics::messages.page') }}</th>
                                    <th class="text-end">{{ __('my_statistics::messages.views') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topPages as $page)
                                <tr>
                                    <td>
                                        <a href="{{ $page->url }}" target="_blank" class="text-dark fw-bold text-decoration-none">
                                            {{ $page->title ?: $page->url }}
                                        </a>
                                        <div class="fs-12 text-muted">{{ Str::limit($page->url, 60) }}</div>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-primary-subtle text-primary">{{ $page->views }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="2" class="text-center text-muted py-4">{{ __('my_statistics::messages.no_data') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
        
        <div class="col-xl-4 d-flex flex-column gap-3">
            <section class="admin-panel">
                <div class="admin-panel__header">
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('my_statistics::messages.software') }}</span>
                        <h2 class="admin-panel__title">{{ __('my_statistics::messages.top_browsers') }}</h2>
                    </div>
                </div>
                <div class="admin-panel__body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($topBrowsers as $b)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $b->browser }}
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">{{ $b->views }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </section>

            <section class="admin-panel flex-grow-1">
                <div class="admin-panel__header">
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('my_statistics::messages.hardware') }}</span>
                        <h2 class="admin-panel__title">{{ __('my_statistics::messages.top_os_devices') }}</h2>
                    </div>
                </div>
                <div class="admin-panel__body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($topOS as $o)
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 pb-1">
                            <span><i class="feather-monitor me-2 text-muted"></i> {{ $o->os }}</span>
                            <span class="fw-bold">{{ $o->views }}</span>
                        </li>
                        @endforeach
                        <li class="list-group-item border-top mt-2">
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                @foreach($deviceUsage as $d)
                                <div class="text-center">
                                    <div class="fs-12 text-muted">{{ $d->device }}</div>
                                    <div class="fw-bold">{{ $d->views }}</div>
                                </div>
                                @endforeach
                            </div>
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </div>

    <!-- Referrers and Countries -->
    <div class="row g-3 mt-4">
        <div class="col-xl-6">
            <section class="admin-panel h-100">
                <div class="admin-panel__header">
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('my_statistics::messages.acquisition') }}</span>
                        <h2 class="admin-panel__title">{{ __('my_statistics::messages.top_referrers') }}</h2>
                    </div>
                </div>
                <div class="admin-panel__body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($topReferrers as $ref)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $ref->domain }}
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">{{ $ref->views }}</span>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted py-4">{{ __('my_statistics::messages.no_referrers') }}</li>
                        @endforelse
                    </ul>
                </div>
            </section>
        </div>
        <div class="col-xl-6">
            <section class="admin-panel h-100">
                <div class="admin-panel__header">
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('my_statistics::messages.geography') }}</span>
                        <h2 class="admin-panel__title">{{ __('my_statistics::messages.top_countries') }}</h2>
                    </div>
                </div>
                <div class="admin-panel__body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($topCountries as $c)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><img src="https://flagcdn.com/20x15/{{ strtolower($c->country) }}.png" alt="{{ $c->country }}" class="me-2 rounded-1"> {{ $c->country }}</span>
                            <span class="fw-bold">{{ $c->views }}</span>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted py-4">{{ __('my_statistics::messages.no_countries') }}</li>
                        @endforelse
                    </ul>
                </div>
            </section>
        </div>
    </div>

    <!-- Latest Visitors -->
    <section class="admin-panel mt-4">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('my_statistics::messages.realtime') }}</span>
                <h2 class="admin-panel__title">{{ __('my_statistics::messages.latest_visitors') }}</h2>
            </div>
        </div>
        <div class="admin-panel__body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('my_statistics::messages.time') }}</th>
                            <th>{{ __('my_statistics::messages.page') }}</th>
                            <th>{{ __('my_statistics::messages.referrer') }}</th>
                            <th>{{ __('my_statistics::messages.details') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestVisitors as $v)
                        <tr>
                            <td class="text-nowrap">{{ \Carbon\Carbon::parse($v->created_at)->diffForHumans() }}</td>
                            <td>
                                <div class="fw-bold text-truncate" style="max-width: 300px;" title="{{ $v->title }}">{{ $v->title ?: __('my_statistics::messages.unknown') }}</div>
                                <div class="fs-12 text-muted text-truncate" style="max-width: 300px;">{{ $v->url }}</div>
                            </td>
                            <td>
                                @if($v->search_engine)
                                    <span class="badge bg-success-subtle text-success">{{ $v->search_engine }}</span>
                                @elseif($v->referrer)
                                    <a href="{{ $v->referrer }}" target="_blank" class="text-truncate d-inline-block text-decoration-none" style="max-width: 200px;">{{ $v->referrer }}</a>
                                @else
                                    <span class="text-muted">{{ __('my_statistics::messages.direct') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span title="{{ $v->browser }}"><i class="feather-globe text-primary"></i></span>
                                    <span title="{{ $v->os }}"><i class="feather-monitor text-secondary"></i></span>
                                    @if($v->country !== 'XX')
                                        <img src="https://flagcdn.com/16x12/{{ strtolower($v->country) }}.png" alt="{{ $v->country }}" title="{{ $v->country }}" class="rounded-1">
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">{{ __('my_statistics::messages.no_visitors') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('trafficTrendChart').getContext('2d');
        var trafficTrendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartDates) !!},
                datasets: [
                    {
                        label: '{{ __('my_statistics::messages.page_views') }}',
                        data: {!! json_encode($chartViews) !!},
                        borderColor: '#3454d1',
                        backgroundColor: 'rgba(52, 84, 209, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: '{{ __('my_statistics::messages.unique_visitors') }}',
                        data: {!! json_encode($chartVisitors) !!},
                        borderColor: '#25b865',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.4,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    });
</script>
@endpush
