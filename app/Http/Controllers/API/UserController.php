<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserData;
use App\Http\Resources\UserCollection;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Validator;

class UserController extends Controller
{
    private $user;

    public function __construct()
    {
        //controllers are loaded before middleware in Laravel >5, so the following closure is required to get user ID from constructor
        $this->middleware(
            function (Request $request, $next) {
                $this->user = User::find(Auth::id());

                return $next($request);
            }
        );
    }
    /**
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] =  $user->createToken('LotteriesCouncil')-> accessToken;
            $success['id']    = $user->id;
            $success['email'] =  $user->email;

            return response()->json(['success' => $success], httpStatusCode('SUCCESS'));
        } else {
            return response()->json(['error' => __('auth.unauthorized')], httpStatusCode('UNAUTHORISED'));
        }
    }
    /**
     * @param  Request $request
     * @return mixed
     * @throws \Exception
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', $this->user);

            $errors = $this->validateInput($request, 'create');
            if ($errors) {
                throw new \Exception($errors);
            }

            $input             = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user              = User::create($input);

            //adds additional data supplied during registration - adds entry to the user_data table;
            $this->insertUserData($input, $user);
            $success['token'] = $user->createToken('LotteriesCouncil')->accessToken;
            $success['id']    = $user->id;
            $success['email'] = $user->email;
        } catch (\Exception $e) {
            $httpStatus = getExceptionType($e);

            return response()->json(['failure' => __('general.failed', ['message' => $e->getMessage()])], $httpStatus);
        }

        return response()->json(['success' => $success], httpStatusCode('SUCCESS'));
    }
    /**
     * @return mixed
     */
    public function index()
    {
        try {
            $this->authorize('viewAny', $this->user);

            $userCollection = (User::with('data')->get());
            $users          = [];
            //iterates over all users, collapses user->data into user and return data
            foreach ($userCollection as $user) {
                array_push($users, $this->collapseUserDataIntoParent($user));
            }
        } catch (\Exception $e) {
            $httpStatus = getExceptionType($e);

            return response()->json(['failure' => __('general.failed', ['message' => $e->getMessage()])], $httpStatus);
        }

        return response()->json(['success' => $users], httpStatusCode('SUCCESS'));
    }

    /**
     * @param  $id
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
     * @param  Request $request
     * @param  $id
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
     * @param  $id
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
     * @param  $id
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
     * @param  $request
     * @param  $validationType
     * @return mixed
     */
    public function validateInput($request, $validationType)
    {
        //use this array to validate creation of new users
        $validateCreate = [
        'email'      => 'required|email',
        'password'   => 'required',
        'c_password' => 'required|same:password',
        ];
        //use this array to validate updates
        $validateUpdate = [
        'email' => 'email'
        ];
        $validateThis   = ($validationType == 'create') ? $validateCreate : $validateUpdate;
        $validated      = Validator::make($request->all(), $validateThis);
        $errorMessages  = false;

        if ($validated->fails()) {
            $errors = $validated->errors();
            foreach ($errors->all() as $error) {
                $errorMessages .= $error;
            }
        }

        return $errorMessages;
    }
    /**
     * @param  $user
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
     * @param  $dataArray
     * @param  $user
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
