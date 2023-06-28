<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users = User::all();
        return view('home', compact('users'));
    }
    public function guest_index()
    {
        return view('guest.home');
        $users = User::all();
        
        return view('guest.home', compact('users'));
    }
    public function redirect_home() {
        $redirectTo = '/home';
        return response()->json(['redirectTo' => $redirectTo]);
    }
    public function destroy($user)
    {
        $user = User::findOrFail($user);
        $user->delete();

        return redirect()->back()->with('success', 'Sekmgi izdzests lietotajs.');
    }
    public function update_username(Request $request, $user)
    {
        $user = User::findOrFail($user);
        $user->name = $request->input('name');
        $user->save();

        return redirect()->back()->with('success', 'Sekmigi atjaunots lietotaja vards.');
    }
    public function update_role(Request $request, $user)
    {
        $user = User::findOrFail($user);

        // if (!auth()->user()->role === 'admin') {
        if (true) {
            // $validatedData = $request->validate([
            //     'role' => 'nullable|in:user,admin'
            // ]);
    
    
            $user->role = $validatedData['role'] ?? 'user'; // Assign 'user' role if no role is provided
            $user->role = $user->role === 'admin' ? 'user' : 'admin';
            $user->save();
    
            return redirect()->back()->with('success', 'User mode updated successfully.');   
        } else {
            return redirect()->back()->withErrors(['Unauthorized action.'])
            ->withInput()
            ->header('Refresh', '5;url=' . redirect()->back()->getTargetUrl());
        }
    }
    public function api_update_role(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        // Log::info('This is an informational log message.');
        
        if (!auth()->user()->role === 'admin') {
        // if (true) {
            // $validatedData = $request->validate([
            //     'role' => 'nullable|in:user,admin'
            // ]);
            // \Log::info($user->role);
            if ($user->role == 'admin') {
                $user->role = 'user';
            } else {
                $user->role = 'admin';
            }
            // \Log::info($user->role);
            $user->save();

        } 
        // response()->json(['message' => 'new role']);
    }
    public function register(Request $request)
    {

        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $filename = uniqid() . '.' . $image->getClientOriginalExtension();

            $path = $image->storeAs('public/images', $filename);

            $user = auth()->user();
            $user->images()->create([
                'filename' => $filename,
                'path' => $path,
            ]);

            return redirect()->back()->with('status', 'Image uploaded successfully.');
        }

        return redirect()->back()->with('error', 'Failed to upload image.');
    }
    public function save_log(Request $request) {
        $error = $request->input('error');
        $userName = $request->input('userName');

        $logData = [
            'timestamp' => now(),
            'error' => $error,
            'user' => $userName,
        ];

        $logJson = json_encode($logData);
        $filePath = storage_path('logs.txt');
        file_put_contents($filePath, $logJson . PHP_EOL, FILE_APPEND);
        return response()->json(['message' => 'Saglabāts ieraksts žurnālā.']);
    }
    public function get_logs()
    {
        $filePath = storage_path('logs.txt');
        $logContents = file_get_contents($filePath);
        $logEntries = explode(PHP_EOL, $logContents);
        $logEntries = array_filter($logEntries);

        $logs = [];
        foreach ($logEntries as $logEntry) {
            $logData = json_decode($logEntry, true);
            $logs[] = $logData;
        }

        return response()->json($logs);
    }
    public function delete_logs()
    {
        $filePath = storage_path('logs.txt');
        $logs = file($filePath);
        $deletedCount = count($logs);
        
        file_put_contents($filePath, '');
        
        return response()->json(['message' => 'Izdzēsti sistēmas ieraksti', 'skaits' => $deletedCount]);
    }

    public function logs_half()
    {
        $filePath = storage_path('logs.txt');
        $logs = file($filePath);
        $deletedCount = 0;
        
        if (count($logs) > 50) {
            $newestLogs = array_slice($logs, -50);
            $deletedCount = count($logs) - 50;
            
            file_put_contents($filePath, '');
            file_put_contents($filePath, implode($newestLogs));
        }
        
        return response()->json(['skaits' => $deletedCount]);
    }


}
