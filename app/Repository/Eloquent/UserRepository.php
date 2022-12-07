<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Models\UserData;
use App\Services\UserControllerService;
use App\Repository\Eloquent\UserDataRepository;
use App\Repository\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{

    private $userControllerService;

    public function __construct(User $model)
    {
        $this->model = $model;
        $this->userControllerService = new UserControllerService();
        $this->UserDataRepository = new UserDataRepository(new UserData());
    }

    /**
     * @return false|void
     */
    public function login($request)
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
                $success = $user->toArray();
                $success['token'] = $user->createToken('LaravelBoilerplate')->accessToken;
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }

        return $success;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function store($request): mixed {
        $this->userControllerService->validateInput($request,'store');
        //password confirmation not required for storing - only validation
        $request->request->remove('c_password');
        $response = parent::store($request);

        //user has additional data - add
        if ($request->has('data')) {
            $request->request->add(['user_id' => $response->id]);
            $this->UserDataRepository->store($request);
        }

        return $response;
    }
}
