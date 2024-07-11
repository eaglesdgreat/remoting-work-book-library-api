<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\{Book};
use App\Http\Resources\BookResource;

class AddBookRatingController extends Controller
{
    /**
     * Add ratings to book.
     *
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        abort_if(!$request->user()->hasAnyRole(['admin', 'user']), Response::HTTP_FORBIDDEN, 'Permission denial!');

        $request->validate([
            'id' => 'required|int|exists:books',
            'user_id' => 'required|int|exists:users,id',
            'rating' => 'required|int',
        ]);

        $book = Book::findOrFail($request->id);

        $current_ratings = $book->ratings;

        if (in_array($request->user_id, array_column($current_ratings, 'user_id'))) {
            foreach($current_ratings as &$item) {
                if ($item['user_id'] === $request->user_id) {
                    $item['rating'] = $request->rating;
                    break;
                }
            }
        } else {
            array_push($current_ratings, ['rating' => $request->rating, 'user_id' => $request->user_id]);
        }

        $book->ratings = $current_ratings;
        $book->save();

        return (new BookResource($book))->additional([
            'status' => Response::HTTP_CREATED,
        ]);
    }
}
