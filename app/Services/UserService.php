<?php

namespace App\Services;

use App\Models\User;
use App\Traits\UserInfo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserService
{
    use UserInfo;
    public function createNewUser($request, ImageUploadService $imageUploadService)
    {
        $data = $request->only('first_name', 'last_name', 'email', 'date_of_birth', 'address');
        $data['image_url'] = $imageUploadService->uploadImage($request->image);
        $data['password'] = bcrypt($request->password);
        return User::create($data);
    }

    public function getUserData()
    {
        return User::findOrFail($this->getCurrentUser()->id);
    }

    public function userLogin($request)
    {
        $user = User::where('email', $request->email)->firstOrFail();
        if (!Hash::check($request->password, $user->password))
            return throw new HttpException(401, 'Wrong email or password!');

        $token = $user->createToken('Personal Access Token');
        return (['token' => $token->plainTextToken]);
    }

    public function updateUserProfile($request, ImageUploadService $imageUploadService)
    {
        $user = $this->getUserData();
        $data = $request->only('first_name', 'last_name', 'date_of_birth', 'address', 'email');
        if ($request->old_password) {
            if (!Hash::check($request->old_password, $user->password))
                throw new HttpException(401, 'Wrong old password');
            $data['password'] = bcrypt($request->password);
        }
        if ($request->image) {
            if (Storage::disk('public')->exists($user->image_url))
                Storage::disk('public')->delete($user->image_url);
            $data['image_url'] = $imageUploadService->uploadImage($request->image);
        }
        return $user->update($data);
    }
}
