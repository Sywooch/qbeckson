<?php

namespace app\config\urlManagerRules;

return [
    '<module:\w+>/<controller:\w+>/<action:(\w|-)+>/<id:\d+>' => '<module>/<controller>/<action>',
    '<controller:\w+>/<action:(\w|-)+>/<id:\d+>' => '<controller>/<action>',
    '/reports' => 'reports/default/index',
    '<module:\w+>/site/save-filter' => 'site/save-filter',
];
