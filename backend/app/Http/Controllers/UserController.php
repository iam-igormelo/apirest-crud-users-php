<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $currentPage = $request->get('current_page') ?? 1;
        $regsPerPage = 3;
        $skip = ($currentPage - 1) * $regsPerPage;
        $users = User::skip($skip)->take($regsPerPage)->orderByDesc('id')->get();
        return response()->json($users->toResourceCollection(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        try {
            $user = new User();
            $user->fill($data);
            $user->password = Hash::make(1234);
            $user->save();
            return response()->json($user->toResource(), 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Falha ao inserir usuário!'
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json($user->toResource(), 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Falha ao buscar usuário!'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $data = $request->validated();

        try {
            $user = User::findOrFail($id);
            $user->update($data);
            return response()->json($user->toResource(), 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Falha ao alterar o usuário!'
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        try {
            $removedUser = User::destroy($id);
            if (!$removedUser) {
                throw new Exception();
            }
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Falha ao remover o usuário!'
            ], 400);
        }
    }
}