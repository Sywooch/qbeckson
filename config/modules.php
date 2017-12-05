<?php

namespace app\config\modules;

use kartik\datecontrol\Module;

return [
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
            \kartik\datecontrol\Module::FORMAT_DATE => 'php:Y-m-d',
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
];
