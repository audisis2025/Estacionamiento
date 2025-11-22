<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Container\Attributes\DB;

class UserService
{
    public function UpdateNotificationToken(int $id, array $data)
    {
        $user = User::findOrFail($id);
        return $user->update([
            'notification_token'=> $data ['notification_token']
        ]);
    }
}
