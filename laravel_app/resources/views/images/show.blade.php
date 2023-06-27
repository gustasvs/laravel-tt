@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Image Details') }}</div>

                    <div class="card-body">
                        <img src="{{ asset('storage/images/' . $image->filename) }}" alt="{{ $image->filename }}">
                        <form action="{{ route('images.update_description', $image->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="description">Bildes Apraksts: </label>
                                <textarea name="description" id="description" class="form-control">{{ $image->apraksts }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Description</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
