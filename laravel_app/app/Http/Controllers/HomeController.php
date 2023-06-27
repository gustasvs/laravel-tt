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

        // if (!auth()->user()->role === 'admin') {
        if (true) {
            $validatedData = $request->validate([
                'role' => 'nullable|in:user,admin'
            ]);
    
            $user->role = $validatedData['role'] ?? 'user'; // Assign 'user' role if no role is provided
            $user->role = $user->role === 'admin' ? 'user' : 'admin';
            $user->save(); 
        } 
    }
    public function register(Request $request)
    {
        // // Process user registration data

        // // Upload and store the image
        // if ($request->hasFile('image')) {
        //     $image = $request->file('image');
        //     $filename = $image->getClientOriginalName();
        //     $path = $image->storeAs('images', $filename);
            
        //     // Store the image information in the images table
        //     $user->images()->create([
        //         'filename' => $filename,
        //         'path' => $path,
        //     ]);
        // }

        // // Redirect or perform other actions
            // $request->validate([
        //     'image' => 'required|image|max:2048',
        // ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // Generate a unique filename for the image
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();

            // Move the uploaded image to the storage directory
            $path = $image->storeAs('public/images', $filename);

            // Save the image details to the database
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

        // Create a log data array
        $logData = [
            'timestamp' => now(),
            'error' => $error,
            'user' => $userName,
        ];

        // Convert the log data to JSON
        $logJson = json_encode($logData);

        // Path to the log file
        $filePath = storage_path('logs.txt');

        // Append the log entry to the file
        file_put_contents($filePath, $logJson . PHP_EOL, FILE_APPEND);

        // Return a response indicating success
        return response()->json(['message' => 'Log saved successfully']);
    }
    public function get_logs()
    {
        // Path to the log file
        $filePath = storage_path('logs.txt');

        // Read the contents of the log file
        $logContents = file_get_contents($filePath);

        // Convert the log contents to an array of log entries
        $logEntries = explode(PHP_EOL, $logContents);

        // Remove empty log entries
        $logEntries = array_filter($logEntries);

        // Parse each log entry as JSON and add it to the logs array
        $logs = [];
        foreach ($logEntries as $logEntry) {
            $logData = json_decode($logEntry, true);
            $logs[] = $logData;
        }

        // Return the logs as a JSON response
        return response()->json($logs);
    }
    public function delete_logs()
    {
        // Path to the log file
        $filePath = storage_path('logs.txt');

        file_put_contents($filePath, '');

        // Return a response indicating success
        return response()->json(['message' => 'Logs deleted successfully']);
    }

}
