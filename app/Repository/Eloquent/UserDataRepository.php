<?php

namespace App\Repository\Eloquent;

use App\Models\UserData;
use App\Services\UserDataControllerService;
use App\Repository\UserDataRepositoryInterface;
use Illuminate\Support\Facades\Log;

class UserDataRepository extends BaseRepository implements UserDataRepositoryInterface
{
    private UserDataControllerService $userDataControllerService;

    public function __construct(UserData $model)
    {
        $this->model = $model;
        $this->userDataControllerService = new UserDataControllerService();
    }

    /**
     * update user data from request
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
}
