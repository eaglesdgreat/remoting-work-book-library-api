<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\{Review};
use App\Http\Controllers\Traits\PaginatedTrait;
use App\Http\Resources\ReviewResource;

class BookReviewController extends Controller
{
    use PaginatedTrait;

    /**
     * Display list of reviews.
     *
     * @authenticated
     * @response {"data":[{"id":"5","comment":"short word","user_id":"23","book_id":"5"}]}
     *
     * @return array<\Illuminate\Http\Response>
     */
    public function index(Request $request)
    {
        abort_if(!$request->user()->hasAnyRole(['admin', 'user']), Response::HTTP_FORBIDDEN, 'Permission denial!');

        $limit = $request->has('first') ? $request->first : 10;
        $page = $request->has('page') ? $request->page : 1;

        $queryBuilder = Review::where('user_id', $request->user_id)->where('book_id', $request->book_id);

        return $this->getPaginatedCollection($queryBuilder, null, null, [], [], $limit, $page, []);
    }

     /**
     * Store a newly created review.
     *
     * @bodyParam comment string require comment.
     * @bodyParam user_id int require user_id.
     * @bodyParam book_id int require book_id.
     *
     * @authenticated
     * @response {"data":[{"id":"5","comment":"short word","user_id":"5","book_id":"7"}]}
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        abort_if(!$request->user()->hasAnyRole(['admin', 'user']), Response::HTTP_FORBIDDEN, 'Permission denial!');

        $request->validate([
            'comment' => 'required|string',
            'user_id' => 'required|int|exists:users,id',
            'book_id' => 'required|int|exists:books,id',
        ]);

        $review = Review::create($request->all());

        return (new ReviewResource($review))->additional([
            'status' => Response::HTTP_CREATED,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Review $review
     *
     * @authenticated
     * @response {"data":[{"id":"5","comment":"short word","user_id":"5","book_id":"7"}]}
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request, Review $review)
    {
        abort_if(!$request->user()->hasAnyRole(['admin', 'user']), Response::HTTP_FORBIDDEN, 'Permission denial!');

        return (new ReviewResource($review))->additional([
            'status' => Response::HTTP_OK,
        ]);
    }

     /**
     * Update the specified resource in storage.
     *
     * @bodyParam comment string required comment.
     *
     * @param Review $review
     *
     * @authenticated
     * @response {"data":[{"id":"5","comment":"short word","user_id":"5","book_id":"7"}]}
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Review $review)
    {
        abort_if(!$request->user()->hasAnyRole(['admin', 'user']), Response::HTTP_FORBIDDEN, 'Permission denial!');

        $request->validate([
            'comment' => 'required|string',
        ]);

        $review->update($request->only('comment'));

        return (new ReviewResource($review))->additional([
            'status' => Response::HTTP_OK,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Review $review
     *
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Review $review)
    {
        abort_if(!$request->user()->hasRole('admin'), Response::HTTP_FORBIDDEN, 'Permission denial!');

        $review->delete();

        return response()->noContent(Response::HTTP_NO_CONTENT);
    }
}
