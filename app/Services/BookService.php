<?php

namespace App\Services;

use App\Repositories\BookRepository;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookService
{
    protected $repository;

    public function __construct(BookRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getIndexData(Request $request, int $perPage = 12)
    {
        $user = auth()->user();
        
        // Non-administrative users (Students, Teachers, Parents) can only see active books
        $isStaff = $user->hasRole('SuperAdmin') || $user->can('manage books') || $user->can('view books');
        
        if ($isStaff) {
            $query = $this->repository->getBooksQuery()->latest();
        } else {
            $query = $this->repository->getActiveBooksQuery()->latest();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function storeBook(array $data, $pdfFile, $coverFile = null): Book
    {
        $data['is_active'] = isset($data['is_active']) ? (bool)$data['is_active'] : true;

        if ($pdfFile) {
            // Save to secure private local disk under books folder
            $data['file_path'] = $pdfFile->store('books', 'local');
        }

        if ($coverFile) {
            // Save to public disk so it can be served as an image directly
            $data['cover_image'] = $coverFile->store('books/covers', 'public');
            $this->copyCoverToPublicFolder($data['cover_image']);
        }

        return $this->repository->create($data);
    }

    public function updateBook(Book $book, array $data, $pdfFile = null, $coverFile = null): bool
    {
        $data['is_active'] = isset($data['is_active']) ? (bool)$data['is_active'] : false;

        if ($pdfFile) {
            // Delete old file
            if ($book->file_path && Storage::disk('local')->exists($book->file_path)) {
                Storage::disk('local')->delete($book->file_path);
            }
            $data['file_path'] = $pdfFile->store('books', 'local');
        }

        if ($coverFile) {
            // Delete old cover
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
                $this->deleteCoverFromPublicFolder($book->cover_image);
            }
            $data['cover_image'] = $coverFile->store('books/covers', 'public');
            $this->copyCoverToPublicFolder($data['cover_image']);
        }

        return $this->repository->update($book, $data);
    }

    public function deleteBook(Book $book): bool
    {
        // Delete PDF file
        if ($book->file_path && Storage::disk('local')->exists($book->file_path)) {
            Storage::disk('local')->delete($book->file_path);
        }

        // Delete cover file
        if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
            Storage::disk('public')->delete($book->cover_image);
            $this->deleteCoverFromPublicFolder($book->cover_image);
        }

        return $this->repository->delete($book);
    }

    /**
     * Duplicate cover to the real public/storage directory (backup for Windows/XAMPP non-symlink folders).
     */
    private function copyCoverToPublicFolder($coverPath)
    {
        try {
            $source = storage_path('app/public/' . $coverPath);
            $destination = public_path('storage/' . $coverPath);
            
            $dir = dirname($destination);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            if (file_exists($source)) {
                copy($source, $destination);
            }
        } catch (\Exception $e) {
            logger()->error('Failed to copy cover image to public folder: ' . $e->getMessage());
        }
    }

    /**
     * Delete cover from the real public/storage directory.
     */
    private function deleteCoverFromPublicFolder($coverPath)
    {
        try {
            $destination = public_path('storage/' . $coverPath);
            if (file_exists($destination)) {
                unlink($destination);
            }
        } catch (\Exception $e) {
            // Silently ignore delete failures
        }
    }
}

