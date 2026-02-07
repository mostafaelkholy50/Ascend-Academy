<?php

namespace App\Http\Controllers\Admin;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $news = $query->latest()->paginate(15);

        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'nullable|boolean',
        ]);

        try {
            $data = $request->only(['title', 'description']);
            $data['slug'] = Str::slug($request->title) . "-" . Str::random(5);
            $data['is_published'] = $request->has('is_published') ? 1 : 0;

            // Handle image upload
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('news', 'public');
            }

            if ($data['is_published']) {
                $data['published_at'] = now();
            }

            $news = News::create($data);

            return redirect()->route('admin.news.index')
                ->with('success', 'News created successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create news: ' . $e->getMessage());
        }
    }

    public function show(News $news)
    {
        return view('admin.news.show', compact('news'));
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'nullable|boolean',
        ]);

        $data = $request->only(['title', 'description']);

        // Only regenerate slug if title has changed
        if ($request->title !== $news->title) {
            $data['slug'] = Str::slug($request->title) . "-" . Str::random(5);
        }

        $data['is_published'] = $request->has('is_published') ? 1 : 0;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($news->image) {
                \Storage::disk('public')->delete($news->image);
            }
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        if ($data['is_published'] && !$news->published_at) {
            $data['published_at'] = now();
        }

        $news->update($data);

        return back()->with('success', 'News updated successfully.');
    }

    public function destroy(News $news)
    {
        // Delete image if exists
        if ($news->image) {
            \Storage::disk('public')->delete($news->image);
        }

        $news->delete();
        return redirect()->route('admin.news.index')
            ->with('success', 'News deleted successfully.');
    }
}
