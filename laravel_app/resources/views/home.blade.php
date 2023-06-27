@extends('layouts.app')

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Ievieto savu bildi majaslapa!') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                </div>
                <form action="{{ route('images.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="image">Izvelies bildi</label>
                        <input type="file" name="image" id="image" class="form-control-file">
                    </div>
                    <div class="form-group">
                        <label for="description">Ieraksti aprakstu</label>
                        <textarea name="description" id="description" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Ielikt!</button>
                </form>
                <div class="display_images">
                    <div class="user-images">
                        <h3>Tavas bildes</h3>
                        <ul class="image-list">
                            @foreach(auth()->user()->images as $image)
                                <li>
                                    <a href="{{ route('images.show', $image->id) }}" target="_blank">
                                    @php $imagePath = asset('storage/images/' . $image->filename); @endphp
                                    <img src="{{ $imagePath }}" alt="{{ $image->filename }}">
                                    </a>
                                    {{-- <form action="{{ route('images.update_description', $image->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <textarea name="description" rows="3" placeholder="Enter description">{{ $image->description }}</textarea>
                                        <button type="submit" class="btn btn-primary">Save Description</button>
                                    </form> --}}
                                    <form action="{{ route('images.destroy', $image->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                    <span> {{$image->apraksts}} </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                
                    <div class="other-images">
                        <h3>Citu bildes</h3>
                        <ul class="image-list">
                            @foreach($users as $user)
                                @if ($user->id !== auth()->user()->id)
                                    @foreach($user->images as $image)
                                        <li>
                                            <a href="{{ route('images.show', $image->id) }}" target="_blank">
                                                @php $imagePath = asset('storage/images/' . $image->filename); @endphp
                                                <img src="{{ $imagePath }}" alt="{{ $image->filename }}">
                                            </a>
                                            <span>{{ $user->name }}</span>
                                            @if (auth()->user()->role === 'admin')
                                                <form action="{{ route('images.destroy', $image->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            @endif

                                            <div class="likes">
                                                <span>{{ $image->likes }} likes</span>
                                            </div>
                                            <div class="like-buttons">
                                                <form action="{{ route('images.like', $image->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary">Like</button>
                                                </form>
                                                <form action="{{ route('images.dislike', $image->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger">Dislike</button>
                                                </form>
                                            </div>

                                            <span> {{$image->apraksts}} </span>
                                        </li>
                                    @endforeach
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
                
                <style>
                    .display_images {
                        display: flex;
                        flex-wrap: wrap;
                        justify-content: center;
                    }
                    .display_images h3 {
                        text-align: center;
                    }
                    .user-images,
                    .other-images {
                        width: 100%;
                        margin-bottom: 20px;
                    }
                    .image-list {
                        display: flex;
                        flex-wrap: wrap;
                        justify-content: center;
                        list-style: none;
                        padding: 0;
                        margin: 0;
                    }
                
                    .image-list li {
                        flex: 0 0 auto;
                        width: 150px;
                        height: 120px;
                        margin: 5px;
                    }
                
                    .image-list img {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                        object-position: center;
                    }
                </style>
                
                <div class="display_users">
                    <div class="col-md-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Lietotājvārds</th>
                                    @if (auth()->user()->role === 'admin')
                                        <th>Loma</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        @if (auth()->user()->role === 'admin')
                                        <td>
                                            <form action="{{ route('users.update_role', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="role" value="{{ $user->role }}">
                                                    <label class="switch">
                                                    <input type="checkbox" onchange="this.form.submit()" {{ $user->role === 'admin' ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </form>
                                        </td>
                                        @endif
                                        <td>{{ $user->role }}</td>
                                        <td>
                                            @php $imagePath = asset('storage/images/' . $user->profile_picture_path); @endphp
                                            @if ($user->profile_picture_path !== 'none')
                                            <img class="profile_image" src="{{ $imagePath }}" alt="{{$imagePath}}">
                                            @else 
                                            {{-- <img class="profile_image" src="{{ $imagePath }}" alt="{{$imagePath}}"> --}}
                                            @endif
                                            <style> 
                                                .profile_image {
                                                    width:50px;
                                                    height: 50px;
                                                }
                                            </style>
                                        </td>
                                        <td>
                                            <a href="{{ route('users.show', $user->id) }}">View Profile</a>
                                        </td>
                                    </tr>
                                @endforeach
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        @foreach ($errors->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
