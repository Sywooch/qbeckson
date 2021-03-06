<?php

use app\components\LocalFlysystemBuilder;
use trntv\filekit\Storage;

Yii::setAlias('@tests', dirname(__DIR__) . '/tests/codeception');

// TODO на на что не влияет, лишь выдает ошибку (программы, экспорт) - убрать, когда все картинки будут сохраняться на @pfdoroot
Yii::setAlias('@webroot', dirname(__DIR__) . '/runtime');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'scriptUrl' => 'http://hmao.pfdo.ru',
        ],
        'operator' => [
            'class' => 'app\components\Operator',
        ],
        'coefficient' => [
            'class' => 'app\components\Coefficient',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['trace'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/commands.log'
                ]
            ],
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
        'fileStorage' => [
            'class' => Storage::class,
            'baseUrl' => '@runtime/uploads',
            'filesystem' => [
                'class' => LocalFlysystemBuilder::class,
                'path' => '@runtime/uploads'
            ],
        ],
        'db' => $db,
    ],
    'modules' => [
        'gridview'    => [
            'class' => '\kartik\grid\Module'
        ],
        'permit' => [
            'class' => 'developeruz\db_rbac\Yii2DbRbac',
            'params' => [
                'userClass' => 'app\models\UserIdentity'
            ]
        ],
    ],
    'aliases' => require(__DIR__ . '/aliases.php'),
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
