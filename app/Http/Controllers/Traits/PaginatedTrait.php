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
        ?array $filter,
        ?array $searchFields,
        int $limit = 25,
        int $page = 1,
        array $append = []
    ) {
        if ($search !== null && $search != "") {
            foreach ($searchFields as $key => $searchField) {
                $builder = ($key == 0) ? $builder->where($searchField, 'like', "%$search%") : $builder->orWhere($searchField, 'like', "%$search%", );
            }
        }

        if (!empty($filter)) {
            $config = $filter[0];

            foreach ($filter as $config) {
                $builder = $builder->where($config['column'], $config['operator'], $config['value']);
            }
        }

        $builder = (null !== $sort) ? $builder->orderBy($sort['column'], $sort['order']) : $builder->orderBy('created_at', 'desc');

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
