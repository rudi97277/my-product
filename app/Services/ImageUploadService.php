<?php

namespace App\Services;

use Illuminate\Support\Str;


class ImageUploadService
{
    public function uploadImage($uploadedImage)
    {
        $fileExtension = pathinfo($uploadedImage->extension())["basename"];
        $imageName = Str::uuid() . "." . $fileExtension;
        $upload = $uploadedImage->storeAs('images', $imageName, 'public');
        return $upload;
    }
}
