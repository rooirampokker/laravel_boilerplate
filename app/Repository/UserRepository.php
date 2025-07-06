<?php

namespace App\Repository;

use App\Models\Role;
use App\Models\User;
use App\Models\UserData;
use App\Repository\Interfaces\UserRepositoryInterface;
use App\Services\UserDataService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserRepository extends BaseRepositorys implements UserRepositoryInterface
{
    private UserDataRpository $userDataRepository;
    private UserDataService $userDataService;

    public function __construct(User $model)
    {
        $this->model = $model;
        $this->userDataService = new UserDataService();
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
            }

            return $this->unauthorised(__('users.login.invalid'));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            return $this->exception($exception);
        }
    }

    /**
     * @param FormRequest $request
     * @return mixed
     */
    public function store(FormRequest $request): mixed
    {
        try {
            DB::beginTransaction();
            $requestParams = $request->all();

            if ($this->model->fill($request->all())->save()) {
                if (array_key_exists('data', $requestParams)) {
                    //adds the use_id to the response - required for user-data storing
                    $request->request->add(['user_id' => $this->model->id]);
                    $this->userDataRepository->store($request);
                    $this->model->assignRole($requestParams['roles']);
                }

                DB::commit();
                return $this->userDataService->hydrateUserWithAdditionalData([$this->model], 'data');
            }

            DB::rollBack();
            return $this->invalid(__('users.store.failed'));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            DB::rollBack();
            return false;
        }
    }

    /**
     * @param FormRequest $request
     * @param $id
     * @return mixed
     */
    public function update(FormRequest $request, $id): mixed
    {
        try {
            DB::beginTransaction();
            $userDataUpdateResponse = $this->userDataRepository->update($request, $id);
            $input = $request->all();
            unset($input['data']);
            //did the user-data update succeed?
            if ($userDataUpdateResponse) {
                //is there anything left in the request bag for the user model?
                if (count($input)) {
                    $user  = User::findOrFail($id);
                    $user->fill($input)->save();
                }

                DB::commit();
                return true;
            }

            DB::rollBack();
            return false;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            DB::rollBack();
            return $this->exception($exception);
        }
    }

    /**
     * @return array|mixed
     */
    public function index()
    {
        dd('here');
        try {
            $userCollection = $this->model::with('data', 'roles')->get();

            return $this->userDataService->hydrateUserWithAdditionalData($userCollection, 'data');
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }
    /**
     * @return array|mixed
     */
    public function indexAll()
    {
        try {
            $userCollection = $this->model::withTrashed()->with('data', 'roles')->get();

            return $this->userDataService->hydrateUserWithAdditionalData($userCollection, 'data');
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }
    /**
     * @return array|mixed
     */
    public function indexTrashed()
    {
        try {
            $userCollection = $this->model::onlyTrashed()->with('data', 'roles')->get();

            return $this->userDataService->hydrateUserWithAdditionalData($userCollection, 'data');
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
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
            $userCollection = $this->model::with('data', 'roles')->find($id);
            if ($userCollection) {
                return $this->userDataService->hydrateUserWithAdditionalData([$userCollection], 'data');
            }

            return false;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }

    /**
     * @param $request
     * @param $id
     * @return false
     */
    public function syncRole($request, $id)
    {
        try {
            $params = $request->all();
            $user = $this->model::find($id);
            $userCollection = $user->syncRoles(Role::whereIn('id', $params['roles'])->get());
            if ($userCollection) {
                return $this->userDataService->hydrateUserWithAdditionalData([$userCollection], 'data');
            }

            return false;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }
    /**
     * @param $request
     * @param $id
     * @return false
     */
    public function addRole($request, $id)
    {
        try {
            $params = $request->all();
            $user = $this->model::find($id);
            $collection = $user->assignRole(Role::whereIn('id', $params['roles'])->get());
            if ($collection) {
                return $this->userDataService->hydrateUserWithAdditionalData([$collection], 'data');
            }

            return false;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }

    /**
     * @param $user_id
     * @param $role_id
     * @return false
     */
    public function removeRole($user_id, $role_id)
    {
        try {
            $user = $this->model::find($user_id);
            $collection = $user->removeRole($role_id);

            if ($collection) {
                return $this->userDataService->hydrateUserWithAdditionalData([$collection], 'data');
            }

            return false;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }
}
