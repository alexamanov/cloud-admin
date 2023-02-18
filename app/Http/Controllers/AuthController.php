<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        private readonly ValidationFactory $validationFactory
    ) {
        $this->middleware(
            'auth:api',
            [
                'except' => [
                    'login',
                    'register',
                ],
            ]
        );
    }

    /**
     * @throws ValidationException
     */
    public function register(Request $request): JsonResponse
    {
        $validator = $this->validationFactory->make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|confirmed|min:6',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        User::create(
            array_merge(
                $validator->validated(),
                [
                    'password' => bcrypt($request->getPassword()),
                ]
            )
        );

        return response()->json(['message' => 'User successfully registered'], 201);
    }

    public function login(Request $request)
    {
    }
}
