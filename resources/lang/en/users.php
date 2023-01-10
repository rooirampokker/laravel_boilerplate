<?php

return [
    'index' => [
        'success' => 'Users retrieved',
        'failed' => 'Unable to retrieved users'
    ],
    'store' => [
        'success' => 'User stored',
        'failed' => 'Unable to store user',
        'with_data' => [
            'success' => 'User and User Data was successfully stored',
        ],
        'only_data' => [
            'success' => 'Additional user data was successfully stored',
        ]
    ],
    'show' => [
        'success' => 'User retrieved',
        'failed' => 'Unable to retrieve the user',
    ],
    'update' => [
        'success' => 'User :id updated',
        'failed' => 'Unable to update user :id',
        'with_data' => [
            'success' => 'User and User Data was successfully updated for :id',
        ],
        'only_data' => [
            'success' => 'Additional data was successfully updated for user :id',
        ]
    ],
    'delete' => [
        'success' => 'User deleted',
        'failed' => 'Unable to delete the user'
    ],
    'restore' => [
        'success' => 'user restored',
        'failed' => 'Failed to restore user'
    ],
    'not_found' => 'User not found',
    'user_registration' => 'User Registration',
    'login' => [
        'success' => 'User logged in successfully',
        'failed' => 'Failed to log in user',
        'invalid' => 'User credentials were invalid'
    ],
    'logout' => [
        'success' => 'User logged out successfully',
        'failed' => 'Failed to log out user'
    ],
    'permissions' => [
        'success' => 'User has permission',
        'failed' => 'User does not have permission'
    ]
];
