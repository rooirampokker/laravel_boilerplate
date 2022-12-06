<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Repository\UserRepositoryInterface;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * @return false|void
     */
    public function login(Request $request)
    {
        $success = false;

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('LaravelBoilerplate')->accessToken;
            $success['id'] = $user->id;
            $success['email'] = $user->email;

            return $success;
        }
    }
}
