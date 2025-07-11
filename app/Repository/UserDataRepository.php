<?php

namespace App\Repository;

use App\Models\UserData;
use App\Repository\Interfaces\UserDataRepositoryInterface;
use App\Services\UserDataService;
use Illuminate\Support\Facades\Log;

class UserDataRepository extends BaseRepository implements UserDataRepositoryInterface
{
    private UserDataService $userDataService;

    public function __construct(UserData $model)
    {
        $this->model = $model;
        $this->userDataService = new UserDataService();
    }

    /**
     * store user data from request
     * This uses createOrUpdate to dynamically add new entity attribute-value pairs as necessary
     *
     * @param $request
     * @return mixed
     * @throws \Exception
     */

    public function store($request): mixed
    {
        try {
            $user_id = $request->request->get('user_id');
            foreach ($request['data'] as $key => $value) {
                $key = str_replace("&nbsp;", '', trim($key));
                $value = str_replace("&nbsp;", '', trim($value));
                UserData::updateOrCreate(
                    [
                        'user_id' => $user_id,
                        'key'     => $key],
                    ['value' => $value]
                );
            }

            return $this->ok(__('user.store.only_data.success'));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return $this->exception($exception);
        }
    }

    /**
     * update user data from request
     * NOTE: this will not add new key-value pairs - only update existing keys
     *
     * @param $request
     * @param $user_id
     * @return mixed
     */

    public function update($request, $user_id): mixed
    {
        try {
            $response = true;
            $request = $request->all();
            if (array_key_exists('data', $request)) {
                foreach ($request['data'] as $key => $value) {
                    $response = UserData::where([
                        ['user_id', '=', $user_id],
                        ['key', '=', $key]
                    ])->update(['value' => $value]);
                }
            }
            //this does not return json - only boolean to check if any field was updated or not
            return $response;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }
}
