<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $booksSourceDir = base_path('Books');

        if (!file_exists($booksSourceDir) || !is_dir($booksSourceDir)) {
            // Check lowercase just in case
            $booksSourceDir = base_path('books');
            if (!file_exists($booksSourceDir) || !is_dir($booksSourceDir)) {
                return;
            }
        }

        // Ensure local private storage books directory exists
        if (!Storage::disk('local')->exists('books')) {
            Storage::disk('local')->makeDirectory('books');
        }

        $pdfFiles = glob($booksSourceDir . '/*.pdf');

        if (empty($pdfFiles)) {
            return;
        }
        foreach ($pdfFiles as $pdfPath) {
            $filename = basename($pdfPath);
            $targetRelativePath = 'books/' . $filename;
            $targetAbsolutePath = Storage::disk('local')->path($targetRelativePath);

            // Copy file to local private storage if not already there
            if (!file_exists($targetAbsolutePath)) {
                copy($pdfPath, $targetAbsolutePath);
            }

            // Create title from filename
            $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
            $cleanedTitle = str_replace(['_', '-'], ' ', $filenameWithoutExt);
            $cleanedTitle = preg_replace('/\s+/', ' ', $cleanedTitle);
            $cleanedTitle = trim($cleanedTitle);
            $cleanedTitle = ucwords($cleanedTitle);

            // Seed into DB
            Book::firstOrCreate(
                ['file_path' => $targetRelativePath],
                [
                    'title' => $cleanedTitle,
                    'description' => 'Academic book resource: ' . $cleanedTitle,
                    'is_active' => true,
                ]
            );
        }
    }
}
