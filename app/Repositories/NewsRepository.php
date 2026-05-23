<?php

namespace App\Repositories;

use App\Models\News;
use Illuminate\Database\Eloquent\Builder;

class NewsRepository
{
    public function getNewsQuery(): Builder
    {
        return News::query();
    }

    public function findOrFail(int $id): News
    {
        return News::findOrFail($id);
    }

    public function create(array $data): News
    {
        return News::create($data);
    }

    public function update(News $news, array $data): bool
    {
        return $news->update($data);
    }

    public function delete(News $news): ?bool
    {
        return $news->delete();
    }
}
