<?php

namespace App\Http\Controllers;

use App\Repository\UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserData;

use App\Http\Resources\UserCollection;
use Validator;

class UserController extends Controller
{
    private Model $user;
    private $userRepository;


    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    /**
     * @return mixed
     */
    public function index()
    {
        $response = $this->userRepository->index();

        if ($response) {
            return response()->json(['success' => $response], httpStatusCode('SUCCESS'));
        } else {
            return response()->json(['error' => __('auth.unauthorized')], httpStatusCode('UNAUTHORISED'));
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        try {
            $userCollection = User::with('data')->findOrFail($id);
            $this->authorize('view', $userCollection);

            $updated        = $this->collapseUserDataIntoParent($userCollection);
        } catch (\Exception $e) {
            $httpStatus = getExceptionType($e);

            return response()->json(['failed' => __('general.failed', ['message' => $e->getMessage()])], $httpStatus);
        }

        return response()->json(['success' => [$updated]], httpStatusCode('SUCCESS'));
    }
  /**
   * @return \Illuminate\Http\Response
   */
    public function login(Request $request)
    {
        $response = $this->userRepository->login($request);

        if ($response) {
            return response()->json(['success' => $response], httpStatusCode('SUCCESS'));
        } else {
            return response()->json(['error' => __('auth.unauthorized')], httpStatusCode('UNAUTHORISED'));
        }
    }
  /**
   * @param Request $request
   * @return mixed
   * @throws \Exception
   */
    public function store(Request $request)
    {
        $response = $this->userRepository->store($request);

        if ($response) {
            return response()->json(['success' => $response], httpStatusCode('SUCCESS'));
        } else {
            return response()->json(['error' => __('auth.unauthorized')], httpStatusCode('UNAUTHORISED'));
        }
    }

  /**
   * @param Request $request
   * @param $id
   * @return mixed
   */
    public function update(Request $request, $id)
    {
        try {
            $errors = $this->validateInput($request, 'update');

            if ($errors) {
                return response()->json(['failed' => __('general.failed', ['message' => $errors])], httpStatusCode('NOT_IMPLEMENTED'));
            }
            $userCollection = User::with('data')->find($id);
            $this->authorize('update', $userCollection);

            $input = $request->all();
            if (count($input)) {
                $user = User::find($id);
                if ($user) {
                    if ($user->fill($input)->save()) {
                        $this->insertUserData($input, $user);
                    } else {
                        throw new \Exception(__('general.record.not_saved', ['id' => $id]));
                    }
                } else {
                    throw new \Exception(__('general.record.not_found', ['id' => $id]));
                }
            } else {
                throw new \Exception(__('general.input_error'));
            }
        } catch (\Exception $e) {
            $httpStatus = getExceptionType($e);

            return response()->json(['failure' => __('general.failed', ['message' => $e->getMessage()])], $httpStatus);
        }

        return response()->json(['success' => __('general.record.update.success', ['id' => $id])], httpStatusCode('SUCCESS'));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        try {
            $user = User::find($id);
            if ($user) {
                $this->authorize('destroy', $user);
                $user->delete();
            } else {
                throw new \Exception(__('general.record.not_found', ['id' => $id]));
            }
        } catch (\Exception $e) {
            $httpStatus = getExceptionType($e);
            return response()->json(['failure' => __('general.failed', ['message' => $e->getMessage()])], $httpStatus);
        }

        return response()->json(['success' => __('general.record.destroy.success', ['id' => $id])], httpStatusCode('SUCCESS'));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function restore($id)
    {
        try {
            $user = User::withTrashed()->find($id);
            if (!$user) {
                  throw new \Exception(__('general.record.not_found', ['id' => $id]));
            }
            $this->authorize('restore', $user);
            $user->restore();
        } catch (\Exception $e) {
            $httpStatus = getExceptionType($e);
            return response()->json(['failure' => __('general.failed', ['message' => $e->getMessage()])], $httpStatus);
        }

        return response()->json(['success' => __('general.record.restore.success', ['id' => $id])], httpStatusCode('SUCCESS'));
    }
  /**
   * @param $user
   * @return array
   */
    public function collapseUserDataIntoParent($user)
    {
        $dataCollection = [];
        foreach ($user->data as $data) {
            $dataCollection[$data->key] = $data->value;
        }
        unset($user->data);

        return array_merge($user->toArray(), $dataCollection);
    }
  /**
   * @param $dataArray
   * @param $user
   * @throws \Exception
   */
    public function insertUserData($dataArray, $user)
    {
        try {
            if (array_key_exists('data', $dataArray)) {
                foreach ($dataArray['data'] as $key => $value) {
                    $key = str_replace("&nbsp;", '', trim($key));
                    $value = str_replace("&nbsp;", '', trim($value));
                    UserData::updateOrCreate(
                        [
                        'user_id' => $user->id,
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
