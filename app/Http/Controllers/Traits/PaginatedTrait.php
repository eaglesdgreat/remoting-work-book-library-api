<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Response;

trait PaginatedTrait
{
    public function getPaginatedCollection(
        ?Builder $builder,
        ?string $search,
        ?array $sort,
        ?array $filters,
        ?array $searchFields,
        int $limit = 25,
        int $page = 1,
        array $append = []
    ) {
        if ($search !== null && $search != "") {
            foreach ($searchFields as $key => $searchField) {
                $builder = ($key == 0) ? $builder->where($searchField, 'like', "%$search%") : $builder->orWhereHas('authors', function($query) use ($searchField, $search) {
                    $query->where($searchField, 'like', "%$search%");
                });
            }
        }

        if (!empty($filters)) {
            foreach ($filters as $filter) {
                $builder = $builder->where($filter['column'], $filter['operator'], $filter['value']);
            }
        }

        if (null !== $sort) {
            if ($sort['column'] === 'name') {
                $builder = $builder->whereHas('authors', function($query) use ($sort) {
                    $query->orderBy($sort['column'], $sort['order']);
                });
            } else {
                $builder = $builder->orderBy($sort['column'], $sort['order']);
            }
        } else {
            $builder = $builder->orderBy('created_at', 'desc');
        }

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $paginatorInfo = $builder->paginate($limit, ["*"], 'page', $page);

        $builder = !empty($append) ? $builder->get()->append($append) : $builder->get();

        return [
            'data' => $builder,
            'paginatorInfo' => [
                'count' => $paginatorInfo->count(),
                'currentPage' => $paginatorInfo->currentPage(),
                'hasMorePages' => $paginatorInfo->lastPage() > $paginatorInfo->currentPage() ? true : false,
                'lastPage' => $paginatorInfo->lastPage(),
                'perPage' => $paginatorInfo->perPage(),
                'total' => $paginatorInfo->total(),
                'firstItem' => $paginatorInfo->firstItem(),
                'lastItem' => $paginatorInfo->lastItem(),
            ],
            'status' => Response::HTTP_OK
        ];
    }
}
