<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{ReadingHistory};
use Illuminate\Http\Response;

class ReadingHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Add book to user reading history.
     *
     * @bodyParam user_id int require user_id.
     * @bodyParam book_id int require book_id.
     *
     * @authenticated
     * @response {"data":[{"id":"5","user_id":"5","book_id":"7"}]}
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        abort_if(!$request->user()->hasAnyRole(['admin', 'user']), Response::HTTP_FORBIDDEN, 'Permission denial!');

        $request->validate([
            'user_id' => 'required|int|exists:users,id',
            'book_id' => 'required|int|exists:books,id',
        ]);

        ReadingHistory::create($request->only(['user_id', 'book_id']));

        return response("Book added to your reading history", Response::HTTP_CREATED);
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
     *
     * @bodyParam is_read bool required is_read.
     *
     * @param ReadingHistory $reading_history
     *
     * @authenticated
     * @response {"data":[{"id":"5","user_id":"5","book_id":"7"}]}
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, ReadingHistory $reading_history)
    {
        abort_if(!$request->user()->hasAnyRole(['admin', 'user']), Response::HTTP_FORBIDDEN, 'Permission denial!');

        $request->validate([
            'is_read' => 'required|boolean',
        ]);

        abort_if($request->user()->id !== $reading_history->user_id, Response::HTTP_FORBIDDEN, 'Not allowed to modify this content');

        $reading_history->update($request->only('is_read'));

        return response("Book history updated to read.", Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
