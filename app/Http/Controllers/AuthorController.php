<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Author;
use App\Http\Resources\AuthorResource;
use Illuminate\Http\Response;
use App\Http\Controllers\Traits\PaginatedTrait;

/**
 * @group Authors
 *
 * API endpoints for managing authors
 */
class AuthorController extends Controller
{
    use PaginatedTrait;

    /**
     * Display list of authors.
     *
     * @authenticated
     * @response {"data":[{"id":"5","name":"Felix","summary":"short word","about":"long word","date_birthed":"23/12/1876","date_died":"null"}]}
     *
     * @return array<\Illuminate\Http\Response>
     */
    public function index(Request $request)
    {
        $limit = $request->first;
        $page = $request->page ?? 1;

        abort_if(!$request->user()->hasRole('admin'), Response::HTTP_FORBIDDEN, 'Permission denial!');

        return $this->getPaginatedCollection(Author::select(), $limit, $page, null, null, [], []);
    }

    /**
     * Store a newly created author.
     *
     * @bodyParam name string required name.
     * @bodyParam about string required about.
     *
     * @authenticated
     * @response {"data":{"name":"Gust Olson","about":"long word","summary":"null",date_birthed:"null",date_died:"null"}, "status":"201"}
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'about' => 'required',
        ]);

        abort_if(!$request->user()->hasRole('admin'), Response::HTTP_FORBIDDEN, 'Permission denial!');

        return (new AuthorResource(Author::create($request->all())))->additional([
            'status' => Response::HTTP_CREATED,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Author $author
     *
     * @unauthenticated
     * @response {"data":{"name":"Gust Olson","about":"long word","summary":"null",date_birthed:"null",date_died:"null"}, "status":"200"}
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request, Author $author)
    {
        abort_if(!$request->user()->hasAnyRole(['admin', 'user']), Response::HTTP_FORBIDDEN, 'Permission denial!');

        return (new AuthorResource($author->load('books')))->additional(([
            'status' => Response::HTTP_OK,
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @bodyParam name string required name.
     * @bodyParam about string required about.
     *
     * @param Author $author
     *
     * @authenticated
     * @response {"data":{"name":"Gust Olson","about":"long word","summary":"null",date_birthed:"null",date_died:"null"}, "status":"201"}
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Author $author)
    {
        $request->validate([
            'name' => 'required',
            'about' => 'required',
        ]);

        abort_if(!$request->user()->hasRole('admin'), Response::HTTP_FORBIDDEN, 'Permission denial!');

        $author->update($request->all());

        return (new AuthorResource($author))->additional(([
            'status' => Response::HTTP_OK,
        ]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Author $author
     *
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Author $author)
    {
        abort_if(!$request->user()->hasRole('admin'), Response::HTTP_FORBIDDEN, 'Permission denial!');

        $author->delete();

        return response()->noContent(Response::HTTP_NO_CONTENT);
    }
}
