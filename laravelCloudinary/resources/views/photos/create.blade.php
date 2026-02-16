@extends('layouts.app')

@section('content')

<div class="container">

    <h1>Upload Photo</h1>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('photos.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="mb-3">
            <label for="title">Title</label>
            <input type="text" name="title" class="form-control">
        </div>

        <div class="mb-3">
            <label for="image">Choose Image</label>
            <input type="file" name="image" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Upload</button>

    </form>

</div>

@endsection