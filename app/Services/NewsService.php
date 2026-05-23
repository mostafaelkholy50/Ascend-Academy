<?php

namespace App\Services;

use App\Repositories\NewsRepository;
use App\Filters\NewsFilter;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class NewsService
{
    protected $repository;
    protected $filter;

    public function __construct(NewsRepository $repository, NewsFilter $filter)
    {
        $this->repository = $repository;
        $this->filter = $filter;
    }

    public function getIndexData(Request $request, int $perPage = 15)
    {
        $query = $this->repository->getNewsQuery()->latest();
        $query = $this->filter->apply($query, $request);

        return $query->paginate($perPage);
    }

    public function storeNews(array $data, $image = null)
    {
        $data['slug'] = Str::slug($data['title']) . "-" . Str::random(5);
        $data['is_published'] = isset($data['is_published']) ? 1 : 0;

        if ($image) {
            $data['image'] = $image->store('news', 'public');
        }

        if ($data['is_published']) {
            $data['published_at'] = now();
        }

        return $this->repository->create($data);
    }

    public function updateNews(News $news, array $data, $image = null)
    {
        // Only regenerate slug if title has changed
        if (isset($data['title']) && $data['title'] !== $news->title) {
            $data['slug'] = Str::slug($data['title']) . "-" . Str::random(5);
        }

        $data['is_published'] = isset($data['is_published']) ? 1 : 0;

        if ($data['is_published'] && !$news->published_at) {
            $data['published_at'] = now();
        }

        if ($image) {
            $oldImage = $news->image;
            $data['image'] = $image->store('news', 'public');
            
            $updated = $this->repository->update($news, $data);
            
            if ($updated && $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
            return $updated;
        }

        return $this->repository->update($news, $data);
    }

    public function deleteNews(News $news)
    {
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        return $this->repository->delete($news);
    }
}
