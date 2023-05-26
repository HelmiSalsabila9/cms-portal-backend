<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::when(request()->q, function($users) {
            $users = $users->where('name', 'like', '%' . request()->q . '%');
        })->oldest()->paginate(5);

        return new UserResource(true, 'List Data Users.', $users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users',
            'email' => 'required|unique:users',
            'telepon' => 'required|unique:users',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create Users
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telepon' => $request->telepon,
            'password' => bcrypt($request->password),
        ]);

        if ($user) {
            return new UserResource(true, 'Data Users Berhasil Disimpan.', $user);
        }

        return new UserResource(false, 'Data Users Gagal Disimpan.', null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::whereId($id)->first();

        if ($user) {
            return new UserResource(true, 'Detail Data Users.', $user);
        }

        return new UserResource(false, 'Detail Data User Tidak Ditemukan.', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users,name,'. $user->id,
            'email' => 'required|unique:users,email,'.$user->id,
            'telepon' => 'required|unique:users,telepon,'.$user->id,
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->password == "") {
            // update user tanpa password
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'telepon' => $request->telepon,
            ]);
        }

        // Update user dg pw
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'telepon' => $request->telepon,
            'password' => bcrypt($request->password)
        ]);

        if ($user) {
            return new UserResource(true, 'Data User Berhasil Diupdate.', $user);
        }

        return new UserResource(false, 'Data User Gagal Diupdate.', null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->delete()) {
            return new UserResource(true, 'Data User Berhasil Dihapus.', null);
        }

        return new UserResource(false, 'Data User Gagal Dihapus.', null);
    }
}
