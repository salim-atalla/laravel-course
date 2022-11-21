@extends('layout')


@section('content')
  @include('partials._hero')
  @include('partials._search')

  <div class="lg:grid lg:grid-cols-2 gap-4 space-y-4 md:space-y-0 mx-4">

    @if (count($listings) == 0)
      <p>No Listings Found</p>
    @else
      @foreach ($listings as $listing)
        <x-listing-card :listing="$listing" />
      @endforeach
    @endif

  </div>

  {{-- Pagination --}}
  <div class="mt-6 p-4">
    {{ $listings->links() }}
  </div>


@endsection
