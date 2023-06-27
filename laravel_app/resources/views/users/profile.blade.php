@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    {{-- <div class="card-header">{{ __('User Profile') }}</div> --}}
                    <div class="card-body">
                        <h3>Lietotaja vards: {{ $user->name }}</h3>
                        {{-- parada profila bildi --}}
                        <a target="_blank"> 
                            @php $imagePath = asset('storage/images/' . $user->profile_picture_path); @endphp
                            <img class="profile_image" src="{{ $imagePath }}" alt="{{$imagePath}}">
                        </a>
                        <style> 
                            .profile_image {
                                width:50px;
                                height: 50px;
                            }
                        </style>
                        {{-- profila bildes nomainishana --}}
                        <form action="{{ route('users.change_profile_picture', ['id' => $user->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="image">Nomainit profila bildi</label>
                                <input type="file" name="image" id="image" class="form-control-file">
                            </div>
                            <button type="submit" class="btn btn-primary">Augsupieladet</button>
                        </form>
                        @if (auth()->user()->role === 'admin' || auth()->user()->name = $user->name)
                            <form action="{{ route('users.update_username', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $user->name }}">
                                <button type="submit" class="btn btn-sm btn-primary">Saglabat</button>
                            </form>
                        @else
                            {{ $user->name }}
                        @endif
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                        <p>Lietotaja epasts: {{ $user->email }}</p>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">{{ __('User Posts') }}</div>

                    <div class="card-body">
                        @foreach($user->images as $image)
                            <li>
                                <img src="{{ asset('storage/images/' . $image->filename) }}" alt="{{ $image->filename }}">
                                <span>{{ $user->name }}</span>
                                @if (auth()->user()->role === 'admin')
                                    <form action="{{ route('images.destroy', $image->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                @endif
                                <span> {{$image->apraksts}} </span>
                            </li>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
