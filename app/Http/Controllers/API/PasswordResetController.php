<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\Models\User;
use App\Models\PasswordReset;
use Validator;

class PasswordResetController extends Controller
{
    public $recordNotFoundStatus = 404;
    public $successStatus = 200;
    /**
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
            if ($user && $passwordReset) {
                $user->notify(new PasswordResetRequest($passwordReset->token));
            }
        } catch (\Exception $e) {
            $httpStatus = getExceptionType($e);

            return response()->json(['failure' => __('validation.reset_token.failed', ['message' => $e->getMessage()])], $httpStatus);
        }
        return response()->json(['success' => __('validation.reset_token.success')], $this->successStatus);
    }

    /**
     * @param  $token
     * @return mixed
     */
    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();
        if (!$passwordReset) {
            return response()->json(['failed' => ""], $this->recordNotFoundStatus);
        }
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return response()->json(['failed' => __('validation.reset_token.invalid')], $this->recordNotFoundStatus);
        }

        return response()->json(['success' => $passwordReset], $this->successStatus);
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
        if (!$passwordReset) {
            return response()->json(['failed' =>  __('validation.reset_token.invalid')], $this->recordNotFoundStatus);
        }
        $user = User::where('email', $passwordReset->email)->first();

        if (!$user) {
            return response()->json(['failed' => __('validation.reset_token.invalid_email')], $this->recordNotFoundStatus);
        }

        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset->delete();
        $user->notify(new PasswordResetSuccess($passwordReset));

        return response()->json($user);
    }
}
