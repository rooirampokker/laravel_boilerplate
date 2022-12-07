<?php

namespace App\Repository\Eloquent;

use App\Models\UserData;
use App\Services\UserDataControllerService;
use App\Repository\UserDataRepositoryInterface;

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
     * @return void
     */
    public function store($request): mixed
    {
        try {
            $request = $request->all();
            if (array_key_exists('data', $request)) {
                foreach ($request['data'] as $key => $value) {
                    $key = str_replace("&nbsp;", '', trim($key));
                    $value = str_replace("&nbsp;", '', trim($value));
                    UserData::updateOrCreate(
                        [
                            'user_id' => $request['user_id'],
                            'key'     => $key],
                        ['value' => $value]
                    );
                }
            }
        } catch (\Exception $e) {
            print_r($e->getMessage());
            $httpStatus = getExceptionType($e);
            throw new \Exception(__('general.failed', ['message' => $e->getMessage()]), $httpStatus);
        }
    }
}
