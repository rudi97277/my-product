<?php

namespace App\Traits;


trait UserInfo
{
    public function getCurrentUser()
    {
        return auth()->user();
    }
}
