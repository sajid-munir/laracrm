<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Rows per page on every list table.
     */
    protected const PER_PAGE = 10;

    /**
     * Paginate a query, moving a page number past the end back to the last page
     * (for example after deleting the only row on the last page).
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  Builder<TModel>  $query
     * @return LengthAwarePaginator<int, TModel>
     */
    protected function paginateWithinRange(Builder $query, Request $request): LengthAwarePaginator
    {
        $total = (clone $query)->count();
        $lastPage = max(1, (int) ceil($total / static::PER_PAGE));
        $page = min(max(1, $request->integer('page', 1)), $lastPage);

        return $query->paginate(static::PER_PAGE, ['*'], 'page', $page);
    }

    /**
     * Pagination block the list tables' JavaScript reads.
     *
     * @param  LengthAwarePaginator<int, mixed>  $paginator
     * @return array{page: int, per_page: int, total: int, total_pages: int}
     */
    protected function paginationMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'total_pages' => max(1, $paginator->lastPage()),
        ];
    }
}
