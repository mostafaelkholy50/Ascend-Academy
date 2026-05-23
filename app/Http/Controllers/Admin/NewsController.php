<?php

namespace App\Http\Controllers\Admin;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\NewsService;
use App\Http\Requests\Admin\StoreNewsRequest;
use App\Http\Requests\Admin\UpdateNewsRequest;

class NewsController extends Controller
{
    protected $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    public function index(Request $request)
    {
        $news = $this->newsService->getIndexData($request);
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(StoreNewsRequest $request)
    {
        try {
            $this->newsService->storeNews(
                $request->validated(),
                $request->file('image')
            );

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

    public function update(UpdateNewsRequest $request, News $news)
    {
        $this->newsService->updateNews(
            $news,
            $request->validated(),
            $request->file('image')
        );

        return back()->with('success', 'News updated successfully.');
    }

    public function destroy(News $news)
    {
        $this->newsService->deleteNews($news);
        return redirect()->route('admin.news.index')
            ->with('success', 'News deleted successfully.');
    }
}
