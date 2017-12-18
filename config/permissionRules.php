<?php

namespace app\config\permissionRules;

return [
    'module' => [
        [
            'allow' => true,
            'actions' => ['index', 'view', 'certificate-calc-normative', 'save', 'normpricesave'],
            'roles' => ['operators'],
        ],
        [
            'allow' => true,
            'actions' => ['update', 'view'],
            'roles' => ['organizations'],
        ]
    ],
    'mailing' => [
        [
            'allow' => true,
            'actions' => ['index', 'create', 'view'],
            'roles' => ['operators']
        ],
    ],
    'notification' => [
        [
            'allow' => true,
            'actions' => ['delete'],
            'roles' => ['@'],
        ]
    ],
    'personal' => [
        [
            'allow' => true,
            'actions' => ['operator-cooperates'],
            'roles' => ['operators']
        ],
        [
            'allow' => true,
            'actions' => ['operator-invoices'],
            'roles' => ['operators']
        ],
        [
            'actions' => ['update-municipality'],
            'allow' => true,
            'roles' => ['certificate']
        ],
        [
            'actions' => [
                'organization-suborder',
                'organization-set-suborder-status',
                'organization-municipal-task',
                'organization-municipal-task-contracts',
            ],
            'allow' => true,
            'roles' => ['organizations']
        ],
        [
            'actions' => [
                'payer-suborder-organizations',
                'payer-all-organizations',
                'payer-municipal-task',
                'user-personal-assign',
                'remove-user-personal-assign',
                'assigned-user-login'
            ],
            'allow' => true,
            'roles' => ['payers']
        ],
    ],
    'invoices' => [
        [
            'allow' => true,
            'actions' => ['roll-back'],
            'roles' => ['payers']
        ],
    ],
    'file-storage' => [
        [
            'allow' => true,
        ],
    ],
    'api' => [
        [
            'allow' => true,
        ],
    ],
    'site' =>
        [
            [
                'allow' => true,
            ],
        ],
    'import' =>
        [
            [
                'allow' => true,
                'roles' => ['admins'],
            ],
        ],
    'matrix' =>
        [
            [
                'allow' => true,
                'roles' => ['payer'],
            ],
        ],
    'admin/cleanup' =>
        [
            [
                'allow' => true,
                'roles' => ['admins'],
            ],
        ],
    'organization' =>
        [
            [
                'actions' => ['request', 'request-update', 'check-status'],
                'allow' => true,
            ],
            [
                'actions' => ['set-as-subordinated', 'cancel-subording', 'view-subordered'],
                'allow' => true,
                'roles' => ['payers'],
            ],
        ],
    'organization/cleanup' =>
        [
            [
                'allow' => true,
                'roles' => ['organizations'],
            ],
        ],
    'operators' =>
        [
            [
                'actions' => ['view'],
                'allow' => true,
            ],
        ],
    'programs' =>
        [
            [
                'actions' => ['index', 'view', 'view-task'],
                'allow' => true,
            ],
            [
                'actions' => ['update-task', 'refuse-task', 'decertificate'],
                'allow' => true,
                'roles' => ['payer']
            ],
            [
                'actions' => ['transfer-task', 'transfer-programme'],
                'allow' => true,
                'roles' => ['organizations']
            ],
        ],
    'municipal-task-contract' =>
        [
            [
                'actions' => ['create'],
                'allow' => true,
                'roles' => ['certificate']
            ],
            [
                'actions' => ['approve', 'view'],
                'allow' => true,
                'roles' => ['organizations']
            ],
        ],
    'user' =>
        [
            [
                'actions' => ['view'],
                'allow' => true,
            ],
        ],
    'debug/default' =>
        [
            [
                'allow' => true,
            ],
        ],
    'activity' =>
        [
            [
                'actions' => ['load-activities', 'add-activity'],
                'allow' => true,
                'roles' => ['organizations'],
            ]
        ],
    'program-module-address' => [
        [
            'actions' => ['create', 'update', 'select'],
            'allow' => true,
            'roles' => ['organizations'],
        ]
    ],
    'admin/directory-program-direction' => [
        [
            'allow' => true,
            'roles' => ['admins'],
        ]
    ],
    'admin/directory-program-activity' => [
        [
            'allow' => true,
            'roles' => ['admins'],
        ]
    ],
    'admin/search-filters' => [
        [
            'allow' => true,
            'roles' => ['admins'],
        ]
    ],
    'admin/help' => [
        [
            'allow' => true,
            'roles' => ['admins'],
        ]
    ],
    'certificates' => [
        [
            'actions' => ['group-pdf', 'password'],
            'allow' => true,
            'roles' => ['certificate'],
        ],
        [
            'actions' => ['nerf-nominal'],
            'allow' => true,
            'roles' => ['payer'],
        ]
    ],
    'site/save-filter' => [
        [
            'allow' => true,
        ]
    ],
    'maintenance' => [
        [
            'allow' => true,
        ]
    ],
    'certificate-information' => [
        [
            'allow' => true,
            'roles' => ['payer'],
        ]
    ],
    'cooperate' => [
        [
            'allow' => true,
            'actions' => ['request', 'appeal-request', 'requisites', 'reject-contract'],
            'roles' => ['organizations'],
        ],
        [
            'allow' => true,
            'actions' => [
                'confirm-request',
                'reject-request',
                'reject-contract',
                'confirm-contract',
                'requisites',
                'payment-limit'
            ],
            'roles' => ['payer'],
        ],
        [
            'allow' => true,
            'actions' => ['view', 'reject-appeal', 'confirm-appeal'],
            'roles' => ['operators'],
        ],
    ],
    'operator/key-storage' => [
        [
            'allow' => true,
            'roles' => ['operators'],
        ],
    ],
    'monitor' => [
        [
            'allow' => true,
            'roles' => ['payers'],
        ]
    ],
    'operator/operator-settings' => [
        [
            'allow' => true,
            'roles' => ['operators']
        ]
    ],
    'organization/address' => [
        [
            'allow' => true,
            'roles' => ['organizations']
        ]
    ],
    'organization/contract-settings' => [
        [
            'allow' => true,
            'actions' => ['change-settings'],
            'roles' => ['organizations'],
        ]
    ],
    'contracts' => [
        [
            'allow' => true,
            'roles' => ['certificate'],
            'actions' => [
                'request',
                'reject-request',
                'application-close-pdf',
                'application-pdf',
                'termrequest',
                'validate-request',
            ]
        ],
        [
            'allow' => true,
            'roles' => ['operators'],
            'actions' => ['create', 'request', 'reject-request']
        ],
    ],
    'guest/general' => [
        [
            'allow' => true,
            'roles' => ['?']
        ],
    ],
    'file' => [
        [
            'allow' => true,
            'roles' => ['operators', 'organizations', 'payer'],
            'actions' => ['contract'],
        ]
    ],
    'groups' => [
        [
            'allow' => true,
            'roles' => ['operators'],
            'actions' => ['contracts']
        ]
    ],
    'mun' => [
        [
            'allow' => true,
            'roles' => ['payer'],
            'actions' => ['view', 'update', 'upload'],
        ]
    ],
];
