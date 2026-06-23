@extends('admin::layouts.admin')

@section('title', __('my_statistics::messages.settings'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.my_statistics.index') }}">{{ __('my_statistics::messages.dashboard_title') }}</a></li>
                <li>{{ __('my_statistics::messages.settings') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('my_statistics::messages.analytics') }}</div>
            <h1 class="admin-hero__title">{{ __('my_statistics::messages.settings') }}</h1>
        </div>
        <div class="admin-hero__actions">
            <a href="{{ route('admin.my_statistics.index') }}" class="btn btn-outline-secondary">
                <i class="feather-arrow-left me-2"></i> {{ __('messages.back') }}
            </a>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success mt-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="row mt-4">
        <div class="col-xl-8">
            <div class="admin-panel">
                <div class="admin-panel__header">
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('my_statistics::messages.settings') }}</span>
                        <h2 class="admin-panel__title">{{ __('my_statistics::messages.settings') }}</h2>
                    </div>
                </div>
                <div class="admin-panel__body">
                    <form action="{{ route('admin.my_statistics.settings.update') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="sampling_rate" class="form-label fw-bold">{{ __('my_statistics::messages.sampling_rate') }}</label>
                            <input type="number" class="form-control" id="sampling_rate" name="sampling_rate" value="{{ old('sampling_rate', $samplingRate) }}" min="1" max="100" required>
                            <div class="form-text">{{ __('my_statistics::messages.sampling_rate_desc') }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="retention_days" class="form-label fw-bold">{{ __('my_statistics::messages.retention_days') }}</label>
                            <input type="number" class="form-control" id="retention_days" name="retention_days" value="{{ old('retention_days', $retentionDays) }}" min="0" required>
                            <div class="form-text">{{ __('my_statistics::messages.retention_days_desc') }}</div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-2"></i> {{ __('my_statistics::messages.save_changes') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
