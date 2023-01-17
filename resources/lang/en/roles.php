<?php

return [
    'index' => [
        'success' => 'Roles retrieved',
        'failed' => 'Unable to retrieved roles',
    ],
    'show' => [
        'success' => 'Role retrieved',
        'failed' => 'Unable to retrieve the role',
    ],
    'store' => [
        'success' => 'Role stored',
        'failed' => 'Unable to store role'
    ],
    'update' => [
        'success' => 'Role updated',
        'failed' => 'Unable to update role'
    ],
    'permissions' => [
        'create' => [
            'success' => 'Permission :permission_id attached to role :role_id',
            'failed' => 'Failed to attach permission :permission_id to role :role_id'
        ],
        'delete' => [
            'success' => 'Permission :permission_id removed from role :role_id',
            'failed' => 'Failed to remove permission :permission_id from role :role_id'
        ],
        'sync' => [
            'success' => "Role :role_id was successfully synced to permission(s) :permission_id",
            'failed' => "Failed to sync permission(s) :permission_id to role :role_id",
        ],
    ],
];
