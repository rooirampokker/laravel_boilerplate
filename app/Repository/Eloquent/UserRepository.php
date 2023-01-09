<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Models\UserData;
use App\Services\UserControllerService;
use App\Repository\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\RepositoryResponseTrait;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    use RepositoryResponseTrait;
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
                $success['token'] = $user->createToken(config('app.name'))->accessToken;

                return $success;
            } else {
                return $this->unauthorised(__('users.login.invalid'));
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            return $this->exception($exception);
        }
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
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            $this->exception($exception);
        }

        return $this->ok(__('validation.password_updated'), [$response]);
    }
    /**
     * @param $request
     * @param $id
     * @return mixed
     */
    public function update($request, $id): mixed
    {
        $success = false;
        try {
            $this->userControllerService->validateInput($request, 'update');
            $input = $request->all();
            if (count($input)) {
                $user = User::find($id);
                if ($user) {
                    if ($user->fill($input)->save()) {
                        $success = $this->userDataRepository->update($request, $id);
                    } else {
                        throw new \Exception(__('general.record.not_saved', ['id' => $id]));
                    }
                } else {
                    throw new \Exception(__('general.record.not_found', ['id' => $id]));
                }
            } else {
                throw new \Exception(__('general.input_error'));
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }

        return $success;
    }

    /**
     * @return array
     */
    public function index()
    {
        try {

            $userCollection = (User::with('data')->get());
            $users          = [];

            //iterates over all users, collapses user->data into user and return data
            foreach ($userCollection as $user) {
                array_push($users, eavParser($user));
            }

            return $this->ok(__('users.index.success'), $users);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return $this->exception($exception);
        }
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
            $userCollection = User::with('data')->find($id);
            if ($userCollection) {
                $user = eavParser($userCollection);

                return $this->ok(__('users.update.success'), $user);
            } else {

                return $this->notFound(__('users.show.failed'));
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return $this->exception($exception);
        }
    }
}
