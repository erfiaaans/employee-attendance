@extends('layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">
            <a href="#"><span class="text-muted fw-light">{{ __('Dashboards') }} /</span>
            </a>
            <a href="#" class="text-secondary">
                {{ __('Master User') }} /</a> {{ __('Detail') }}
        </h4>
        <div class="row">
            <h1>Data Dahboard</h1>
            <pre>{{ json_encode(Auth::user(), JSON_PRETTY_PRINT) }}</pre>

        </div>
    </div>
@endsection
