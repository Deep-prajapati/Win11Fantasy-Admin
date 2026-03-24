<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileHelper
{
    /**
     * Upload a file to the given path in public storage.
     *
     * @param UploadedFile $file The file to upload
     * @param string $path The storage path inside 'public'
     * @return string|null The file path or null on failure
     */
    public static function uploadFile(UploadedFile $file, string $path)
    {
        try {
            // Ensure the directory exists
            $destinationPath = public_path($path);
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true); // Create directory with permissions
            }

            // Generate a unique filename
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Move the file to the public directory
            $file->move($destinationPath, $fileName);

            // Return the relative file path (accessible via URL)
            return $path . '/' . $fileName;
        } catch (\Exception $e) {
            return null; // Return null if an error occurs
        }
    }

    /**
     * Delete a file from the public directory.
     *
     * @param string $filePath The file path to delete (relative to public/)
     * @return bool True if deleted, false otherwise
     */
    public static function deleteFile(string $filePath)
    {
        try {
            $fullPath = public_path($filePath);

            if (file_exists($fullPath)) {
                return unlink($fullPath); // Delete the file
            }

            return false; // File doesn't exist
        } catch (\Exception $e) {
            return false; // Return false if an error occurs
        }
    }
    public static function uploadStorageFile(UploadedFile $file, string $path)
    {
        try {
            // Ensure the directory exists
            Storage::disk('public')->makeDirectory($path);

            // Generate a unique filename
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Store the file in the 'public' disk
            $filePath = $file->storeAs($path, $fileName, 'public');

            return $filePath; // Return stored file path
        } catch (\Exception $e) {
            return null; // Return null if an error occurs
        }
    }

    /**
     * Delete a file from public storage.
     *
     * @param string $filePath The file path to delete
     * @return bool True if deleted, false otherwise
     */
    public static function deleteStorageFile(string $filePath)
    {
        try {
            // Ensure the file exists before deleting
            if (Storage::disk('public')->exists($filePath)) {
                return Storage::disk('public')->delete($filePath);
            }
            return false; // File doesn't exist
        } catch (\Exception $e) {
            return false; // Return false if an error occurs
        }
    }
}
