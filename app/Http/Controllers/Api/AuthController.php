<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;


class AuthController extends Controller
{


    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'getUserTypes']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {

        try {
            $validator = Validator::make(request()->all(), [
                "email" => "required|email",
                "password" => "required"
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $credentials = request(['email', 'password']);

            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return $this->respondWithToken([
                'token' => $token,
                'user' => auth()->user()
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ]);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 43200
        ]);
    }

    /**
     * Register a new user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     * 
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "name" => "required",
                "email" => "required|email|unique:users",
                "password" => "required|confirmed"
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }


            // Alta al usuario
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = $request->password;
            $user->user_type = $request->user_type;
            $user->save();

            // Respuesta de la API

            return response($user, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Get all users
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUsers()
    {
        $users = User::all();
        return response($users, Response::HTTP_OK);
    }

    /**
     * Get a user by id
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * *
     */

    public function getUser($id)
    {
        $user = User::find($id);
        return response($user, Response::HTTP_OK);
    }

    /**
     * Update a user
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function updateUser(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "password" => "confirmed"
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = User::find($id);
            $user->name = $request->name ? $request->name : $user->name;
            $user->email = $request->email ? $request->email : $user->email;
            $user->phone = $request->phone ? $request->phone : $user->phone;
            $user->password = $request->password != '' ? $request->password : $user->password;
            $user->user_type = $request->user_type ? $request->user_type : $user->user_type;
            $user->save();

            return response($user, Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        }
    }



    /**
     * Return the types of users
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserTypes()
    {
        $userTypes = UserType::all();
        return response($userTypes, Response::HTTP_OK);
    }
}
