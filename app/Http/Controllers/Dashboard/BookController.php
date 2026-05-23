<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    protected $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    private function authorizeView()
    {
        $user = auth()->user();
        if ($user->hasRole('SuperAdmin')) {
            return;
        }
        if ($user->hasAnyRole(['Student', 'Teacher', 'Parent'])) {
            return;
        }
        if ($user->can('view books') || $user->can('manage books')) {
            return;
        }
        abort(403, 'Unauthorized access.');
    }

    private function authorizeManage()
    {
        $user = auth()->user();
        if ($user->hasRole('SuperAdmin') || $user->can('manage books')) {
            return;
        }
        abort(403, 'Unauthorized access.');
    }

    public function index(Request $request)
    {
        $this->authorizeView();
        $books = $this->bookService->getIndexData($request);
        return view('books.index', compact('books'));
    }

    public function show(Book $book)
    {
        $this->authorizeView();
        
        // Retrieve all other active books for the sidebar quick switcher
        $otherBooks = Book::active()
            ->where('id', '!=', $book->id)
            ->get();

        return view('books.show', compact('book', 'otherBooks'));
    }

    public function stream(Book $book)
    {
        $this->authorizeView();

        if (!$book->file_path || !Storage::disk('local')->exists($book->file_path)) {
            abort(404, 'Book PDF file not found.');
        }

        $absolutePath = Storage::disk('local')->path($book->file_path);

        return response()->file($absolutePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($book->file_path) . '"'
        ]);
    }

    public function download(Book $book)
    {
        $this->authorizeView();

        if (!$book->file_path || !Storage::disk('local')->exists($book->file_path)) {
            abort(404, 'Book PDF file not found.');
        }

        $absolutePath = Storage::disk('local')->path($book->file_path);

        return response()->download($absolutePath, basename($book->file_path));
    }

    public function create()
    {
        $this->authorizeManage();
        return view('books.create');
    }

    public function store(Request $request)
    {
        $this->authorizeManage();

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pdf_file' => 'required|file|mimes:pdf|max:1024000', // max 1GB for large files
            'cover_image' => 'nullable|image|max:4096',
            'is_active' => 'nullable',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $this->bookService->storeBook(
                $request->only(['title', 'description', 'is_active']),
                $request->file('pdf_file'),
                $request->file('cover_image')
            );

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('books.index'),
                    'message' => 'Book created successfully.'
                ]);
            }

            return redirect()->route('books.index')
                ->with('success', 'Book created successfully.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save book: ' . $e->getMessage()
                ], 422);
            }
            return back()->withInput()->with('error', 'Failed to save book: ' . $e->getMessage());
        }
    }

    public function edit(Book $book)
    {
        $this->authorizeManage();
        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $this->authorizeManage();

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pdf_file' => 'nullable|file|mimes:pdf|max:1024000',
            'cover_image' => 'nullable|image|max:4096',
            'is_active' => 'nullable',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $this->bookService->updateBook(
                $book,
                $request->only(['title', 'description', 'is_active']),
                $request->file('pdf_file'),
                $request->file('cover_image')
            );

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('books.index'),
                    'message' => 'Book updated successfully.'
                ]);
            }

            return redirect()->route('books.index')
                ->with('success', 'Book updated successfully.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update book: ' . $e->getMessage()
                ], 422);
            }
            return back()->withInput()->with('error', 'Failed to update book: ' . $e->getMessage());
        }
    }

    public function destroy(Book $book)
    {
        $this->authorizeManage();

        try {
            $this->bookService->deleteBook($book);
            return redirect()->route('books.index')
                ->with('success', 'Book deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete book: ' . $e->getMessage());
        }
    }
}
