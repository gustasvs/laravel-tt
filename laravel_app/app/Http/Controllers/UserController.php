<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class UserController extends Controller
{
    public function get_users()
{
    // if (Auth::check()) {
    //     $user = Auth::user();
    //     $token = $user->createToken('token-name')->plainTextToken;
        
    //     $response = Http::withHeaders([
    //         'Authorization' => 'Bearer ' . $token,
    //     ])->get('http://localhost/api/get_users');
        
    //     if ($response->successful()) {
            $users = User::all();
            return response()->json($users);
            // return 
    //     } else {
    //         return response()->json(['error' => 'Failed to retrieve users'], $response->status());
    //     }
    // }

    // return response()->json(['error' => 'Unauthorized'], 401);
}

    public function show($id) {
        // Retrieve the user with the given ID from the database
        $user = User::findOrFail($id);

        // Redirect to the user's profile page or any other desired page
        return redirect()->route('users.profile', $user);
        
    }
    public function get_user($id) {
        $user = User::findOrFail($id);
        return response()->json($user);
    }
    public function get_auth_user() {

        if (Auth::check()) {
            $authenticatedUser = auth()->user();
            $tmp = new User();
            $tmp->name = $authenticatedUser->name;
            $tmp->email = $authenticatedUser->email;
            $tmp->password = $authenticatedUser->password;
            $tmp->role = $authenticatedUser->role;
            $tmp->profile_picture_path = $authenticatedUser->profile_picture_path;
            return response()->json($tmp);
        } else {
            return response()->json(['error' => 'User is not authenticated'], 401);
            $anonymousUser = new User();
            $anonymousUser->name = 'Empty';
            $anonymousUser->email = 'empty@email.com';
            return response()->json($anonymousUser);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }
    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }

    public function get_user_token()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'User is not authenticated'], 401);
        }
    }

    public function profile(User $user)
    {
        return view('users.profile', ['user' => $user]);
    }
    public function change_profile_picture(Request $request, string $id)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $filename = uniqid() . '.' . $image->getClientOriginalExtension();

            $path = $image->storeAs('public/images', $filename);

            $user = User::findOrFail($id);
            $user->profile_picture_path = $filename;
            $user->save();

            return redirect()->back()->with('status', 'Image uploaded successfully.');
        }

        return redirect()->back()->with('error', 'Failed to upload image.');
    }
    public function change_name(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        if ($request->name)
            $user->name = $request->name;
        $user->save();

        return response()->json('nomainits');
    }
    public function api_change_profile_picture(Request $request, string $id)
    {
        // $user = User::findOrFail($id);
        // return response()->json($user);

        $user = User::findOrFail($id);
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $filename = uniqid() . '.' . $image->getClientOriginalExtension();

            $path = $image->storeAs('public/images', $filename);

            // $user = auth()->user();
            $user->profile_picture_path = $filename;
            $user->save();
            
            return response()->json(['message' => 'Profile picture updated successfully'], 200);
        }
        return response()->json(['error' => 'Failed to upload image'], 400);
    }
    public function get_user_images() {
        $user = auth()->user();
        $images = $user->images()->with('user')->get();
        return response()->json($images);
    }
    public function get_some_user_images(string $id) {
        $user = User::findOrFail($id);
        $images = $user->images;
        return response()->json($images);
    }
    public function api_delete(string $id) {
        if (auth()->user()->role === 'admin') {
        $user = User::findOrFail($id);
        $user->delete();
        }
        return response()->json(['message' => 'Izdevas izdzest profilu'], 200);
    }
}
