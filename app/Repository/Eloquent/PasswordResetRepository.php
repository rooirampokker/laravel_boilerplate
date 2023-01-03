<?php

namespace App\Repository\Eloquent;

use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\Repository\PasswordResetRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PasswordResetRepository extends BaseRepository implements PasswordResetRepositoryInterface
{
    public function __construct(PasswordReset $model)
    {
        $this->model = $model;
    }

    /**
     * TODO: CLEAN THIS UP A BIT - SINGLE RESPONSIBILITY
     *
     * @param  Request $request
     * @return mixed
     */
    public function create(Request $request)
    {
        try {
            $request->validate(
                [
                    'email' => 'required|string|email',
                ]
            );
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['failed' => __('validation.reset_token.invalid_email')], $this->recordNotFoundStatus);
            }

            $passwordReset = PasswordReset::updateOrCreate(
                ['email' => $user->email],
                ['email' => $user->email,
                    'token' => str_random(60)]
            );

            if ($passwordReset) {
                $user->notify(new PasswordResetRequest($passwordReset->token));
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
        return true;
    }

    /**
     * @param  $token
     * @return mixed
     */
    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();
        if (!$passwordReset) {
            return false;
        }
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return false;
        }

        return $passwordReset;
    }

    /**
     * @param  Request $request
     * @return mixed
     */
    public function reset(Request $request)
    {
        $validateFields = [
            'email'    => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'token'    => 'required'
        ];
        Validator::make($request->all(), $validateFields);

        $passwordReset = PasswordReset::where(
            [
                ['token', $request->token],
                ['email', $request->email]
            ]
        )->first();
        if ($passwordReset) {
            $user = User::where('email', $passwordReset->email)->first();
            if ($user) {
                $user->password = bcrypt($request->password);
                $user->save();
                $passwordReset->delete();
                $user->notify(new PasswordResetSuccess($passwordReset));

                return response()->json($user);
            }
        }
        return false;
    }
}
