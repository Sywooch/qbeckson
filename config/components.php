<?php

namespace app\config\components;

use app\components\LocalFlysystemBuilder;
use app\components\trntv\TrntvStorage;
use NumberFormatter;

return [
    'assetManager' => [
        'appendTimestamp' => true,
    ],
    'keyStorage' => [
        'class' => \app\components\KeyStorage::class
    ],
    'yandexMapsApi' => [
        'class' => 'mirocow\yandexmaps\Api',
    ],
    'MonitorAccess' => [
        'class' => 'app\components\MonitorAccess',
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
        'rules' => require_once 'urlManagerRules.php',
    ],
    'authManager' => [
        'class' => 'yii\rbac\DbManager',
    ],
    'formatter' => [
        'class' => '\app\components\AppFormatter',
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
        'class' => \trntv\filekit\Storage::class,
        'baseUrl' => '@web/file/contract?path=/uploads',
        'filesystem' => [
            'class' => LocalFlysystemBuilder::class,
            'path' => '@pfdoroot/uploads'
        ],
    ],
    'contractFileStorage' => [
        'class' => TrntvStorage::class,
        'baseUrl' => '@web/file/contract?path=/uploads',
        'filesystem' => [
            'class' => LocalFlysystemBuilder::class,
            'path' => '@pfdoroot/uploads'
        ],
    ],
];
