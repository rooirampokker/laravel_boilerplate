<?php

namespace App\Http\Repository\api\v1;

use App\Http\Repository\api\v1\Interfaces\UserRepositoryInterface;
use App\Models\Role;
use App\Models\User;
use App\Models\UserData;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    private UserDataRepository $userDataRepository;

    public function __construct(User $model)
    {
        parent::__construct($model);
        $this->userDataRepository = new UserDataRepository(new UserData());
    }

    /**
     * @param $request
     * @return array
     */
    /**
     * @param $request
     * @return array
     */
    public function login($request)
    {
        $request = $request->only('email', 'password');

        try {
            $user = User::where('email', $request['email'])->first();

            if ($user && Hash::check($request['password'], $user->password)) {
                $success = $user->toArray();
                $success['token'] = $user->createToken(config('app.name'))->accessToken;

                return $this->ok(__('users.login.success'), $success);
            }

            return $this->unauthorised(__('users.login.invalid'));

            // @codeCoverageIgnoreStart
        } catch (\Throwable $exception) {
            $this->logError($exception);

            return $this->exception($exception);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param FormRequest $request
     * @return mixed
     */
    public function store($request): mixed
    {
        try {
            DB::beginTransaction();
            $requestParams = $request->all();

            if ($this->model->fill($requestParams)->save()) {
                if (isset($requestParams['roles'])) {
                    $this->model->assignRole($requestParams['roles']);
                }
                if (array_key_exists('data', $requestParams)) {
                    //adds the use_id to the response - required for user-data storing
                    $request->request->add(['user_id' => $this->model->id]);
                    $this->userDataRepository->store($request);
                }
                DB::commit();

                return collect([$this->model]);
            }

            DB::rollBack();
            return $this->invalid(__('users.store.failed'));

        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->logError($exception);

            return false;
        }
    }

    /**
     * @param FormRequest $request
     * @param $id
     * @return mixed
     */
    public function update($request, $id): mixed
    {
        try {
            DB::beginTransaction();
            $updateData = $this->userDataRepository->update($request, $id);
            $input = $request->all();
            unset($input['data']);

            //did the user-data update succeed?
            if ($updateData) {
                //is there anything left in the request bag for the user model?
                if (count($input)) {
                    $user  = User::findOrFail($id);
                    $user->fill($input)->save();
                }

                DB::commit();
                $model = User::findOrFail($id);
                return collect([$model]);
            }

            DB::rollBack();

            return false;
        } catch (\Throwable $exception) {
            $this->logError($exception);
            DB::rollBack();

            return $this->exception($exception);
        }
    }

    /**
     * Fetches a single User with associated data, if any
     *
     * @param $id
     * @return array|mixed|void
     */


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
                return $this->dataService->hydrateCollectionWithAdditionalData([$userCollection]);
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
                return $this->dataService->hydrateCollectionWithAdditionalData([$collection]);
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
            //get the user and his roles, where the role id matches $role_id -
            // should only even return a single user with a single role
            $user = $this->model::with('roles')->whereHas('roles', function($query) use($role_id) {
                $query->where('id', $role_id);
            })->find($user_id);

            $collection = $user->removeRole($user->roles->first());

            if ($collection) {
                return $this->dataService->hydrateCollectionWithAdditionalData([$collection]);
            }

            return false;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }
}
