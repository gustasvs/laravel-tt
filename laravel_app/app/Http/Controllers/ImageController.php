<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $images = Image::with('user')->get();
    return response()->json($images);
}

    public function update_description(Request $request, string $id)
    {
        $image = Image::findOrFail($id); // Assuming 'Image' is your model name

        $user = auth()->user();
        if ($user->id === $image->user_id || $user->role === 'admin') {
            $image->apraksts = $request->input('description');
            $image->save();

            return redirect()->back()->with('status', 'Description updated successfully.');
        }

        return redirect()->back()->with('error', 'You are not authorized to update this image description.');
    }
    public function api_update_description(Request $request, string $id) {
        $image = Image::findOrFail($id);

        $user = auth()->user();
        if ($user->id === $image->user_id || $user->role === 'admin') {
            $image->apraksts = $request->input('description');
            $image->save();

            return response()->json(['message' => 'Izdevas nomainit description'], 200);
        }
        return response()->json(['error' => 'Failed to change description'], 400);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    public function get_images() {
        // $images = Image::all();
        $images = Image::with('user')->get();
        return response()->json($images);

        // }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
                'apraksts' => $request->input('description'),
            ]);

            return redirect()->back()->with('status', 'Image uploaded successfully.');
        }

        return redirect()->back()->with('error', 'Failed to upload image.');
    }
    public function api_store(Request $request)
    {
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
                'apraksts' => $request->input('description'),
            ]);
            return response()->json($image);
            // return redirect()->back()->with('status', 'Image uploaded successfully.');
        }

        return response()->json(['error' => 'Failed to upload image.'], 400);
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $image = Image::findOrFail($id);
        dd($image);
        return view('images.show', compact('image'));
    }
    public function get_image($id) {
        $image = Image::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();
        $image = Image::findOrFail($id);
        if ($user->id == $image->user_id || $user->role === 'admin') {
            // Delete the image file from storage
            $filePath = storage_path('public/images/' . $image->filename);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $image->delete();
            return redirect()->back()->with('status', 'Image deleted successfully.');
        }
        return redirect()->back()->with('error', 'Nav privilegijas izdzest.');
    }
    public function api_destroy(string $id)
    {
        $user = auth()->user();
        $image = Image::findOrFail($id);
        if ($user->id == $image->user_id || $user->role === 'admin') {
            // Delete the image file from storage
            $filePath = storage_path('public/images/' . $image->filename);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $image->delete();
            // return redirect()->back()->with('status', 'Image deleted successfully.');
        }
        // return redirect()->back()->with('error', 'Nav privilegijas izdzest.');
    }

    public function like(string $id) {
        $image = Image::findOrFail($id);
        if (!auth()->user()->likes()->where('image_id', $id)->exists()) {
            auth()->user()->likes()->attach($id);
            $image->increment('likes');
        }

        return redirect()->back()->with('status', 'Image liked successfully.');
    }

    public function api_like(string $id) {

        $user = auth()->user();
        $image = Image::findOrFail($id);

        if (!auth()->user()->likes()->where('image_id', $id)->exists()) {
            auth()->user()->likes()->attach($id);
            $image->increment('likes');
            return response()->json($image->likes);
        }
        return response()->json($image->likes);
    }
    public function api_view(string $id) {

        $user = auth()->user();
        $image = Image::findOrFail($id);
        if ($image->user->id != $user->id)
            $image->increment('views');
        return response()->json($image->views);
    }
    public function api_dislike(string $id) {
        $image = Image::findOrFail($id);
        if (auth()->user()->likes()->where('image_id', $id)->exists()) {
            auth()->user()->likes()->detach($id);
            $image->decrement('likes');
        }
        return response()->json($image->likes);
    }
    public function dislike(string $id) {
        $image = Image::findOrFail($id);
        if (auth()->user()->likes()->where('image_id', $id)->exists()) {
            auth()->user()->likes()->detach($id);
            $image->decrement('likes');
        }
        return redirect()->back()->with('status', 'Image disliked successfully.');
    }
}
