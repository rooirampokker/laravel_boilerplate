<?php

namespace App\Services;

use Validator;

class UserService
{
    /**
     * @param $request
     * @param $validationType
     * @return void
     * @throws \Exception
     */
    public function validateInput(&$request, $validationType)
    {
        //use this array to validate creation of new users
        $validateCreate = [
            'email'      => 'required|email',
            'password'   => 'required',
            'c_password' => 'required|same:password',
            'roles'       => 'required',
        ];
        //use this array to validate updates
        $validateUpdate = [
            'email' => 'email'
        ];
        $validateThis   = ($validationType == 'store') ? $validateCreate : $validateUpdate;
        $validated      = Validator::make($request->all(), $validateThis);
        $errorMessages  = false;
        //password confirmation not required for storing - only validation
        $request->request->remove('c_password');
        if ($validated->fails()) {
            $errors = $validated->errors();
            foreach ($errors->all() as $error) {
                $errorMessages .= $error;
            }

            throw new \Exception($errors);
        }
    }

    /**
     * @param $input
     * @param $model
     * @return int[]|string[]
     */
    public function fillableInputCount($input, $model)
    {
        return count(array_intersect(array_keys($input), $model->getFillable()));
    }
}
