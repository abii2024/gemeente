<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,jpg,png,gif,pdf|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(40) . '.' . $extension;
        $path = 'temp-uploads/' . $filename;

        // For images, resize and optimize
        if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])) {
            $image = Image::read($file->getRealPath());
            
            // Resize if larger than 1920px width or 1080px height while maintaining aspect ratio
            if ($image->width() > 1920 || $image->height() > 1080) {
                $image->scale(width: 1920, height: 1080);
            }
            
            // Save the optimized image
            $imageData = $image->encode(quality: 85);
            Storage::disk('public')->put($path, $imageData);
            
            // Create thumbnail (300x300)
            $thumbnailPath = 'temp-uploads/thumbnails/' . $filename;
            $thumbnail = $image->cover(300, 300);
            $thumbnailData = $thumbnail->encode(quality: 75);
            Storage::disk('public')->put($thumbnailPath, $thumbnailData);
        } else {
            // For non-images (PDFs), just store normally
            Storage::disk('public')->putFileAs('temp-uploads', $file, $filename);
            $thumbnailPath = null;
        }

        return response()->json([
            'success' => true,
            'path' => $path,
            'thumbnail_path' => $thumbnailPath,
            'original_name' => $originalName,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'url' => Storage::disk('public')->url($path),
            'thumbnail_url' => $thumbnailPath ? Storage::disk('public')->url($thumbnailPath) : null,
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $path = $request->input('path');
        
        // Security check - only allow deletion of temp uploads
        if (!str_starts_with($path, 'temp-uploads/')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            
            // Also delete thumbnail if exists
            $thumbnailPath = str_replace('temp-uploads/', 'temp-uploads/thumbnails/', $path);
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
        }

        return response()->json(['success' => true]);
    }
}