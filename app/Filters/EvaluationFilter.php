<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class EvaluationFilter
{
    /**
     * Apply filters to the query.
     *
     * @param Builder $query
     * @param Request $request
     * @return Builder
     */
    public function apply(Builder $query, Request $request)
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('teacher', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->filled('date_from')) {
            $query->where('evaluation_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('evaluation_date', '<=', $request->date_to);
        }

        return $query;
    }
}
