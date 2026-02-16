@extends('layouts.app')

@section('content')

<div class="container">
    <h2>Uploaded Photos</h2>

    <a href="{{ route('photos.create') }}" class="btn btn-primary mb-3">Upload New Photo</a>

    <div class="row">
        @forelse($photos as $photo)
        <div class="col-md-4 mb-3">
            <div class="card">
                <img src="{{ $photo->image_url }}" class="card-img-top" alt="Photo">
                <div class="card-body">
                    <h5>{{ $photo->title ?? 'Untitled' }}</h5>
                    <form action="{{ route('photos.destroy', $photo) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger w-100">Delete</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <p>No photos uploaded yet.</p>
        @endforelse
    </div>
</div>

@endsection