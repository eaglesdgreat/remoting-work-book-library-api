<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Http\Response;
use App\Http\Requests\CreateBookRequest;
use App\Http\Controllers\Traits\PaginatedTrait;
use App\Http\Resources\BookResource;

class BookController extends Controller
{
    use PaginatedTrait;

    /**
     * Display list of books.
     *
     * @authenticated
     * @response {"data":[{"id":"5","title":"Sci fi","description":"short word","book_url":"https://books.com"}]}
     *
     *  @return array<\Illuminate\Http\Response>
     */
    public function index(Request $request)
    {
        $limit = $request->first;
        $page = $request->page ?? 1;
        $filter = $request->filter ?? null;
        $search = $request->search ?? null;
        $sort = $request->has('sort') && count($request->sort) > 0 ? $request->sort[0] : null;

        abort_if(!$request->user()->hasAnyRole(['admin', 'user']), Response::HTTP_FORBIDDEN, 'Permission denial!');

        $searchFields = Book::$searchable;
        $queryBuilder = Book::with('author:id,name,about');

        return $this->getPaginatedCollection($queryBuilder, $limit, $page, $search, $sort, $filter, $searchFields);
    }

    /**
     * Store a newly created book.
     *
     * @bodyParam title string require name.
     * @bodyParam author_id int require author_id.
     * @bodyParam description int require description.
     * @bodyParam image int require image_url.
     * @bodyParam publisher int require publisher.
     * @bodyParam published_date int require published_date.
     * @bodyParam book int require book_url.
     *
     * @authenticated
     * @response {"data":[{"id":"5","title":"Sci fi","description":"short word","book_url":"https://books.com","image_url":"image.png"}]}
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(CreateBookRequest $request)
    {
        abort_if(!$request->user()->hasRole('admin'), Response::HTTP_FORBIDDEN, 'Permission denial!');

        $book = Book::create($request->safe()->except(['book', 'image']));

        return (new BookResource($book->load('author')))->additional([
            'status' => Response::HTTP_CREATED,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
