<?php

return [
    'manageOwnTask' => [
        'type' => 2,
        'description' => 'Manage own task',
        'ruleName' => 'isAuthor',
        'children' => [
            'manageTaskNew',
            'manageTaskInWork',
        ],
    ],
    'manageTaskNew' => [
        'type' => 2,
        'description' => 'Manage task which status is "new"',
        'ruleName' => 'isStatusNew',
        'children' => [
            'cancelOwnTask',
            'manageResponse',
        ],
    ],
    'manageTaskInWork' => [
        'type' => 2,
        'description' => 'Manage task which status is "in work"',
        'ruleName' => 'isStatusInWork',
        'children' => [
            'completeOwnTask',
        ],
    ],
    'cancelOwnTask' => [
        'type' => 2,
        'description' => 'Cancel own task',
    ],
    'manageResponse' => [
        'type' => 2,
        'description' => 'Manage response',
    ],
    'completeOwnTask' => [
        'type' => 2,
        'description' => 'Complete task',
    ],
    'actTaskNew' => [
        'type' => 2,
        'description' => 'Perform an action with task which status is "new"',
        'ruleName' => 'isStatusNew',
        'children' => [
            'respondToTask',
        ],
    ],
    'actTaskInWork' => [
        'type' => 2,
        'description' => 'Perform an action with task which status is "in work"',
        'ruleName' => 'isStatusInWork',
        'children' => [
            'declineTask',
        ],
    ],
    'respondToTask' => [
        'type' => 2,
        'description' => 'Respond to task',
        'ruleName' => 'isUniqueResponse',
    ],
    'declineTask' => [
        'type' => 2,
        'description' => 'Decline task',
        'ruleName' => 'isSelectedContractor',
    ],
    'customer' => [
        'type' => 1,
        'children' => [
            'manageOwnTask',
        ],
    ],
    'contractor' => [
        'type' => 1,
        'children' => [
            'actTaskNew',
            'actTaskInWork',
        ],
    ],
];
