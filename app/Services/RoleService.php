<?php

namespace App\Services;

use Validator;

class RoleService
{
    /**
     * @param $request
     * @param $validationType
     * @return void
     * @throws \Exception
     */
    public function validateInput(&$request)
    {
        $validate = [
            'name'      => 'required',
        ];

        $validated      = Validator::make($request->all(), $validate);
        $errorMessages  = false;
        //password confirmation not required for storing - only validation
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
