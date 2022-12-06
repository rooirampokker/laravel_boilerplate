<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Repository\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $request = $request->all();
        try {
            if (
                Auth::attempt(
                    [
                    'email' => $request['email'],
                    'password' => $request['password']]
                )
            ) {
                $user = Auth::user();
                $success['token'] = $user->createToken('LaravelBoilerplate')->accessToken;
                $success['id'] = $user->id;
                $success['email'] = $user->email;
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }

        return $success;
    }

    /**
     * @return mixed
     */
//    public function index() {
//        try {
//            $userCollection = (User::with('data')->get());
//            $users          = [];
//            //iterates over all users, collapses user->data into user and return data
//            foreach ($userCollection as $user) {
//                array_push($users, $this->collapseUserDataIntoParent($user));
//            }
//        } catch(\Exception $e) {
//            $httpStatus = getExceptionType($e);
//
//            return response()->json(['failure' => __('general.failed', ['message' => $e->getMessage()])], $httpStatus);
//        }
//
//        return response()->json(['success'=> $users],httpStatusCode('SUCCESS'));
//    }
}
