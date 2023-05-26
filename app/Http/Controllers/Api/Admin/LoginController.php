<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * index
     *
     * @param  mixed $request
     * @return void
     */
    public function index(Request $request)
    {
        // Set validasi
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Respon error validasi
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Get "email" dan "password" dari input
        $credentials = $request->only('email', 'password');

        // Check jika tidak sesuai
        if (!$token = auth()->guard('api')->attempt($credentials)) {
            // Respon login "failed"
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password salah!'
            ], 401);
        }

        // Respon login berhasil
        return response()->json([
            'success' => true,
            'user' => auth()->guard('api')->user(),
            'token' => $token
        ], 200);

    }

    /**
     * getUser
     *
     * @return void
     */
    public function getUser()
    {
        // respon user yang sedang login
        return response()->json([
            'success' => true,
            'user' => auth()->guard('api')->user()
        ], 200);
    }

     /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(Request $request)
    {
        // refresh token
        $refreshToken = JWTAuth::refresh(JWTAuth::getToken());

        // Set user dengan token baru
        $user = JWTAuth::setToken($refreshToken)->toUser();

        // Set header "Authorization" dengan type Bearer + Token baru
        $request->headers->set('Authorization', 'Bearer' . $refreshToken);

        // Respon data user dengan token baru
        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $refreshToken,
        ], 200);
    }

    /**
     * logout
     *
     * @return void
     */
    public function logout()
    {
        // remove token jwt
        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

        // Respon success logout
        return response()->json([
            'success' => true,
        ], 200);
    }
}
