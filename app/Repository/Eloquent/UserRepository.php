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
    private UserControllerService $userControllerService;
    private UserDataRepository $userDataRepository;

    public function __construct(User $model)
    {
        $this->model = $model;
        $this->userControllerService = new UserControllerService();
        $this->userDataRepository = new UserDataRepository(new UserData());
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
    public function store($request): mixed
    {
        try {
            $this->userControllerService->validateInput($request, 'store');
            //password confirmation not required for storing - only validation
            $request->request->remove('c_password');
            $response = parent::store($request);
            //store additional data, if any
            $request->request->add(['user_id' => $response->id]);
            $this->userDataRepository->store($request);
        } catch (\Exception $e) {
            Log::error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }

        return $response;
    }

    public function index() {
        try {
            $userCollection = (User::with('data')->get());
            $users          = [];
            //iterates over all users, collapses user->data into user and return data
            foreach ($userCollection as $user) {
                array_push($users, eavParser($user));
            }
        } catch (\Exception $e) {
            Log::error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }

        return $users;
    }
    /**
     * Fetches a single User with associated data, if any
     *
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse|mixed|void
     */
    public function show($id)
    {
        try {
            $userCollection = User::with('data')->findOrFail($id);
            $user           = eavParser($userCollection);
        } catch (\Exception $e) {
            $httpStatus = getExceptionType($e);
            return response()->json(['failed' => __('general.failed', ['message' => $e->getMessage()])], $httpStatus);
        }

        return $user;
    }
}
