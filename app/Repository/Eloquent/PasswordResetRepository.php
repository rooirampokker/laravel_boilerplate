<?php

namespace App\Repository\Eloquent;

use Validator;

use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\Repository\PasswordResetRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\RepositoryResponseTrait;

class PasswordResetRepository extends BaseRepository implements PasswordResetRepositoryInterface
{
    use RepositoryResponseTrait;

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
                return $this->badRequest(__('validation.reset_token.invalid_email'));
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

            return $this->exception($exception);
        }

        return $this->ok(__('validation.reset_token.success'));
    }

    /**
     * @param  $token
     * @return mixed
     */
    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();
        if (!$passwordReset) {
            return $this->invalid(__('validation.reset_tokens.invalid'));
        }
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return $this->invalid(__('validation.reset_tokens.invalid'));
        }

        return $this->ok(__('validation.reset_tokens.success'));
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

                return $this->ok(__('validation.password_updated'), [$user]);
            }
        }

        return $this->invalid(__('validation.reset_token.invalid'));
    }
}
