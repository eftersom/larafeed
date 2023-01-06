@extends('larafeed::layouts.app')

@section('content')
    <div class="flex-center position-ref full-height">
        <div class="content">
            <div class="title m-b-md">
                {{ config('larafeed.name', 'Lara-feed') }}
            </div>

            <h2 class="pt-2">
                {{ __('larafeed::general.home_for_feeds') }}
            </h2>
        </div>
    </div>
@endsection
