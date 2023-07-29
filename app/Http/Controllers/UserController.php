<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ImageUploadService;
use App\Services\UserService;
use App\Traits\ApiResponser;
use App\Traits\UserInfo;

class UserController extends Controller
{
    use ApiResponser;
    protected $service;
    protected $imageUploadService;

    public function __construct(UserService $userService, ImageUploadService $imageUploadService)
    {
        $this->service = $userService;
        $this->imageUploadService = $imageUploadService;
    }

    public function register(UserRegisterRequest $request)
    {
        $user = $this->service->createNewUser($request, $this->imageUploadService);
        return $this->showOne(new UserResource($user));
    }

    public function login(UserLoginRequest $request)
    {
        $token = $this->service->userLogin($request);
        return $this->showOne($token);
    }

    public function profile()
    {
        $user = $this->service->getUserData();
        return $this->showOne(new UserResource($user));
    }

    public function update(UserUpdateRequest $request)
    {
        $status = $this->service->updateUserProfile($request, $this->imageUploadService);
        return $this->showOne($status);
    }
}
