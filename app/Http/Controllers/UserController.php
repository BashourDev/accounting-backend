<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return response(User::query()->where('type', '<>', User::TYPE_ADMIN)->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'username' => 'unique:users,username',
            ]);
        } catch (ValidationException $e) {
            return \response('username already exists', 422);
        }


        return response(User::query()->create([
            'name' => $request->get('name'),
            'username' => $request->get('username'),
            'password' => bcrypt($request->get('password')),
            'type' => $request->get('type'),
        ]));
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return Response
     */
    public function show(User $user)
    {
        return response($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function update(Request $request, User $user)
    {
        $v = Validator::make($request->only(['username']), [
            'username' => [
                'required',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        try {
            $v->validate();
        } catch (ValidationException $e) {
            return \response('username already exists', 422);
        }

        return response($user->update([
            'name' => $request->get('name'),
            'username' => $request->get('username'),
            'type' => $request->get('type'),
        ]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return Response
     */
    public function destroy(User $user)
    {
        return response($user->delete());
    }

    public function lockUser(User $user)
    {
        $user->is_locked = true;
        $user->save();
        return response(true);
    }

    public function unlockUser(User $user)
    {
        $user->is_locked = false;
        $user->save();
        return response(true);
    }

    public function updatePassword(Request $request, User $user)
    {
        if (!Hash::check($request->get('oldPassword'), $user->password)) {
            return response("password mismatch", 403);
        }
        $user->password = bcrypt($request->get('password'));
        return response(true);
    }

    public function updatePermissions(Request $request, User $user)
    {
        $user->permissions = $request->get('permissions');
        $user->save();
    }
}
