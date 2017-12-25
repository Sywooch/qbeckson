<?php

use developeruz\db_rbac\behaviors\AccessBehavior;

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'language' => 'ru-RU',
    'sourceLanguage' => 'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'manual'],
    'layout' => 'gos',
    'defaultRoute' => 'site/index',
    'components' => require_once(__DIR__ . '/components.php'),
    'modules' => require_once(__DIR__ . '/modules.php'),
    'aliases' => require(__DIR__ . '/aliases.php'),
    'as AccessBehavior' => [
        'class' => AccessBehavior::class,
        'rules' => require_once(__DIR__ . '/permissionRules.php'),
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
        'generators' => [
            'fixture' => [
                'class' => 'elisdn\gii\fixture\Generator',
            ],
        ],
    ];
}

return $config;
