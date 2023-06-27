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

            // Create a new instance of User model
            $tmp = new User();

            // Assign the attributes from the authenticated user to the new user
            // $tmp->id == $authenticatedUser->id;
            $tmp->name = $authenticatedUser->name;
            $tmp->email = $authenticatedUser->email;
            $tmp->password = $authenticatedUser->password;
            $tmp->role = $authenticatedUser->role;
            $tmp->profile_picture_path = $authenticatedUser->profile_picture_path;

            // Return the data of the new user
            return response()->json($tmp);
        } else {
            return response()->json(['error' => 'User is not authenticated'], 401);
            // Create a new instance of User model
            $anonymousUser = new User();

            // Set default values for specific attributes
            $anonymousUser->name = 'Empty';
            $anonymousUser->email = 'empty@email.com';
            // Set other default values for other attributes...

            // Return the new user object
            return response()->json($anonymousUser);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;

            // Debugging: Log the user ID and token
            \Log::info('User ID: ' . $user->name);
            \Log::info('Token: ' . $token);

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

            // Debugging: Log the user ID and token
            \Log::info('User ID: ' . $user->name);
            \Log::info('Token: ' . $token);

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

            // Debugging: Log the user ID and token
            // \Log::info('User ID: ' . $user->id);
            // \Log::info('Token: ' . $token);

            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'User is not authenticated'], 401);
        }
    }

    public function profile(User $user)
    {
        // Do any necessary processing to display the user's profile
        return view('users.profile', ['user' => $user]);
    }
    public function change_profile_picture(Request $request, string $id)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // Generate a unique filename for the image
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();

            // Move the uploaded image to the storage directory
            $path = $image->storeAs('public/images', $filename);

            // Save the image details to the database
            // $user = auth()->user();
            $user = User::findOrFail($id);
            $user->profile_picture_path = $filename;
            $user->save();

            return redirect()->back()->with('status', 'Image uploaded successfully.');
        }

        return redirect()->back()->with('error', 'Failed to upload image.');
    }
    public function api_change_profile_picture(Request $request, string $id)
    {
        // $user = User::findOrFail($id);
        // return response()->json($user);

        $user = User::findOrFail($id);
        \Log::info('changable id: ' . $id);
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // Generate a unique filename for the image
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();

            // Move the uploaded image to the storage directory
            $path = $image->storeAs('public/images', $filename);

            // Save the image details to the database
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
        // $images = $user->images->with('user')->get()
        $images = $user->images;
        \Log::info('images: ' . $images);
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
