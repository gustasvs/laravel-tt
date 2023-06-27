<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;

class CatController extends Controller
{
    public function register(Request $request) {
        $validation = $request->validate([
            'name' => ['required', 'min:3', 'max:12'], // Rule::unique('users', 'name')
            // ,'email' => 'required',
            // 'password' => 'required'
        ]);
        
        $input = $request->input();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        // dd($user);
        // return response()->json($user);
        auth()->login($user);
        return redirect('/Sample');
        // return view("Register");
    }
    public function logout() {
        auth()->logout();
        return redirect('/Sample');
    }
    public function getUsers()
    {
        $users = User::all();
        return view('your-view-file', compact('users'));
    }
}
