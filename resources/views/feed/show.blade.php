@extends('larafeed::layouts.app')

@section('content')
    <div class="card">
            @if ($feed)
                <h1 class="card-header">{{ $feed['title'] }}</h1>
                <div class="card-body">
                    <div class="vh-25 image-wrapper">
                        <img
                            alt="Responsive image"
                            src="{{ $feed['image'] }}"
                        >
                    </div>
                    <p class="pt-3">
                        {{ $feed['description'] }}
                    </p>
                    <ul class="list-group">
                        @if ($feed['entries'])
                            @forelse($feed['entries'] as $entry)
                                <li class="list-group-item">
                                    <a href="{{ $entry['link'] ?? ''}}" class="text-decoration-none link-secondary">
                                        <div class="row">
                                            <h3 class="col-12 nav-header disabled mb-4">{{ $entry['title'] ?? ''}}</h3>
                                            <div class="col-md-2 col-xs-12">
                                                <div class="thumbnail-container">
                                                    <img class="img-thumbnail mb-4" src="{{ $entry['image'] ? $entry['image'] : asset('vendor/larafeed/images/default.png') }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-10 col-xs-12 card-text mb-4">{!! $entry['description'] ?? '' !!}</div>
                                            <div class="card-footer mt-3">
                                                <div class="footer-height row">
                                                    <p class="col-md-10 col-sm-12 text-muted">{{ $entry['published_date'] ?? ''}}</p>
                                                    <small class="col-md-2 col-sm-12">{{ __('larafeed::general.article_click') }} </small>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li class="list-group-item">{{ __('larafeed::general.no_posts') }}</li>
                            @endforelse
                        @endif
                    </ul>
                </div>
            @endif
    </div>
@endsection
