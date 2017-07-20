<?php
use app\components\LocalFlysystemBuilder;
use developeruz\db_rbac\behaviors\AccessBehavior;
use \kartik\datecontrol\Module;
use trntv\filekit\Storage;

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'language' => 'ru-RU',
    'sourceLanguage' => 'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'layout' => 'gos',
    'defaultRoute' => 'site/index',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'BiX4pOrGw2eu1QX9cP19jetOtng9vYA7',
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
        'user' => [
            'identityClass' => 'app\models\UserIdentity',
            'enableAutoLogin' => true,
            'loginUrl'=>['/site/index'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
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

    'as AccessBehavior' => [
        'class' => AccessBehavior::className(),
        'rules' =>
            [
                'personal' => [
                    [
                        'actions' => ['update-municipality'],
                        'allow' => true,
                        'roles' => ['certificate']
                    ],
                ],
                'file-storage' => [
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
                'organization' =>
                    [
                        [
                            'actions' => ['request', 'request-update', 'check-status'],
                            'allow' => true,
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
            ]
    ],

    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
