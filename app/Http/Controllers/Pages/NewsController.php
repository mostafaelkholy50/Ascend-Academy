<?php

namespace App\Http\Controllers\Pages;

use App\Models\News;
use App\Http\Controllers\Controller;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::published()
            ->latest('published_at')
            ->paginate(12);

        return view('pages.News', compact('news'));
    }

    public function show($slug)
    {
        $newsItem = News::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedNews = News::published()
            ->where('id', '!=', $newsItem->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('pages.news-detail', compact('newsItem', 'relatedNews'));
    }
}
