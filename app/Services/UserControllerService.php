<?php
namespace App\Services;

use Validator;

class UserControllerService
{
    /**
     * @param $request
     * @param $validationType
     * @return void
     * @throws \Exception
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
        $validateThis   = ($validationType == 'store') ? $validateCreate : $validateUpdate;
        $validated      = Validator::make($request->all(), $validateThis);
        $errorMessages  = false;

        if ($validated->fails()) {
            $errors = $validated->errors();
            foreach ($errors->all() as $error) {
                $errorMessages .= $error;
            }

            throw new \Exception($errors);
        }
    }
}
