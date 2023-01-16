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
    ],
    'role' => [
        'store',
        'index',
        'show',
        'update',
        'delete',
        'addPermission',
        'revokePermission'
    ]
];
