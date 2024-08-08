<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Book};
use Illuminate\Http\Response;
use App\Http\Requests\{CreateBookRequest, UpdateBookRequest};
use App\Http\Controllers\Traits\PaginatedTrait;
use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Storage;

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
        $limit = $request->has('first') ? $request->first : 10;
        $page = $request->has('page') ? $request->page : 1;
        $filters = $request->has('filter') ? $request->filter : null;
        $search = $request->has('search') ? $request->search : null;
        $sort = $request->has('sort') && count($request->sort) > 0 ? $request->sort[0] : null;

        $searchFields = Book::$searchable;
        $queryBuilder = Book::with('authors:id,name,about');

        return $this->getPaginatedCollection($queryBuilder, $search, $sort, $filters, $searchFields, $limit, $page, ['rating']);
    }

    /**
     * Store a newly created book.
     *
     * @bodyParam title string require name.
     * @bodyParam author_ids array<int> require author_ids.
     * @bodyParam description int require description.
     * @bodyParam image int require image_url.
     * @bodyParam publisher int require publisher.
     * @bodyParam published_date int require published_date.
     * @bodyParam book int require book_url.
     *
     * @authenticated
     * @response {"data":[{"id":"5","title":"Sci fi","description":"short word","book_url":"https://books.com/file.pdf","image_url":"image.png"}]}
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(CreateBookRequest $request)
    {
        abort_if(!$request->user()->hasRole('admin'), Response::HTTP_FORBIDDEN, 'Permission denial!');

        $book = Book::create($this->getBookAndImageUrl($request));
        $book->authors()->attach($request->only('author_ids')['author_ids']);

        return (new BookResource($book->load('authors')))->additional([
            'status' => Response::HTTP_CREATED,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Book $book
     *
     * @authenticated
     * @response {"data":[{"id":"5","title":"Sci fi","description":"short word","book_url":"https://books.com/file.pdf","image_url":"image.png"}]}
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function show(Book $book)
    {
        return (new BookResource($book->load('authors')))->additional([
            'status' => Response::HTTP_OK,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @bodyParam title string require name.
     * @bodyParam author_ids array<int> require author_ids.
     * @bodyParam description int require description.
     * @bodyParam image int require image_url.
     * @bodyParam publisher int require publisher.
     * @bodyParam published_date int require published_date.
     * @bodyParam book int require book_url.
     *
     * @param Book $book
     *
     * @authenticated
     * @response {"data":[{"id":"5","title":"Sci fi","description":"short word","book_url":"https://books.com/file.pdf","image_url":"image.png"}]}
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        abort_if(!$request->user()->hasRole('admin'), Response::HTTP_FORBIDDEN, 'Permission denial!');

        $book->update($request->all());

        return (new BookResource($book->load('authors')))->additional([
            'status' => Response::HTTP_OK,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Book $book
     *
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Book $book)
    {
        abort_if(!$request->user()->hasRole('admin'), Response::HTTP_FORBIDDEN, 'Permission denial!');

        $book->delete();

        return response()->noContent(Response::HTTP_NO_CONTENT);
    }

    private function getBookAndImageUrl(CreateBookRequest $request)
    {
        try {
            $bookData = $request->except(['book', 'image', 'author_ids']);

            $title = str_replace(' ', '-', $request->only('title')['title']);
            $imgPath = "images/{$title}.{$request->file('image')->getClientOriginalExtension()}";
            $pdfPath = "books/{$title}.{$request->file('book')->getClientOriginalExtension()}";

            $googleDisk = Storage::disk('google');

            $googleDisk->put($imgPath, file_get_contents($request->file('image')->getRealPath()), 'public');
            $googleDisk->put($pdfPath, file_get_contents( $request->file('book')->getRealPath()), 'public');

            $bookData['image_url'] = $googleDisk->url($imgPath);
            $bookData['book_url'] = $googleDisk->url($pdfPath);

            return $bookData;
        } catch(\Exception $e) {
            throw $e->getMessage();
        }
    }
}
