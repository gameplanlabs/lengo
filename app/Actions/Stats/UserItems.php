<?php

namespace App\Actions\Stats;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class UserItems
{
    public static Authenticatable $user;

    public function __construct()
    {
        self::$user = Auth::user();
    }

    public static function allForUser(): array
    {

        return [
            'goals' => Goal::query()->where()
        ];
    }
}
