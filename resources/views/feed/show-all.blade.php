@extends('larafeed::layouts.app')

@section('content')
<div class="card">
        <h3 class="card-header">{{ __('larafeed::general.search_new_feeds') }}</h3>
        <div class="card-body">
            @if(Auth::check())
                @if(Auth::user()->id === $user)
                    <form method="POST" action="{{ route('feed-search') }}">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="url" placeholder="Search for feed URL" aria-label="Search for feed URL" aria-describedby="button-feed-add" maxlength=”100″>
                            <button class="btn btn-outline-secondary" type="submit" type="button" id="button-feed-add">{{ __('larafeed::general.submit') }}</button>
                        </div>
                    </form>
                @else
                    {{ __('larafeed::general.no_permission') }}
                @endif
            @else
                {{ __('larafeed::general.must_login') }}
            @endif
        </div>
    </div>

    <section id="feed" class="feed">
        <div class="row feed-container m-0 p-0">
            <div class="col-12 m-0 p-0">
                <div class="row m-0 p-0">
                    @forelse($feeds->chunk(3) as $feedRow)
                        @foreach($feedRow as $key => $feedColumn)
                            @if ($key % 2 !== 0)
                                <div class="col-lg-2 vh-25"></div>
                            @endif
                            <div class="col-lg-5 col-sm-12 vh-25 p-0 mt-4 position-relative">
                                @if (Auth::check() && Auth::user()->id === $user)
                                    <form method="POST" action="{{ route('feed-user-remove') }}" class="close">
                                        @csrf
                                        <div class="input-group mb-3">
                                            <input type="hidden" id="id" name="id" value="{{ $feedColumn->id }}">
                                            <button class="btn btn-danger" type="submit" type="button" id="button-feed-add">X</button>
                                        </div>
                                    </form>
                                @endif
                                @if (Auth::check() && Auth::user()->id === $user)
                                    <a href="{{ route('feed-show', [$feedColumn->slug])}}">
                                @else
                                    <a href="{{ route('feed-user-show', ['user' => $user, 'slug' => $feedColumn->slug])}}">
                                @endif
                                    <div class="feed-item filter-app position-absolute border border-dark w-100 bg-white">
                                            <img src="{{ $feedColumn->image ?? ''}}" class="img-fluid" alt="">
                                            <div class="feed-info">
                                                <h4>{{ $feedColumn->title ?? ''}}</h4>
                                                <p>
                                                    {!! ($feedColumn->description) ? substr($feedColumn->description, 0, 150)
                                                            : __('larafeed::general.click_below') !!}...
                                                </p>
                                            </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @empty
                        <div class="card">
                            <div class="card-body">
                                {{ __('larafeed::general.no_feeds') }}
                            </div>
                        </div>
                    @endforelse
                    <div class="mt-4">
                        {!! $feeds->withQueryString()->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
