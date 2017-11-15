<?php

use app\components\KeyStorage;
use app\components\LocalFlysystemBuilder;
use developeruz\db_rbac\behaviors\AccessBehavior;
use kartik\datecontrol\Module;
use trntv\filekit\Storage;

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'language' => 'ru-RU',
    'sourceLanguage' => 'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'manual'],
    'layout' => 'gos',
    'defaultRoute' => 'site/index',
    'components' => [
        'keyStorage' => [
            'class' => KeyStorage::class
        ],
        'yandexMapsApi' => [
            'class' => 'mirocow\yandexmaps\Api',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'BiX8pOuGw7eu1QX9cP19jptOtNg9vYA7',
            'enableCsrfValidation' => false,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'operator' => [
            'class' => 'app\components\Operator',
        ],
        'coefficient' => [
            'class' => 'app\components\Coefficient',
        ],
        'manual' => [
            'class' => 'app\components\Manual',
        ],
        'user' => [
            'identityClass' => 'app\models\UserIdentity',
            'enableAutoLogin' => true,
            'loginUrl' => ['/site/index'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.mail.ru',
                'username' => 'noreply.pfdo@mail.ru',
                'password' => 'qwerty123456',
                'port' => '465',
                'encryption' => 'ssl',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                '<module:\w+>/<controller:\w+>/<action:(\w|-)+>' => '<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:(\w|-)+>/<id:\d+>' => '<module>/<controller>/<action>',
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'php:d.m.Y',
            'datetimeFormat' => 'php:d.m.Y H:i:s',
            'timeFormat' => 'php:H:i:s',
            'nullDisplay' => '-',
            'thousandSeparator' => ' ',
            'decimalSeparator' => ',',
            'locale' => 'ru-RU',
            'currencyCode' => 'RUR',
            'numberFormatterOptions' => [
                NumberFormatter::MIN_FRACTION_DIGITS => 0,
                NumberFormatter::MAX_FRACTION_DIGITS => 0,
            ],
            'numberFormatterSymbols' => [
                NumberFormatter::CURRENCY_SYMBOL => '&#8381;',
            ],
        ],
        'fileStorage' => [
            'class' => Storage::class,
            'baseUrl' => '@web/uploads',
            'filesystem' => [
                'class' => LocalFlysystemBuilder::class,
                'path' => '@webroot/uploads'
            ],
        ],
    ],
    'modules' => [
        'permit' => [
            'class' => 'developeruz\db_rbac\Yii2DbRbac',
            'params' => [
                'userClass' => 'app\models\UserIdentity'
            ]
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module'
        ],
        'datecontrol' => [
            'class' => 'kartik\datecontrol\Module',

            'displaySettings' => [
                Module::FORMAT_DATE => 'php:d.m.Y',
                Module::FORMAT_TIME => 'hh:mm:ss a',
                Module::FORMAT_DATETIME => 'dd-MM-yyyy hh:mm:ss a',
            ],

            'saveSettings' => [
                Module::FORMAT_DATE => 'php:Y-m-d',
                Module::FORMAT_TIME => 'php:H:i:s',
                Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
            ],

            'autoWidget' => true,

            'autoWidgetSettings' => [
                Module::FORMAT_DATE => ['type' => 2, 'pluginOptions' => ['autoclose' => true]], // example
                Module::FORMAT_DATETIME => [], // setup if needed
                Module::FORMAT_TIME => [], // setup if needed
            ],

            'widgetSettings' => [
                Module::FORMAT_DATE => [
                    'class' => 'yii\jui\DatePicker', // example
                    'options' => [
                        'dateFormat' => 'php:Y-m-d',
                        'options' => ['class' => 'form-control'],
                    ]
                ]
            ]
        ]
    ],
    'aliases' => require(__DIR__ . '/aliases.php'),
    'as AccessBehavior' => [
        'class' => AccessBehavior::class,
        'rules' => [
            'mailing' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'create', 'view'],
                    'roles' => ['operators']
                ],
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
                    'organization-municipal-task'],
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
            'invoices'      => [
                [
                    'allow'   => true,
                    'actions' => ['roll-back'],
                    'roles'   => ['payers']
                ],
            ],
            'file-storage'  => [
                [
                    'allow' => true,
                ],
            ],
            'api'  => [
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
                        'actions' => ['index', 'view'],
                        'allow' => true,
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
                        'termrequest'
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
            ]
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [
            'fixture' => [
                'class' => 'elisdn\gii\fixture\Generator',
            ],
        ],
    ];
}

return $config;
