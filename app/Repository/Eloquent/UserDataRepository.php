<?php

namespace App\Repository\Eloquent;

use App\Models\UserData;
use App\Services\UserDataControllerService;
use App\Repository\UserDataRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class UserDataRepository extends BaseRepository implements UserDataRepositoryInterface
{
    private UserDataControllerService $userDataControllerService;

    public function __construct(UserData $model)
    {
        $this->model = $model;
        $this->userDataControllerService = new UserDataControllerService();
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
        $response = false;
        try {
            $request = $request->all();

            if (array_key_exists('data', $request)) {
                foreach ($request['data'] as $key => $value) {
                    $key = str_replace("&nbsp;", '', trim($key));
                    $value = str_replace("&nbsp;", '', trim($value));
                    $response = UserData::updateOrCreate(
                        [
                            'user_id' => $request['user_id'],
                            'key'     => $key],
                        ['value' => $value]
                    );
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }

        return $response;
    }
    /**
     * update user data from request
     * NOTE: this will not add new key-value pairs - only update existing keys
     *
     * @param $request
     * @return mixed
     * @throws \Exception
     */

    public function update($request, $user_id): mixed
    {
        $response = false;
        try {
            $request = $request->all();
            if (array_key_exists('data', $request)) {
                $response = DB::transaction(function () use ($request, $user_id) {
                    foreach($request['data'] as $key => $value) {
                            $response = UserData::where([
                                ['user_id', '=', $user_id],
                                ['key', '=', $key]
                            ])->update(['value' => $value]);
                    }
                     return $response;
                });
            } else { //no data to update
                $response = true;
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }

        return $response;
    }
}
