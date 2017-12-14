<?php

namespace app\config\urlManagerRules;

return [
    '<module:\w+>/<controller:\w+>/<action:(\w|-)+>/<id:\d+>' => '<module>/<controller>/<action>',
    '<controller:\w+>/<action:(\w|-)+>/<id:\d+>' => '<controller>/<action>',
];
