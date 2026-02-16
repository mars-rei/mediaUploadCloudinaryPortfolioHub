<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Log;
class PhotoController extends Controller
{
    protected $cloudinary;

    public function __construct()
    {
        // Initialize Cloudinary with credentials from .env

        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key'    => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
            'url' => [
                'secure' => true
            ]
        ]);
    }

    public function index()
    {
        $photos = Photo::latest()->get();
        return view('photos.index', compact('photos'));
    }

    public function create()
    {
        return view('photos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|max:2048',
        ]);

        $file = $request->file('image');

        try {
            // Upload to Cloudinary using direct SDK
            $uploadResult = $this->cloudinary->uploadApi()->upload(
                $file->getRealPath(),
                [
                    'folder' => 'testing',
                    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET', 'testing')
                ]
            );

            // Create database record
            Photo::create([
                'title' => $request->title,
                'image_url' => $uploadResult['secure_url'],
                'image_public_id' => $uploadResult['public_id'],
            ]);

            return redirect()->route('photos.index')
                ->with('success', 'Photo uploaded successfully to Cloudinary!');

        } catch (\Exception $e) {
            // Detailed error message for debugging
            $errorMessage = 'Cloudinary Error: ' . $e->getMessage();
            
            // Log the full error for debugging
            Log::error('Cloudinary upload failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    public function destroy(Photo $photo)
    {
        try {
            // Delete from Cloudinary
            $this->cloudinary->uploadApi()->destroy($photo->image_public_id);
            
            // Delete from database
            $photo->delete();
            
            return redirect()->route('photos.index')
                ->with('success', 'Photo deleted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete photo: ' . $e->getMessage());
        }
    }
}