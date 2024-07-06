<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;

/**
 * @group Users
 *
 * API endpoints for managing users
 */
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @authenticated
     * @response {"data":[{"id":"5","email":"updated@example.net","name":"Mercy Grady","username":"mercy"}]}
     *
     * @return array<\Illuminate\Http\Response>
     */
    public function index(Request $request)
    {
        $user = $request->user();

        abort_if(!$user->hasRole('admin'), 403, 'Permission denial!');

        $users = User::all();

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     */
    public function store(Request $request)
    {
        //
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
     * @bodyParam name string required Must not be greater than 255 characters.
     * @bodyParam email string required Must be a valid email address.
     * @bodyParam username string required Must not be greater than 255 characters and Must be unique.
     *
     * @authenticated
     * @response {"data":{"id":"5","email":"updated@example.net","name":"Mercy Grady","username":"mercy"}}
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        $user = $request->user();
        abort_if(!$user, 401, 'Unauthorized');

        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
        ]);

        return (new UserResource($user));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'   => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user ?? null)],
            'username'        => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user ?? null)],
        ]);
    }
}
