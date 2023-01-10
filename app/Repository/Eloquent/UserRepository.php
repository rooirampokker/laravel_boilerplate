<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Models\UserData;
use App\Services\UserControllerService;
use App\Repository\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
     * @param $request
     * @return array
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

                return $this->ok(__('users.login.success'), $success);
            } else {
                return $this->unauthorised(__('users.login.invalid'));
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            return $this->exception($exception);
        }
    }

    /**
     * @param $request
     * @return mixed
     */
    public function store($request): mixed
    {
        try {
            DB::beginTransaction();

            $this->userControllerService->validateInput($request, 'store');
            //adds the use_id to the response - required for user-data storing
            if ($this->model->fill($request->all())->save()) {
                if (array_key_exists('data', $request->all())) {
                    $request->request->add(['user_id' => $this->model->id]);
                    $this->userDataRepository->store($request);
                }

                DB::commit();
                return $this->ok(__('users.store.success'), $this->model);
            }

            DB::rollBack();
            return $this->invalid(__('users.store.failed'));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            DB::rollBack();
            return $this->exception($exception);
        }
    }

    /**
     * @param $request
     * @param $id
     * @return mixed
     */
    public function update($request, $id): mixed
    {
        try {
            $this->userControllerService->validateInput($request, 'update');
            $input = $request->all();
            if (count($input)) {
                $user = User::find($id);
                if ($user) {
                    $user->fill($input)->save();
                    $userDataUpdateResponse = $this->userDataRepository->update($request, $id);
                    if($userDataUpdateResponse && $user->wasChanged()) {

                        return $this->ok(__('users.update.with_data.success', ['id' => $id]));
                    } elseif($user->wasChanged()) {

                        return $this->ok(__('users.update.success', ['id' => $id]));
                    } elseif ($userDataUpdateResponse) {

                        return $this->ok(__('users.update.only_data.success', ['id' => $id]));
                    }
                }
            }

            return $this->invalid(__('users.update.failed',  ['id' => $id]));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return $this->exception($exception);
        }
    }

    /**
     * @return array|mixed
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
     * @return array|mixed|void
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
