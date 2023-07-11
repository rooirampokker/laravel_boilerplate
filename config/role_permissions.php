<?php

return [
    'user' => [
        'store',
        'index',
        'indexAll',
        'indexTrashed',
        'update',
        'delete',
        'restore',
        'show',
        'addRole',
        'removeRole',
        'syncRole',
    ],
    'tenant' => [
        'store',
        'index',
        'indexAll',
        'indexTrashed',
        'update',
        'delete',
        'restore',
        'show',
    ],
    'role' => [
        'store',
        'index',
        'show',
        'update',
        'delete',
        'addPermission',
        'revokePermission',
        'syncPermission'
    ]
];
