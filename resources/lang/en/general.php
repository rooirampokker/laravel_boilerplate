<?php

return [
    'failed'    => 'Unable to perform action: :message',
    'input_error' => 'There seems to be a problem with your request body. Please check and update your request',


    'record' => [
        'not_found' => 'Record :id not found',
        'not_saved' => 'Record with :id could not be saved',
        'update' => [
            'success' => 'Record :id was successfully updated',
        ],
        'create' => [
            'failed' => 'Record creation failed',
        ],
        'destroy' => [
            'success' => 'Record :id was successfully deleted',
        ],
        'restore' => [
            'success' => 'Record :id was successfully restored',
        ],
    ],
];
